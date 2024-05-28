<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Form\ProductoType;
use App\Repository\ProductoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/configuracion/producto")
 */
class ProductoController extends AbstractController
{

    /**
     * @Route("/dataTable", name="producto_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio)
    {

        $token = $this->get('security.csrf.token_manager');

        $dql = 'SELECT p FROM App:Producto p';

        $columns = [
            0 => 'p.id',
            1 => 'p.nombre',
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
                        <a href="'.$this->generateUrl('producto_edit', ['id' => $res->getId()]).'">Editar</a>
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
     * @Route("/", name="producto_index", methods={"GET"}, options={"expose" : "true"})
     */
    public function index(ProductoRepository $productoRepository): Response
    {
        return $this->render('producto/index.html.twig');
    }

    /**
     * @Route("/new", name="producto_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $producto = new Producto();
        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($producto);
            $entityManager->flush();

            $this->addFlash('success', 'El elemento se ha insertado corréctamente');

            return $this->redirectToRoute('producto_index');
        } else {
            $errors = $validator->validate($producto);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('producto/new.html.twig', [
            'producto' => $producto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="producto_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Producto $producto, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'El elemento se ha editado corréctamente');

            return $this->redirectToRoute('producto_index');
        } else {
            $errors = $validator->validate($producto);

            foreach ($errors as $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('producto/new.html.twig', [
            'producto' => $producto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="producto_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Producto $producto): Response
    {
        if ($this->isCsrfTokenValid('delete'.$producto->getId(), $request->request->get('_token'))) {
            try {
                $em = $this->getDoctrine()->getManager();

                $em->remove($producto);
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
