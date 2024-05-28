<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/seguridad/user")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/dataTable", name="user_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio)
    {

        $token = $this->get('security.csrf.token_manager');

        $dql = 'SELECT u FROM App:User u LEFT JOIN u.evaluador e ';

        $columns = [
            0 => 'u.id',
            1 => 'u.username',
            2 => 'u.email',
            3 => 'u.estado',
            4 => 'u.roles',
            5 => 'e.nombre'
        ];

        $resultados = $dataTableServicio->datatableResult($request, $dql, $columns);
        $count = $dataTableServicio->count($request, $dql, $columns);
        $countAll = $dataTableServicio->countAll($dql);

        $array = [];
        foreach ($resultados as $res) {
            $array[] = [
                '',
                $res->getUsername(),
                $res->getEmail(),
                ($res->getEstado() == true) ? '<span class="badge badge-info">Activo</span>' : '<span class="badge badge-dark">Inactivo</span>',
                $res->getRoles(),
                ($res->getEvaluador()) ? $res->getEvaluador()->getNombre() : '',
                '<div class="dropdown d-inline-block widget-dropdown">
                    <a class="dropdown-toggle fas fa-cogs text-primary" href="" role="button" id="dropdown-recent-order1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static"></a>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-recent-order1">
                      <li class="dropdown-item">
                        <a href="'.$this->generateUrl('user_edit', ['id' => $res->getId()]).'">Editar</a>
                      </li>
                      <li class="dropdown-item">
                        <a class="confirmacion eliminar" href="#" id="'.$res->getId().'" token="'.$token->getToken('delete'.$res->getId()).'">Eliminar</a>
                      </li>
                    </ul>
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
     * @Route("/", name="user_index", methods={"GET"}, options={"expose" : "true"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig');
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator, UserPasswordHasherInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $passwordCodificado = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($passwordCodificado);
            $user->setEstado(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'El elemento se ha insertado corréctamente');

            return $this->redirectToRoute('user_index');
        } else {

            $errors = $validator->validate($user);

            foreach ($errors as $e){
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, ValidatorInterface $validator, UserPasswordHasherInterface $encoder): Response
    {

                $password_actual = $user->getPassword();
                
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($user->getPassword() == null) {
                $user->setPassword($password_actual);
            } else {
                $passwordCodificado = $encoder->hashPassword($user, $user->getPassword());
                $user->setPassword($passwordCodificado);
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'El elemento se ha editado corréctamente');

            return $this->redirectToRoute('user_index');
        } else {

            $errors = $validator->validate($user);

            foreach ($errors as $e){
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            try {
                $em = $this->getDoctrine()->getManager();

                $em->remove($user);
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
