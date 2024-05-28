<?php

namespace App\Controller;

use App\Entity\Evaluacion;
use App\Form\EvaluacionType;
use App\Repository\EvaluacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Solicitud;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\NotificacionServicio;


/**
 * @Route("/evaluacion")
 */
class EvaluacionController extends AbstractController
{

    /**
     * @Route("/new/{solicitud}", name="evaluacion_new", methods={"GET","POST"})
     */
    public function new(Request $request, Solicitud $solicitud, ValidatorInterface $validator, NotificacionServicio $notificacionServicio): Response
    {

        $evaluacion = new Evaluacion();

        $evaluacion->setSolicitud($solicitud);

        $form = $this->createForm(EvaluacionType::class, $evaluacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $evaluacion->getSolicitud()->setEstado('Evaluado');

            /* NO BORRAR */
            /*$evaluacion->setFecha(new \DateTime());*/

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evaluacion);
            $entityManager->flush();


            if($request->get('notificar')) {
                $email = 'ismary@cecmed.cu';
                $notificacionServicio->notificarEvaluacion($email, $evaluacion);
            }


            $this->addFlash('success', 'El elemento se ha insertado corréctamente');

            return $this->redirectToRoute('solicitud_show', ['id' => $solicitud->getId() ]);
        } else {
            $errors = $validator->validate($evaluacion);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('evaluacion/new.html.twig', [
            'solicitud' => $solicitud,
            'evaluacion' => $evaluacion,
            'notificacion' => true,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="evaluacion_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Evaluacion $evaluacion, ValidatorInterface $validator, NotificacionServicio $notificacionServicio): Response
    {
        $form = $this->createForm(EvaluacionType::class, $evaluacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            if($request->get('notificar')) {
                $email = 'ismary@cecmed.cu';
                $notificacionServicio->notificarEvaluacion($email, $evaluacion);
            }

            $this->addFlash('success', 'El elemento se ha editado corréctamente');

            return $this->redirectToRoute('solicitud_show', ['id' => $evaluacion->getSolicitud()->getId()]);
        } else {
            $errors = $validator->validate($evaluacion);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('evaluacion/new.html.twig', [
            'solicitud' => $evaluacion->getSolicitud(),
            'evaluacion' => $evaluacion,
            'notificacion' => false,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="evaluacion_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Evaluacion $evaluacion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evaluacion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

                try{
                    $dictamen = $evaluacion->getDictamen();

                    $evaluacion->getSolicitud()->setEstado('Nuevo');

                    $entityManager->remove($evaluacion);
                    $entityManager->flush();

                    if($dictamen){
                        foreach ($solicitud->getDictamen() as $s) {
                            $p = $this->getParameter('evaluacion_dictamen') . '/' . $s;
                            $file = new File($p);

                            if (file_exists($file->getPathname())) {
                                unlink($file->getPathname());
                            }
                        }
                    }

                    $this->addFlash('success', 'El elemento se ha eliminado corréctamente');
                }catch(\Exception $e){
                    $this->addFlash(
                        'error',
                        'No se pudo eliminar elemento seleccionado, ya que puede estar siendo usado'
                    );
                }


            
        }

        return new Response(null, '200', ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/{id}/uploadfileevaluacion", name="evaluacion_uploadfile", methods={"GET","POST"}, options={"expose" : "true"})
     */
    public function uploadfile(Request $request, Evaluacion $evaluacion): Response
    {

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
                    $path = $this->getParameter('evaluacion_dictamen'),
                    $newFilename
                );

                $d = $evaluacion->getDictamen();

                $d[] = [
                    'originalFilename' => $oName,
                    'newFilename' => $newFilename,
                ];
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

        }

        $evaluacion->setDictamen($d);
        $this->getDoctrine()->getManager()->flush();


        $p = $this->getParameter('evaluacion_dictamen') . '/' . $newFilename;
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
     * @Route("/{id}/loadfileevaluacion", name="evaluacion_loadfile", methods={"GET","POST"}, options={"expose" : "true"})
     */
    public function loadfile(Request $request, Evaluacion $evaluacion): Response
    {

        $arrayfile = [];
        if($evaluacion->getDictamen()){
        foreach ($evaluacion->getDictamen() as $s) {
            $p = $this->getParameter('evaluacion_dictamen') . '/' . $s['newFilename'];
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
     * @Route("/{id}/deletefileevaluacion", name="evaluacion_deletefile", methods={"GET","POST"}, options={"expose" : "true"})
     */
    public function deletefile(Request $request, Evaluacion $evaluacion): Response
    {

        $filename = $request->get('name');
        $arr = $evaluacion->getDictamen();

        foreach($arr as $key => $d){

            if($d['originalFilename'] == $filename){

                $p = $this->getParameter('evaluacion_dictamen') . '/' . $d['newFilename'];
                $file = new File($p);

                if (file_exists($file->getPathname())) {
                    unlink($file->getPathname());
                }

                unset($arr[$key]);

                $evaluacion->setDictamen($arr);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return new Response('', 200, ['Content-Type' => 'application/json']);
    }
}
