<?php

namespace App\Controller;

use App\Entity\Formafarmaceutica;
use App\Form\FormafarmaceuticaType;
use App\Repository\FormafarmaceuticaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/configuracion/formafarmaceutica")
 */
class FormafarmaceuticaController extends AbstractController
{

    /**
     * @Route("/dataTable", name="formafarmaceutica_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio)
    {

        $token = $this->get('security.csrf.token_manager');

        $dql = 'SELECT ff FROM App:Formafarmaceutica ff';

        $columns = [
            0 => 'ff.id',
            1 => 'ff.nombre',
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
                        <a href="'.$this->generateUrl('formafarmaceutica_edit', ['id' => $res->getId()]).'">Editar</a>
                      </li>
                      <li class="dropdown-item">
                        <a class="confirmacion eliminar" href="javascripts:;" id="'.$res->getId().'" token="'.$token->getToken('delete'.$res->getId()).'">Eliminar</a>
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
     * @Route("/", name="formafarmaceutica_index", methods={"GET"}, options={"expose" : "true"})
     */
    public function index(FormafarmaceuticaRepository $formafarmaceuticaRepository): Response
    {
        return $this->render('formafarmaceutica/index.html.twig');
    }

    /**
     * @Route("/new", name="formafarmaceutica_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $formafarmaceutica = new Formafarmaceutica();
        $form = $this->createForm(FormafarmaceuticaType::class, $formafarmaceutica);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formafarmaceutica);
            $entityManager->flush();

            $this->addFlash('success', 'El elemento se ha insertado corréctamente');

            return $this->redirectToRoute('formafarmaceutica_index');
        } else {
            $errors = $validator->validate($formafarmaceutica);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('formafarmaceutica/new.html.twig', [
            'formafarmaceutica' => $formafarmaceutica,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="formafarmaceutica_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Formafarmaceutica $formafarmaceutica, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(FormafarmaceuticaType::class, $formafarmaceutica);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'El elemento se ha editado corréctamente');

            return $this->redirectToRoute('formafarmaceutica_index');
        } else {
            $errors = $validator->validate($formafarmaceutica);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('formafarmaceutica/new.html.twig', [
            'formafarmaceutica' => $formafarmaceutica,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="formafarmaceutica_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Formafarmaceutica $formafarmaceutica): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formafarmaceutica->getId(), $request->request->get('_token')))
        {
            try {
                $em = $this->getDoctrine()->getManager();

                $em->remove($fabricante);
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
