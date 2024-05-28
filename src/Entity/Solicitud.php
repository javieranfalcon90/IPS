<?php

namespace App\Entity;

use App\Repository\SolicitudRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=SolicitudRepository::class)
 * @UniqueEntity("codigo", message= "No se pudo relizar la operación correctamente porque el valor {{ value }} del campo CÓDIGO ya está registrado.")
 * @Audit\Auditable()
 */
class Solicitud
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
    private $codigo;

    /**
     * @ORM\ManyToOne(targetEntity=Solicitante::class, inversedBy="solicitudes")
     */
    private $solicitante;

    /**
     * @ORM\OneToOne(targetEntity=Evaluacion::class, mappedBy="solicitud")
     */
    private $evaluacion;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observaciones;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $documentacion = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $estado;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $anno;

    /**
     * @ORM\ManyToOne(targetEntity=Titular::class, inversedBy="solicitudes")
     */
    private $titular;

    /**
     * @ORM\ManyToOne(targetEntity=Formafarmaceutica::class, inversedBy="solicitudes")
     */
    private $formafarmaceutica;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $noregistro;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $noips;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $periodoevaluado;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $periodoevaluadoadecuado;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $regulacionpermitida;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechainscripcionmed;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaultimarenovacion;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $indicacionesaprobadas;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecharcpvigente;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechainternacionalmed;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fortaleza;

    /**
     * @ORM\ManyToOne(targetEntity=Motivopresentacion::class, inversedBy="solicitudes")
     */
    private $motivopresentacion;

    /**
     * @ORM\ManyToMany(targetEntity=Seccionfaltante::class, inversedBy="solicitudes")
     */
    private $seccionfaltante;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $ipsanterior;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $paises;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nacionalidadtitular;

    /**
     * @ORM\ManyToMany(targetEntity=Viaadministracion::class, inversedBy="solicitudes")
     */
    private $viaadministracion;

    /**
     * @ORM\OneToOne(targetEntity=Asignacion::class, mappedBy="solicitud", cascade={"persist", "remove"})
     */
    private $asignacion;

    public function __construct()
    {
        $this->seccionfaltante = new ArrayCollection();
        $this->viaadministracion = new ArrayCollection();
    }



    public function __toString(){
        return $this->codigo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getSolicitante(): ?Solicitante
    {
        return $this->solicitante;
    }

    public function setSolicitante(?Solicitante $solicitante): self
    {
        $this->solicitante = $solicitante;

        return $this;
    }

    public function getEvaluacion(): ?Evaluacion
    {
        return $this->evaluacion;
    }

    public function setEvaluacion(?Evaluacion $evaluacion): self
    {
        // unset the owning side of the relation if necessary
        if ($evaluacion === null && $this->evaluacion !== null) {
            $this->evaluacion->setSolicitud(null);
        }

        // set the owning side of the relation if necessary
        if ($evaluacion !== null && $evaluacion->getSolicitud() !== $this) {
            $evaluacion->setSolicitud($this);
        }

        $this->evaluacion = $evaluacion;

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

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    public function getDocumentacion()
    {
        return $this->documentacion;
    }

    public function setDocumentacion(?array $documentacion): self
    {
        $this->documentacion = $documentacion;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getAnno(): ?string
    {
        return $this->anno;
    }

    public function setAnno(string $anno): self
    {
        $this->anno = $anno;

        return $this;
    }

    public function getTitular(): ?Titular
    {
        return $this->titular;
    }

    public function setTitular(?Titular $titular): self
    {
        $this->titular = $titular;

        return $this;
    }

    public function getFormafarmaceutica(): ?Formafarmaceutica
    {
        return $this->formafarmaceutica;
    }

    public function setFormafarmaceutica(?Formafarmaceutica $formafarmaceutica): self
    {
        $this->formafarmaceutica = $formafarmaceutica;

        return $this;
    }

    public function getNoregistro(): ?string
    {
        return $this->noregistro;
    }

    public function setNoregistro(?string $noregistro): self
    {
        $this->noregistro = $noregistro;

        return $this;
    }

    public function getNoips(): ?string
    {
        return $this->noips;
    }

    public function setNoips(?string $noips): self
    {
        $this->noips = $noips;

        return $this;
    }

    public function getPeriodoevaluado(): ?string
    {
        return $this->periodoevaluado;
    }

    public function setPeriodoevaluado(?string $periodoevaluado): self
    {
        $this->periodoevaluado = $periodoevaluado;

        return $this;
    }

    public function getPeriodoevaluadoadecuado(): ?string
    {
        return $this->periodoevaluadoadecuado;
    }

    public function setPeriodoevaluadoadecuado(?string $periodoevaluadoadecuado): self
    {
        $this->periodoevaluadoadecuado = $periodoevaluadoadecuado;

        return $this;
    }

    public function getRegulacionpermitida(): ?string
    {
        return $this->regulacionpermitida;
    }

    public function setRegulacionpermitida(?string $regulacionpermitida): self
    {
        $this->regulacionpermitida = $regulacionpermitida;

        return $this;
    }

    public function getFechainscripcionmed(): ?\DateTimeInterface
    {
        return $this->fechainscripcionmed;
    }

    public function setFechainscripcionmed(?\DateTimeInterface $fechainscripcionmed): self
    {
        $this->fechainscripcionmed = $fechainscripcionmed;

        return $this;
    }

    public function getFechaultimarenovacion(): ?\DateTimeInterface
    {
        return $this->fechaultimarenovacion;
    }

    public function setFechaultimarenovacion(?\DateTimeInterface $fechaultimarenovacion): self
    {
        $this->fechaultimarenovacion = $fechaultimarenovacion;

        return $this;
    }

    public function getIndicacionesaprobadas(): ?string
    {
        return $this->indicacionesaprobadas;
    }

    public function setIndicacionesaprobadas(?string $indicacionesaprobadas): self
    {
        $this->indicacionesaprobadas = $indicacionesaprobadas;

        return $this;
    }

    public function getFecharcpvigente(): ?\DateTimeInterface
    {
        return $this->fecharcpvigente;
    }

    public function setFecharcpvigente(?\DateTimeInterface $fecharcpvigente): self
    {
        $this->fecharcpvigente = $fecharcpvigente;

        return $this;
    }

    public function getFechainternacionalmed(): ?\DateTimeInterface
    {
        return $this->fechainternacionalmed;
    }

    public function setFechainternacionalmed(?\DateTimeInterface $fechainternacionalmed): self
    {
        $this->fechainternacionalmed = $fechainternacionalmed;

        return $this;
    }

    public function getFortaleza(): ?string
    {
        return $this->fortaleza;
    }

    public function setFortaleza(?string $fortaleza): self
    {
        $this->fortaleza = $fortaleza;

        return $this;
    }

    public function getMotivopresentacion(): ?Motivopresentacion
    {
        return $this->motivopresentacion;
    }

    public function setMotivopresentacion(?Motivopresentacion $motivopresentacion): self
    {
        $this->motivopresentacion = $motivopresentacion;

        return $this;
    }

    /**
     * @return Collection|Seccionfaltante[]
     */
    public function getSeccionfaltante(): Collection
    {
        return $this->seccionfaltante;
    }

    public function addSeccionfaltante(Seccionfaltante $seccionfaltante): self
    {
        if (!$this->seccionfaltante->contains($seccionfaltante)) {
            $this->seccionfaltante[] = $seccionfaltante;
        }

        return $this;
    }

    public function removeSeccionfaltante(Seccionfaltante $seccionfaltante): self
    {
        $this->seccionfaltante->removeElement($seccionfaltante);

        return $this;
    }

    public function getIpsanterior(): ?\DateTimeInterface
    {
        return $this->ipsanterior;
    }

    public function setIpsanterior(?\DateTimeInterface $ipsanterior): self
    {
        $this->ipsanterior = $ipsanterior;

        return $this;
    }

    public function getPaises(): ?string
    {
        return $this->paises;
    }

    public function setPaises(string $paises): self
    {
        $this->paises = $paises;

        return $this;
    }

    public function getNacionalidadtitular(): ?string
    {
        return $this->nacionalidadtitular;
    }

    public function setNacionalidadtitular(string $nacionalidadtitular): self
    {
        $this->nacionalidadtitular = $nacionalidadtitular;

        return $this;
    }

    /**
     * @return Collection<int, Viaadministracion>
     */
    public function getViaadministracion(): Collection
    {
        return $this->viaadministracion;
    }

    public function addViaadministracion(Viaadministracion $viaadministracion): self
    {
        if (!$this->viaadministracion->contains($viaadministracion)) {
            $this->viaadministracion[] = $viaadministracion;
        }

        return $this;
    }

    public function removeViaadministracion(Viaadministracion $viaadministracion): self
    {
        $this->viaadministracion->removeElement($viaadministracion);

        return $this;
    }

    public function getAsignacion(): ?Asignacion
    {
        return $this->asignacion;
    }

    public function setAsignacion(?Asignacion $asignacion): self
    {
        // unset the owning side of the relation if necessary
        if ($asignacion === null && $this->asignacion !== null) {
            $this->asignacion->setSolicitud(null);
        }

        // set the owning side of the relation if necessary
        if ($asignacion !== null && $asignacion->getSolicitud() !== $this) {
            $asignacion->setSolicitud($this);
        }

        $this->asignacion = $asignacion;

        return $this;
    }


}
