<?php

namespace App\Controller;

use App\Entity\Asignacion;
use App\Entity\Solicitud;
use App\Form\SolicitudType;
use App\Repository\SolicitudRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\NotificacionServicio;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @Route("/solicitud")
 */
class SolicitudController extends AbstractController
{

    /**
     * @Route("/dataTable", name="solicitud_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio)
    {

        //$token = $this->get('security.csrf.token_manager');
        $parameter = [];

        $parameter['anno'] = $request->get('anno');

        $dql = 'SELECT so FROM App:Solicitud so 
            LEFT JOIN so.asignacion a 
            LEFT JOIN a.producto p 
            LEFT JOIN so.solicitante s 
            LEFT JOIN so.viaadministracion v 
            LEFT JOIN a.evaluador e 
            WHERE so.anno = :anno ';

        if($this->getUser()->getRoles() == ['ROLE_EVALUADOR']){
            $dql .= ' AND ( e = :evaluador ) ';

            $parameter['evaluador'] = $this->getUser()->getEvaluador();
        }

        $columns = [
            0 => 'so.id',
            1 => 'so.codigo',
            2 => 'so.tramite',
            3 => 'p.nombre',
            4 => 's.nombre',
            5 => 'e.nombre',
            6 => 'so.estado'
        ];

        $resultados = $dataTableServicio->datatableResult($request, $dql, $columns, $parameter);
        $count = $dataTableServicio->count($request, $dql, $columns, $parameter);
        $countAll = $dataTableServicio->countAll($dql, $parameter);

        $array = [];
        foreach ($resultados as $res) {

            if($res->getEstado() == 'Nuevo'){
                $estado = '<span class="badge badge-primary">'.$res->getEstado().'</span>';          
            }elseif($res->getEstado() == 'No Procede'){
                $estado = '<span class="badge badge-danger">'.$res->getEstado().'</span>';
            }elseif($res->getEstado() == 'Evaluado'){
                $estado = '<span class="badge badge-info">'.$res->getEstado().'</span>';
            }else{
                $estado = '<span class="badge badge-success">'.$res->getEstado().'</span>';
            }

            $array[] = [
                '',
                '<a class="" href="'.$this->generateUrl('solicitud_show', ['id' => $res->getId()]).'" >'. $res->getCodigo() .'
                 </a>',
                ($res->getAsignacion()) ? $res->getAsignacion()->getTramite() : '-',
                ($res->getAsignacion()) ? $res->getAsignacion()->getProducto()->getNombre() : '-',
                ($res->getSolicitante()) ? $res->getSolicitante()->getNombre() : '-',
                ($res->getAsignacion()) ? $res->getAsignacion()->getEvaluador()->getNombre() : '-',
                $estado


            ];
        }

        $data = [
            'iTotalRecords' => $countAll, //consulta para el total de elementos
            'iTotalDisplayRecords' => $count, //consulta para el filtro de elementos
            'data' => $array,
        ];

        $data1 = json_encode($data);

        return new Response($data1, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/", name="solicitud_index", methods={"GET"}, options={"expose" : "true"})
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        if($this->isGranted('ROLE_EVALUADOR')){
           $evaluador = $this->getUser()->getEvaluador(); 
        }
        
        $qb = $em->getRepository(Asignacion::class)->createQueryBuilder('a')->where('a.solicitud IS null');

        if($this->isGranted('ROLE_EVALUADOR')) {
            $qb->andwhere('a.evaluador = :evaluador')->setParameter('evaluador', $evaluador);
        } 

        $asignaciones = $qb->getQuery()->getResult();

        $anno = new \DateTime();
        return $this->render('solicitud/index.html.twig',[
            'anno' => $anno->format('Y'),
            'asignaciones' => $asignaciones
        ]);
    }

    /**
     * @Route("/new", name="solicitud_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $asignacion = $entityManager->getRepository(Asignacion::class)->find($request->get('asignacion'));

        /* Chequeando los permisos */
        if($this->isGranted('ROLE_CONSULTOR')){
            $this->denyAccessUnlessGranted('No tienes los permisos necesarios para realizar esta operación');
        }
        $this->denyAccessUnlessGranted('NEW', $asignacion, 'No tienes los permisos necesarios para realizar esta operación');

