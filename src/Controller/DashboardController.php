<?php

namespace App\Controller;

use App\Entity\Evaluacion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Asignacion;
use App\Entity\Solicitud;
use App\Entity\Resultado;
use App\Entity\Evaluador;
use App\Entity\Solicitante;
use App\Entity\Fabricante;

use Doctrine\Persistence\ManagerRegistry;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index(ManagerRegistry $doctrine): Response
    {

    	$anno = new \DateTime();

        $cant_x_evaluadores = $this->cantidad_x_evaluador($doctrine);
        $cant_x_solicitantes = $this->cantidad_x_solicitante($doctrine);


        return $this->render('default/dashboard.html.twig', [
            'controller_name' => 'DashboardController',
            'anno' => $anno->format('Y'),

            'cant_x_evaluadores' => $cant_x_evaluadores,
            'cant_x_solicitantes' => $cant_x_solicitantes

        ]);
    }

    /**
     * @Route("/cantidad_anno", name="cantidad_anno", methods={"GET"}, options={"expose" : "true"})
     */
    public function cantidad_anno(Request $request, ManagerRegistry $doctrine): Response
    {

		$em = $doctrine->getManager();

		$anno = $request->get('anno');

		$entitys = $em->getRepository(Solicitud::class)->findBy(['anno' => $anno]);
		$array_cant = [0,0,0,0,0,0,0,0,0,0,0,0];

        for($i=1; $i<=12; $i++){
            $cont = 0;

		    foreach ($entitys as $e) {

				if($e->getFecha()->format('n') == $i){
					$cont++;
				}
				$array_cant[$i-1] = $cont;
			}

		}

        $data1 = json_encode($array_cant);

        return new Response($data1, 200, ['Content-Type' => 'application/json']);
    }


    /**
     * @Route("/cantidad_x_resultado", name="cantidad_x_resultado", methods={"GET"}, options={"expose" : "true"})
     */
    public function cantidad_x_resultado(Request $request, ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();


        $resultados = $em->getRepository(Resultado::class)->findAll();

        $array_resultados = [];
        $array_cant = [];
        foreach ($resultados as $r){
            $entitys = $em->getRepository(Evaluacion::class)->findBy(['resultado' => $r]);

            $array_resultados[] = $r->getNombre();
            $array_cant[] = count($entitys);
        }

        $data = [
            'resultados' => $array_resultados,
            'cant' => $array_cant

        ];
        $data1 = json_encode($data);

        return new Response($data1, 200, ['Content-Type' => 'application/json']);
    }


    /**
     * @Route("/cantidad_x_estado", name="cantidad_x_estado", methods={"GET"}, options={"expose" : "true"})
     */
    public function cantidad_x_estado(Request $request, ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();


        $estados = ['Nuevo', 'Evaluado', 'No Procede'];

        $array_cant = [];
        foreach ($estados as $e){
            $entitys = $em->getRepository(Solicitud::class)->findBy(['estado' => $e]);

            $array_cant[] = count($entitys);
        }

        $data = [
            'estados' => $estados,
            'cant' => $array_cant

        ];
        $data1 = json_encode($data);

        return new Response($data1, 200, ['Content-Type' => 'application/json']);
    }


    public function cantidad_x_evaluador(ManagerRegistry $doctrine){

        $em = $doctrine->getManager();

        $evaluadores = $em->getRepository(Evaluador::class)->findAll();

        $array_evaluadores = [];
        $array_total = [];
        $array_pendientes = [];
        $array_evaluadas = [];
        foreach ($evaluadores as $e){

            $array_evaluadores[] = $e->getNombre();

            $entitys = $em->getRepository(Solicitud::class)->createQueryBuilder('s')
                ->join('s.asignacion', 'a')
                ->where('a.evaluador = :evaluador')
                ->setParameter('evaluador', $e)
                ->getQuery()->getResult();


            $eval = $em->getRepository(Solicitud::class)->createQueryBuilder('s')
                ->join('s.evaluacion', 'e')
                ->join('s.asignacion', 'a')
                ->where('a.evaluador = :evaluador')
                ->setParameter('evaluador', $e)
                ->getQuery()->getResult();

            $array_total[] = $total = count($entitys);
            $array_evaluadas[] = $evaluadas = count($eval);
            $array_pendientes[] = $total - $evaluadas;

        }

        return [$array_evaluadores, $array_total, $array_evaluadas, $array_pendientes];
    }

    public function cantidad_x_solicitante(ManagerRegistry $doctrine){

        $em = $doctrine->getManager();

        $solicitantes = $em->getRepository(Solicitante::class)->findAll();

        $array_solicitantes = [];
        $array_total = [];
        $array_pendientes = [];
        $array_evaluadas = [];
        foreach ($solicitantes as $s){

            $array_solicitantes[] = $s->getNombre();

            $entitys = $em->getRepository(Solicitud::class)->findBy(['solicitante' => $s]);

            $qb = $em->getRepository(Solicitud::class)->createQueryBuilder('s')
                ->join('s.evaluacion', 'e')->where('s.solicitante = :solicitante')->setParameter('solicitante', $s)->getQuery();

            $eval = $qb->getResult();

            $array_total[] = $total = count($entitys);
            $array_evaluadas[] = $evaluadas = count($eval);
            $array_pendientes[] = $total - $evaluadas;
        }


        return [$array_solicitantes, $array_total, $array_evaluadas, $array_pendientes];

    }


}
