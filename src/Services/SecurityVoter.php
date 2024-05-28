<?php
/**
 * Created by PhpStorm.
 * User: jfalcon
 * Date: 15/06/2018
 * Time: 11:40
 */

namespace App\Services;

use App\Entity\Asignacion;
use App\Entity\Solicitud;
use App\Entity\Evaluacion;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\User as U;

class SecurityVoter extends Voter
{

    private $em;
    private $token;

    const NEW = 'NEW';
    const SHOW = 'SHOW';
    const EDIT = 'EDIT';

    public function __construct($token, $entityManager)
    {
        $this->token = $token;
        $this->em = $entityManager;
    }

    protected function supports($attribute, $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::NEW, self::SHOW, self::EDIT])) {
            return false;
        }
            return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();

        /* El usuario tiene q estar logueado */
        if (!$user instanceof U && !$user instanceof User) {
            return false;
        }

        /* En caso q sea SuperAdministrador tiene permiso para todo */
        if (in_array('ROLE_ADMINISTRADOR', $user->getRoles())) {
            return true;
        }

        /* En caso de que sea otro usuario se rectifica si tiene o no permisos */
        $usuario = $this->em->getRepository(User::class)->find($user);

        $x = $this->accesoEntidades($attribute, $subject, $usuario);

        if($x === true){
            return true;
        }else{
            return false;
        }

    }

    public function accesoEntidades($attribute, $subject, $usuario){

        if(is_object($subject)){
            if($subject instanceof Asignacion) {

                switch ($attribute) {                    
                    case self::NEW:
                        // Si es SUPERVISOR o EVALUADOR y la asignacion fue asignada a él  
                        if( (in_array('ROLE_SUPERVISOR', $usuario->getRoles()) || in_array('ROLE_EVALUADOR', $usuario->getRoles())) && ($usuario->getEvaluador() == $subject->getEvaluador()) ){
                            return true;
                        }

                        return false;
                }

                switch ($attribute) {                    
                    case self::EDIT:
                        // Si es SUPERVISOR y no tiene solicitud 
                        if( in_array('ROLE_SUPERVISOR', $usuario->getRoles()) && $subject->getSolicitud() == null ){
                            return true;
                        }

                        return false;
                }

            }

            if($subject instanceof Solicitud) {

                switch ($attribute) {
                    case self::SHOW:
                        // Solo el CONSULTOR o SUPERVISOR pueden ver
                        if( in_array('ROLE_CONSULTOR', $usuario->getRoles()) || in_array('ROLE_SUPERVISOR', $usuario->getRoles()) ){
                            return true;
                        }

                        // Si es EVALUADOR y las solicitud fue asignada a él
                        if(($usuario->getEvaluador() == $subject->getAsignacion()->getEvaluador())){
                            return true;
                        }
                    
                        return false;
                        
                    case self::EDIT:
                        // Si es SUPERVISOR y se encuentra en estado distinto de Cerrado puede modificar 
                        if( in_array('ROLE_SUPERVISOR', $usuario->getRoles()) && $subject->getEstado() != 'Cerrado'){
                            return true;
                        }

                        // Si es EVALUADOR, las solicitud fue asignada a él y se encuentra en estado distinto de Cerrado puede modificar
                        if(in_array('ROLE_EVALUADOR', $usuario->getRoles()) && ($usuario->getEvaluador() == $subject->getAsignacion()->getEvaluador()) && $subject->getEstado() != 'Cerrado'){
                            return true;
                        }

                        return false;
                }

            }

            if($subject instanceof Evaluacion) {

                switch ($attribute) {
                    case self::EDIT:
                        if( in_array('ROLE_SUPERVISOR', $usuario->getRoles()) && $subject->getEstado() != 'Cerrado' ){
                            return true;
                        }
                        if( ($usuario->getEvaluador() != $subject->getSolicitud()->getAsignacion()->getEvaluador()) || $subject->getSolicitud()->getEstado() == 'Evaluado'){
                            return false;
                        }else{
                            return true;
                        }
                }

            }

        }
            return true;

    }

}