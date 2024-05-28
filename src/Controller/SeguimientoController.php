<?php

namespace App\Controller;

use App\Entity\Seguimiento;
use App\Form\SeguimientoType;
use App\Repository\SeguimientoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Evaluacion;

/**
 * @Route("/seguimiento")
 */
class SeguimientoController extends AbstractController
{

    /**
     * @Route("/new", name="seguimiento_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $evaluacion_id = $request->get('evaluacion');
        $evaluacion = $entityManager->getRepository(Evaluacion::class)->find($evaluacion_id);

        $seguimiento = new Seguimiento();
        $form = $this->createForm(SeguimientoType::class, $seguimiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $seguimiento->setEvaluacion($evaluacion);
            $seguimiento->setUser($this->getUser()->getUsername());

            $entityManager->persist($seguimiento);
            $entityManager->flush();

            return $this->redirectToRoute('solicitud_show', ['id' => $evaluacion->getSolicitud()->getId() ]);
        }

        return $this->render('seguimiento/new.html.twig', [
            'evaluacion' => $evaluacion,
            'seguimiento' => $seguimiento,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="seguimiento_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Seguimiento $seguimiento): Response
    {

        $evaluacion = $seguimiento->getEvaluacion();

        $form = $this->createForm(SeguimientoType::class, $seguimiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $seguimiento->setUser($this->getUser()->getUsername());

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('solicitud_show', ['id' => $seguimiento->getEvaluacion()->getSolicitud()->getId() ]);
        }

        return $this->render('seguimiento/new.html.twig', [
            'evaluacion' => $evaluacion,
            'seguimiento' => $seguimiento,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="seguimiento_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Seguimiento $seguimiento): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seguimiento->getId(), $request->request->get('_token'))) {
            try {
                $em = $this->getDoctrine()->getManager();

                $em->remove($seguimiento);
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
