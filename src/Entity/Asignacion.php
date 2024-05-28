<?php

namespace App\Entity;

use App\Repository\AsignacionRepository;
use Doctrine\ORM\Mapping as ORM;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AsignacionRepository::class)
 * @UniqueEntity("tramite", message= "No se pudo relizar la operación correctamente porque el valor {{ value }} del campo TRÁMITE ya está registrado.")
 * @Audit\Auditable()
 */
class Asignacion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tramite;

    /**
     * @ORM\ManyToOne(targetEntity=Producto::class, inversedBy="asignaciones")
     */
    private $producto;

    /**
     * @ORM\ManyToOne(targetEntity=Evaluador::class, inversedBy="asignaciones")
     */
    private $evaluador;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\OneToOne(targetEntity=Solicitud::class, inversedBy="asignacion", cascade={"persist", "remove"})
     */
    private $solicitud;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTramite(): ?string
    {
        return $this->tramite;
    }

    public function setTramite(string $tramite): self
    {
        $this->tramite = $tramite;

        return $this;
    }

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): self
    {
        $this->producto = $producto;

        return $this;
    }

    public function getEvaluador(): ?Evaluador
    {
        return $this->evaluador;
    }

    public function setEvaluador(?Evaluador $evaluador): self
    {
        $this->evaluador = $evaluador;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getSolicitud(): ?Solicitud
    {
        return $this->solicitud;
    }

    public function setSolicitud(?Solicitud $solicitud): self
    {
        $this->solicitud = $solicitud;

        return $this;
    }
}
