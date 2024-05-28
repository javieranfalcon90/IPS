<?php

namespace App\Controller;

use App\Entity\Asignacion;
use App\Form\AsignacionType;
use App\Repository\AsignacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use App\Services\NotificacionServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/asignacion")
 */
class AsignacionController extends AbstractController
{

    /**
     * @Route("/dataTable", name="asignacion_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio)
    {

        $token = $this->get('security.csrf.token_manager');

        $parameter = [];

        $parameter['start']  = new \DateTime($request->get('anno').'-01-01');
        $parameter['end']  = new \DateTime($request->get('anno').'-12-31');

        $dql = 'SELECT a FROM App:Asignacion a LEFT JOIN a.evaluador e LEFT JOIN a.producto p where a.fecha BETWEEN :start AND :end';

        $columns = [
            0 => 'a.id',
            1 => 'a.tramite',
            2 => 'p.nombre',
            3 => 'a.fecha',
            4 => 'e.firma'
        ];

        $resultados = $dataTableServicio->datatableResult($request, $dql, $columns, $parameter);
        $count = $dataTableServicio->count($request, $dql, $columns, $parameter);
        $countAll = $dataTableServicio->countAll($dql, $parameter);

        $array = [];
        foreach ($resultados as $res) {

            $array[] = [
                '',
                $res->getTramite(),
                $res->getProducto()->getNombre(),
                $res->getFecha()->format('d-m-Y'),
                $res->getEvaluador()->getNombre(),
                ($res->getSolicitud() == null) ? 
                '<div class="text-center">
                    <div class="dropdown d-inline-block widget-dropdown">
                        <a class="dropdown-toggle fas fa-cogs text-primary" href="" role="button" id="dropdown-'.$res->getId().'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static"></a>
                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-'.$res->getId().'">
                          <li class="dropdown-item">
                            <a href="'.$this->generateUrl('asignacion_edit', ['id' => $res->getId()]).'">Editar</a>
                          </li>
                          <li class="dropdown-item">
                            <a class="confirmacion eliminar" href="javascript:;" id="'.$res->getId().'" token="'.$token->getToken('delete'.$res->getId()).'">Eliminar</a>
                          </li>
                        </ul>
                    </div>
                </div>' : '<div class="text-center"><span class="badge badge-success">Procesado</span></div>',
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
     * @Route("/", name="asignacion_index", methods={"GET"}, options={"expose" : "true"})
     */
    public function index(AsignacionRepository $asignacionRepository): Response
    {
        $anno = new \DateTime();
        return $this->render('asignacion/index.html.twig',[
            'anno' => $anno->format('Y'),
        ]);
    }

    /**
     * @Route("/new", name="asignacion_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AsignacionRepository $asignacionRepository, ValidatorInterface $validator, NotificacionServicio $notificacionServicio): Response
    {
        $asignacion = new Asignacion();
        $form = $this->createForm(AsignacionType::class, $asignacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $asignacionRepository->add($asignacion, true);

            if($request->get('notificar')) {
                $email = $asignacion->getEvaluador()->getUser()->getEmail();
                $notificacionServicio->notificarAsignacion($email, $asignacion);
            }

            $this->addFlash('success', 'El elemento se ha insertado corréctamente');

            return $this->redirectToRoute('asignacion_index');
        } else {

            $errors = $validator->validate($asignacion);
            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->renderForm('asignacion/new.html.twig', [
            'asignacion' => $asignacion,
            'notificacion' => true,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="asignacion_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Asignacion $asignacion, AsignacionRepository $asignacionRepository, ValidatorInterface $validator, NotificacionServicio $notificacionServicio): Response
    {
        $form = $this->createForm(AsignacionType::class, $asignacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $asignacionRepository->add($asignacion, true);

            if($request->get('notificar')) {
                $email = $asignacion->getEvaluador()->getUser()->getEmail();
                $notificacionServicio->notificarAsignacion($email, $asignacion);
            }

            $this->addFlash('success', 'El elemento se ha editado corréctamente');

            return $this->redirectToRoute('asignacion_index');
        } else {
            $errors = $validator->validate($asignacion);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->renderForm('asignacion/new.html.twig', [
            'asignacion' => $asignacion,
            'notificacion' => '',
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="asignacion_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Asignacion $asignacion, AsignacionRepository $asignacionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$asignacion->getId(), $request->request->get('_token'))) {
            
            try {
                $asignacionRepository->remove($asignacion, true);

                $this->addFlash('success', 'El elemento se ha eliminado corréctamente');

            } catch (\Exception $e) {
                $this->addFlash('error', 'No se pudo eliminar elemento seleccionado, ya que puede estar siendo usado');

            }

        }

        return new Response(null, '200', ['Content-Type' => 'application/json']);
    }
}
