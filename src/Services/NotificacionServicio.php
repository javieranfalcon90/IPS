<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificacionServicio
{

    protected $mailer;

    public function __construct(MailerInterface $mailerInterface)
    {
        $this->mailer = $mailerInterface;
    }

    public function notificarAsignacion($diremail, $asignacion){

        $email = (new Email())
            ->from('webmaster@cecmed.cu')
            ->to($diremail)
            ->subject('Notificación Sistema IPS')
            ->html('<p>Nueva asignación de IPS a gestionar</p><br>
                <p>Trámite: '.$asignacion->getTramite().'</p>
                <p>Producto: '.$asignacion->getProducto()->getNombre().'</p><br>
                <p>Puede acceder al sistema mediante este enlace: <a href="http://ips.cecmed.local/login">http://ips.cecmed.local/login</a></p>');

        $this->mailer->send($email);

    }

    public function notificarEvaluacion($diremail, $evaluacion){

        $email = (new Email())
            ->from('webmaster@cecmed.cu')
            ->to($diremail)
            ->subject('Notificación Sistema IPS')
            ->html('<p>Nueva evaluación de IPS a gestionar</p><br>
                <p>Trámite: '.$evaluacion->getSolicitud()->getAsignacion()->getTramite().'</p>
                <p>Producto: '.$evaluacion->getSolicitud()->getAsignacion()->getProducto()->getNombre().'</p>
                <p>Evaluador: '.$evaluacion->getSolicitud()->getAsignacion()->getEvaluador()->getNombre().'</p>
                <p>Resultado: '.$evaluacion->getResultado().'</p><br>
                <p>Puede acceder al sistema mediante este enlace: <a href="http://ips.cecmed.local/solicitud/'.$evaluacion->getSolicitud()->getId().'/show">
                http://ips.cecmed.local/solicitud/'.$evaluacion->getSolicitud()->getId().'/show</a></p>');

        $this->mailer->send($email);

    }

    public function notificarCierre($diremail, $solicitud){

        $email = (new Email())
            ->from('webmaster@cecmed.cu')
            ->to($diremail)
            ->subject('Notificación Sistema IPS')
            ->html('<p>Proceso de IPS cerrado</p><br>
                <p>Trámite: '.$solicitud->getAsignacion()->getTramite().'</p>
                <p>Producto: '.$solicitud->getAsignacion()->getProducto()->getNombre().'</p>
                <p>Resultado: '.$solicitud->getEvaluacion()->getResultado().'</p><br>
                <p>Puede acceder al sistema mediante este enlace: <a href="http://ips.cecmed.local/solicitud/'.$solicitud->getId().'/show">
                http://ips.cecmed.local/solicitud/'.$solicitud->getId().'/show</a></p>');

        $this->mailer->send($email);

    }

}