        $solicitud = new Solicitud();
        $form = $this->createForm(SolicitudType::class, $solicitud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /* INICIO FUNCIONALIDAD PARA GENERAR AUTOMATICAMENTE EL CODIGO DE SOLICITUD */
            
            $startdate = new \DateTime('01-01-'.date('Y'));
            $enddate = new \DateTime('31-12-'.date('Y'));

            $result = $entityManager->getRepository(Solicitud::class)->createQueryBuilder('s')
                  ->select('s.codigo')
                  ->where('s.fecha BETWEEN :startdate AND :enddate')
                  ->setParameter('startdate', $startdate)
                  ->setParameter('enddate', $enddate)
                  ->getQuery()->getResult();

            $auto = (count($result)+1).'-'.date('y'); 
            
            $solicitud->setCodigo($auto);

            /* FIN */

            /* NO BORRAR  */
            /*$solicitud->setFecha(new \DateTime());
            $solicitud->setAnno((new \DateTime())->format('Y') );*/


            $solicitud->setAnno($solicitud->getFecha()->format('Y'));            


            $solicitud->setEstado('Nuevo');
            $solicitud->setAsignacion($asignacion);

            $entityManager->persist($solicitud);
            $entityManager->flush();

            $this->addFlash('success', 'El elemento se ha insertado corréctamente');

            return $this->redirectToRoute('solicitud_show', ['id' => $solicitud->getId()]);
        } else {

            $errors = $validator->validate($solicitud);
            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('solicitud/new.html.twig', [
            'solicitud' => $solicitud,
            'asignacion' => $asignacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="solicitud_show", methods={"GET"}, options={"expose" : "true"})
     */
    public function show(Solicitud $solicitud): Response
    {

        /* Chequeando los permisos */
        $this->denyAccessUnlessGranted('SHOW', $solicitud, 'No tienes los permisos necesarios para realizar esta operación');

        return $this->render('solicitud/show.html.twig', [
            'solicitud' => $solicitud,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="solicitud_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Solicitud $solicitud, ValidatorInterface $validator): Response
    {

        $this->denyAccessUnlessGranted('EDIT', $solicitud, 'No tienes los permisos necesarios para realizar esta operación');
            
        $form = $this->createForm(SolicitudType::class, $solicitud);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'El elemento se ha editado corréctamente');

            return $this->redirectToRoute('solicitud_show', ['id' => $solicitud->getId()]);
        } else {
            $errors = $validator->validate($solicitud);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('solicitud/new.html.twig', [
            'solicitud' => $solicitud,
            'asignacion' => $solicitud->getAsignacion(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="solicitud_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Solicitud $solicitud): Response
    {

        $this->denyAccessUnlessGranted('EDIT', $solicitud, 'No tienes los permisos necesarios para realizar esta operación');

        if ($this->isCsrfTokenValid('delete'.$solicitud->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $documentacion = $solicitud->getDocumentacion();

                $entityManager->remove($solicitud);
                $entityManager->flush();

                if($documentacion){
                    foreach ($solicitud->getDocumentacion() as $s) {
                        $p = $this->getParameter('solicitud_documentacion') . '/' . $s['newFilename'];
                        $file = new File($p);

                        if (file_exists($file->getPathname())) {
                            unlink($file->getPathname());
                        }
                    }
                }

                $this->addFlash('success', 'El elemento se ha eliminado corréctamente');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se pudo eliminar el elemento seleccionado, ya que puede estar siendo usado');
            }
        }

        return new Response(null, '200', ['Content-Type' => 'application/json']);
    }


    /**
     * @Route("/{id}/uploadfilesolicitud", name="solicitud_uploadfile", methods={"GET","POST"}, options={"expose" : "true"})
     */
    public function uploadfile(Request $request, Solicitud $solicitud): Response
    {

        $this->denyAccessUnlessGranted('EDIT', $solicitud, 'No tienes los permisos necesarios para realizar esta operación');

        /** @var UploadedFile */
        $inputFile = $request->files->get('upload');

        $arrayfile = [];
        if ($inputFile) {

            $originalFilename = pathinfo($inputFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $newFilename = $originalFilename.'-'.uniqid().'.'.$inputFile->guessExtension();
            $oName = $originalFilename.'.'.$inputFile->guessExtension();

            try {
                $inputFile->move(
                    $path = $this->getParameter('solicitud_documentacion'),
                    $newFilename
                );

                $d = $solicitud->getDocumentacion();

                $d[] = [
                    'originalFilename' => $oName,
                    'newFilename' => $newFilename,
                ];
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

        }

        $solicitud->setDocumentacion($d);
        $this->getDoctrine()->getManager()->flush();


        $p = $this->getParameter('solicitud_documentacion') . '/' . $newFilename;
        $file = new File($p);

        $arrayfile = array(
            'name' => $oName,
            'archivo' => $file->getFilename(),
            'size' => $file->getSize(),
            'uri' => $file->getPath(),
        );

        $data1 = json_encode($arrayfile);

        return new Response($data1, 200, ['Content-Type' => 'application/json']);    
    }

    /**
     * @Route("/{id}/loadfilesolicitud", name="solicitud_loadfile", methods={"GET","POST"}, options={"expose" : "true"})
     */
    public function loadfile(Request $request, Solicitud $solicitud): Response
    {

        $this->denyAccessUnlessGranted('EDIT', $solicitud, 'No tienes los permisos necesarios para realizar esta operación');

        $arrayfile = [];
        if($solicitud->getDocumentacion()){
        foreach ($solicitud->getDocumentacion() as $s) {
            $p = $this->getParameter('solicitud_documentacion') . '/' . $s['newFilename'];
            $file = new File($p);

            $arrayfile[] = array(
                'name' => $s['originalFilename'],
                'archivo' => $file->getFilename(),
                'size' => $file->getSize(),
                'uri' => $file->getPath(),
            );
        }}

        $data1 = json_encode($arrayfile);

        return new Response($data1, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/{id}/deletefilesolicitud", name="solicitud_deletefile", methods={"GET","POST"}, options={"expose" : "true"})
     */
    public function deletefile(Request $request, Solicitud $solicitud): Response
    {

        $this->denyAccessUnlessGranted('EDIT', $solicitud, 'No tienes los permisos necesarios para realizar esta operación');

        $filename = $request->get('name');
        $arr = $solicitud->getDocumentacion();

        foreach($arr as $key => $d){

            if($d['originalFilename'] == $filename){

                $p = $this->getParameter('solicitud_documentacion') . '/' . $d['newFilename'];
                $file = new File($p);

                if (file_exists($file->getPathname())) {
                    unlink($file->getPathname());
                }

                unset($arr[$key]);

                $solicitud->setDocumentacion($arr);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return new Response('', 200, ['Content-Type' => 'application/json']);
    }


    /**
     * @Route("/{id}/noprocede", name="solicitud_noprocede", methods={"POST"}, options={"expose" : "true"})
     */
    public function noprocede(Request $request, Solicitud $solicitud): Response
    {

        $this->denyAccessUnlessGranted('EDIT', $solicitud, 'No tienes los permisos necesarios para realizar esta operación');


            if($solicitud->getEstado() == "Nuevo"){

                $solicitud->setEstado('No Procede');

                $this->getDoctrine()->getManager()->flush();

            }
        return new Response(null, '200', ['Content-Type' => 'application/json']);
    }


    /**
     * @Route("/{id}/cerrar", name="solicitud_cerrar", methods={"POST"}, options={"expose" : "true"})
     */
    public function cerrar(Request $request, Solicitud $solicitud, NotificacionServicio $notificacionServicio): Response
    {

        $this->denyAccessUnlessGranted('EDIT', $solicitud, 'No tienes los permisos necesarios para realizar esta operación');

            if($solicitud->getEstado() == "Evaluado"){

                $solicitud->setEstado('Cerrado');
                $this->getDoctrine()->getManager()->flush();

                $email = $solicitud->getAsignacion()->getEvaluador()->getUser()->getEmail(); 
                $notificacionServicio->notificarCierre($email, $solicitud);

            }else if($solicitud->getEstado() == "Cerrado"){
                
                $solicitud->setEstado('Evaluado');
                $this->getDoctrine()->getManager()->flush();
            }
        return new Response(null, '200', ['Content-Type' => 'application/json']);
    }


    /**
     * @Route("/{id}/pdf", name="solicitud_pdf", methods={"GET"}, options={"expose" : "true"})
     */
    public function pdf(Request $request, Solicitud $solicitud): Response
    {

        $firmado = $request->get('firmado');
        $revisado = $request->get('revisado');

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'arial');
        $pdfOptions->setIsRemoteEnabled(true); // Necesario para cargar las fotos
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
    
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('solicitud/pdf.html.twig', [
            'solicitud' => $solicitud,
            'firmado' => $firmado,
            'revisado' => $revisado
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('letter', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("dictamen.pdf", [
            "Attachment" => true
        ]);
    }










}
