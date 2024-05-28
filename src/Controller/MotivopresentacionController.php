<?php

namespace App\Controller;

use App\Entity\Motivopresentacion;
use App\Form\MotivopresentacionType;
use App\Repository\MotivopresentacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/configuracion/motivopresentacion")
 */
class MotivopresentacionController extends AbstractController
{

    /**
     * @Route("/dataTable", name="motivopresentacion_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio)
    {

        $token = $this->get('security.csrf.token_manager');

        $dql = 'SELECT e FROM App:Motivopresentacion e';

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
                        <a href="'.$this->generateUrl('motivopresentacion_edit', ['id' => $res->getId()]).'">Editar</a>
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
     * @Route("/", name="motivopresentacion_index", methods={"GET"}, options={"expose" : "true"})
     */
    public function index(MotivopresentacionRepository $motivopresentacionRepository): Response
    {
        return $this->render('motivopresentacion/index.html.twig');
    }

    /**
     * @Route("/new", name="motivopresentacion_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $motivopresentacion = new Motivopresentacion();
        $form = $this->createForm(MotivopresentacionType::class, $motivopresentacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($motivopresentacion);
            $entityManager->flush();

            $this->addFlash('success', 'El elemento se ha insertado corréctamente');

            return $this->redirectToRoute('motivopresentacion_index');
        } else {
            $errors = $validator->validate($motivopresentacion);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('motivopresentacion/new.html.twig', [
            'motivopresentacion' => $motivopresentacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="motivopresentacion_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Motivopresentacion $motivopresentacion, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(MotivopresentacionType::class, $motivopresentacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'El elemento se ha editado corréctamente');

            return $this->redirectToRoute('motivopresentacion_index');
        } else {
            $errors = $validator->validate($motivopresentacion);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('motivopresentacion/new.html.twig', [
            'motivopresentacion' => $motivopresentacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="motivopresentacion_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Motivopresentacion $motivopresentacion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$motivopresentacion->getId(), $request->request->get('_token'))) {
            try {
                $em = $this->getDoctrine()->getManager();

                $em->remove($motivopresentacion);
                $em->flush();

                $this->addFlash('success', 'El elemento se ha eliminado corréctamente');

            } catch (\Exception $e) {
                $this->addFlash('error', 'No se pudo eliminar elemento seleccionado, ya que puede estar siendo usado');

            }
        }

        return new Response(null, '200', ['Content-Type' => 'application/json']);
    }
}
