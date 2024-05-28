<?php

namespace App\Controller;

use App\Entity\Viaadministracion;
use App\Form\ViaadministracionType;
use App\Repository\ViaadministracionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/configuracion/viaadministracion")
 */
class ViaadministracionController extends AbstractController
{

    /**
     * @Route("/dataTable", name="viaadministracion_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio)
    {

        $token = $this->get('security.csrf.token_manager');

        $dql = 'SELECT e FROM App:Viaadministracion e';

        $columns = [
            0 => 'e.id',
            1 => 'e.nombre'
        ];

        $resultados = $dataTableServicio->datatableResult($request, $dql, $columns);
        $count = $dataTableServicio->count($request, $dql, $columns);
        $countAll = $dataTableServicio->countAll($dql);

        $array = [];
        foreach ($resultados as $res) {
            $array[] = [
                '',
                $res->getNombre(),
                '<div class="text-center">
                <div class="dropdown d-inline-block widget-dropdown">
                    <a class="dropdown-toggle fas fa-cogs text-primary" href="" role="button" id="dropdown-'.$res->getId().'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static"></a>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-'.$res->getId().'">
                      <li class="dropdown-item">
                        <a href="'.$this->generateUrl('viaadministracion_edit', ['id' => $res->getId()]).'">Editar</a>
                      </li>
                      <li class="dropdown-item">
                        <a class="confirmacion eliminar" href="#" id="'.$res->getId().'" token="'.$token->getToken('delete'.$res->getId()).'">Eliminar</a>
                      </li>
                    </ul>
                  </div>
                  </div>',
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
     * @Route("/", name="viaadministracion_index", methods={"GET"}, options={"expose" : "true"})
     */
    public function index(ViaadministracionRepository $viaadministracionRepository): Response
    {
        return $this->render('viaadministracion/index.html.twig');
    }

    /**
     * @Route("/new", name="viaadministracion_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $viaadministracion = new Viaadministracion();
        $form = $this->createForm(ViaadministracionType::class, $viaadministracion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($viaadministracion);
            $entityManager->flush();

            $this->addFlash('success', 'El elemento se ha insertado corréctamente');

            return $this->redirectToRoute('viaadministracion_index');
        } else {
            $errors = $validator->validate($viaadministracion);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('viaadministracion/new.html.twig', [
            'viaadministracion' => $viaadministracion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="viaadministracion_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Viaadministracion $viaadministracion, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(ViaadministracionType::class, $viaadministracion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'El elemento se ha editado corréctamente');

            return $this->redirectToRoute('viaadministracion_index');
        } else {
            $errors = $validator->validate($viaadministracion);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('viaadministracion/new.html.twig', [
            'viaadministracion' => $viaadministracion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="viaadministracion_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Viaadministracion $viaadministracion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$viaadministracion->getId(), $request->request->get('_token'))) {
            try {
                $em = $this->getDoctrine()->getManager();

                $em->remove($viaadministracion);
                $em->flush();

                $this->addFlash('success', 'El elemento se ha eliminado corréctamente');

            } catch (\Exception $e) {
                $this->addFlash('error', 'No se pudo eliminar elemento seleccionado, ya que puede estar siendo usado');

            }
        }

        return new Response(null, '200', ['Content-Type' => 'application/json']);
    }
}
