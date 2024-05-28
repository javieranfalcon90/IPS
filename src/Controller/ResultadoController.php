<?php

namespace App\Controller;

use App\Entity\Resultado;
use App\Form\ResultadoType;
use App\Repository\ResultadoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/configuracion/resultado")
 */
class ResultadoController extends AbstractController
{

/**
     * @Route("/dataTable", name="resultado_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio)
    {

        $token = $this->get('security.csrf.token_manager');

        $dql = 'SELECT r FROM App:Resultado r';

        $columns = [
            0 => 'r.id',
            1 => 'r.nombre',
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
                        <a href="'.$this->generateUrl('resultado_edit', ['id' => $res->getId()]).'">Editar</a>
                      </li>
                      <li class="dropdown-item">
                        <a class="confirmacion eliminar" href="#" id="'.$res->getId().'" token="'.$token->getToken('delete'.$res->getId()).'">Eliminar</a>
                      </li>
                    </ul>
                  </div>
                  </div>'
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
     * @Route("/", name="resultado_index", methods={"GET"}, options={"expose" : "true"})
     */
    public function index(ResultadoRepository $resultadoRepository): Response
    {
        return $this->render('resultado/index.html.twig');
    }

    /**
     * @Route("/new", name="resultado_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $resultado = new Resultado();
        $form = $this->createForm(ResultadoType::class, $resultado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($resultado);
            $entityManager->flush();

            $this->addFlash('success', 'El elemento se ha insertado corréctamente');

            return $this->redirectToRoute('resultado_index');
        } else {
            $errors = $validator->validate($resultado);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('resultado/new.html.twig', [
            'resultado' => $resultado,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="resultado_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Resultado $resultado, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(ResultadoType::class, $resultado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'El elemento se ha editado corréctamente');

            return $this->redirectToRoute('resultado_index');
        } else {
            $errors = $validator->validate($resultado);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('resultado/edit.html.twig', [
            'resultado' => $resultado,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resultado_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Resultado $resultado): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resultado->getId(), $request->request->get('_token')))
        {
            try {
                $em = $this->getDoctrine()->getManager();

                $em->remove($resultado);
                $em->flush();

                $this->addFlash(
                    'success',
                    'El elemento se ha eliminado corréctamente'
                );

            } catch (\Exception $e) {
                $this->addFlash(
                    'error',
                    'No se pudo eliminar elemento seleccionado, ya que puede estar siendo usado'
                );

            }
        }

        return new Response(null, '200', ['Content-Type' => 'application/json']);
    }
}
