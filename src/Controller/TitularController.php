<?php

namespace App\Controller;

use App\Entity\Titular;
use App\Form\TitularType;
use App\Repository\TitularRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/configuracion/titular")
 */
class TitularController extends AbstractController
{

        /**
     * @Route("/dataTable", name="titular_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio)
    {

        $token = $this->get('security.csrf.token_manager');

        $dql = 'SELECT t FROM App:Titular t';

        $columns = [
            0 => 't.id',
            1 => 't.nombre',
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
                        <a href="'.$this->generateUrl('titular_edit', ['id' => $res->getId()]).'">Editar</a>
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
     * @Route("/", name="titular_index", methods={"GET"}, options={"expose" : "true"})
     */
    public function index(TitularRepository $titularRepository): Response
    {
        return $this->render('titular/index.html.twig');
    }

    /**
     * @Route("/new", name="titular_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $titular = new Titular();
        $form = $this->createForm(TitularType::class, $titular);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($titular);
            $entityManager->flush();

            $this->addFlash('success', 'El elemento se ha insertado corréctamente');

            return $this->redirectToRoute('titular_index');
        } else {
            $errors = $validator->validate($titular);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('titular/new.html.twig', [
            'titular' => $titular,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="titular_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Titular $titular, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(TitularType::class, $titular);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'El elemento se ha editado corréctamente');

            return $this->redirectToRoute('titular_index');
        } else {
            $errors = $validator->validate($titular);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('titular/new.html.twig', [
            'titular' => $titular,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="titular_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Titular $titular): Response
    {
        if ($this->isCsrfTokenValid('delete'.$titular->getId(), $request->request->get('_token'))) {
            try {
                $em = $this->getDoctrine()->getManager();

                $em->remove($titular);
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
