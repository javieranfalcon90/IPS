<?php

namespace App\Entity;

use App\Repository\EvaluacionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=EvaluacionRepository::class)
 * @UniqueEntity("solicitud", message= "No se pudo relizar la operación correctamente porque el valor {{ value }} del campo SOLICITUD ya está registrado.")
 * @Audit\Auditable()
 */
class Evaluacion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Resultado::class, inversedBy="evaluaciones")
     */
    private $resultado;

    /**
     * @ORM\OneToOne(targetEntity=Solicitud::class, inversedBy="evaluacion")
     */
    private $solicitud;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $dictamen = [];

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $exposicionestimada;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $accionesdeseguridad;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $cambiosdeinformacion;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $informaciondeestudios;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $casosindividuales;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $clasificacionriesgosimportantes;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $clasificacionriesgospotenciales;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $clasificacionriesgosinformacionfaltante;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $caracterizacionderiesgos;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sennalesdeseguridad;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $evaluacionbeneficioriesgo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionderiesgo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $conclusionesdeltitular;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $recomendacion;

    /**
     * @ORM\OneToMany(targetEntity=Seguimiento::class, mappedBy="evaluacion")
     */
    private $seguimientos;

    public function __construct()
    {
        $this->seguimientos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResultado(): ?Resultado
    {
        return $this->resultado;
    }

    public function setResultado(?Resultado $resultado): self
    {
        $this->resultado = $resultado;

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

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getDictamen(): ?array
    {
        return $this->dictamen;
    }

    public function setDictamen(?array $dictamen): self
    {
        $this->dictamen = $dictamen;

        return $this;
    }

    public function getExposicionestimada(): ?string
    {
        return $this->exposicionestimada;
    }

    public function setExposicionestimada(?string $exposicionestimada): self
    {
        $this->exposicionestimada = $exposicionestimada;

        return $this;
    }

    public function getAccionesdeseguridad(): ?string
    {
        return $this->accionesdeseguridad;
    }

    public function setAccionesdeseguridad(?string $accionesdeseguridad): self
    {
        $this->accionesdeseguridad = $accionesdeseguridad;

        return $this;
    }

    public function getCambiosdeinformacion(): ?string
    {
        return $this->cambiosdeinformacion;
    }

    public function setCambiosdeinformacion(?string $cambiosdeinformacion): self
    {
        $this->cambiosdeinformacion = $cambiosdeinformacion;

        return $this;
    }

    public function getInformaciondeestudios(): ?string
    {
        return $this->informaciondeestudios;
    }

    public function setInformaciondeestudios(?string $informaciondeestudios): self
    {
        $this->informaciondeestudios = $informaciondeestudios;

        return $this;
    }

    public function getCasosindividuales(): ?string
    {
        return $this->casosindividuales;
    }

    public function setCasosindividuales(?string $casosindividuales): self
    {
        $this->casosindividuales = $casosindividuales;

        return $this;
    }

    public function getClasificacionriesgosimportantes(): ?string
    {
        return $this->clasificacionriesgosimportantes;
    }

    public function setClasificacionriesgosimportantes(?string $clasificacionriesgosimportantes): self
    {
        $this->clasificacionriesgosimportantes = $clasificacionriesgosimportantes;

        return $this;
    }

    public function getClasificacionriesgospotenciales(): ?string
    {
        return $this->clasificacionriesgospotenciales;
    }

    public function setClasificacionriesgospotenciales(?string $clasificacionriesgospotenciales): self
    {
        $this->clasificacionriesgospotenciales = $clasificacionriesgospotenciales;

        return $this;
    }

    public function getClasificacionriesgosinformacionfaltante(): ?string
    {
        return $this->clasificacionriesgosinformacionfaltante;
    }

    public function setClasificacionriesgosinformacionfaltante(?string $clasificacionriesgosinformacionfaltante): self
    {
        $this->clasificacionriesgosinformacionfaltante = $clasificacionriesgosinformacionfaltante;

        return $this;
    }

    public function getCaracterizacionderiesgos(): ?string
    {
        return $this->caracterizacionderiesgos;
    }

    public function setCaracterizacionderiesgos(?string $caracterizacionderiesgos): self
    {
        $this->caracterizacionderiesgos = $caracterizacionderiesgos;

        return $this;
    }

    public function getSennalesdeseguridad(): ?string
    {
        return $this->sennalesdeseguridad;
    }

    public function setSennalesdeseguridad(?string $sennalesdeseguridad): self
    {
        $this->sennalesdeseguridad = $sennalesdeseguridad;

        return $this;
    }

    public function getEvaluacionbeneficioriesgo(): ?string
    {
        return $this->evaluacionbeneficioriesgo;
    }

    public function setEvaluacionbeneficioriesgo(?string $evaluacionbeneficioriesgo): self
    {
        $this->evaluacionbeneficioriesgo = $evaluacionbeneficioriesgo;

        return $this;
    }

    public function getGestionderiesgo(): ?string
    {
        return $this->gestionderiesgo;
    }

    public function setGestionderiesgo(?string $gestionderiesgo): self
    {
        $this->gestionderiesgo = $gestionderiesgo;

        return $this;
    }

    public function getConclusionesdeltitular(): ?string
    {
        return $this->conclusionesdeltitular;
    }

    public function setConclusionesdeltitular(?string $conclusionesdeltitular): self
    {
        $this->conclusionesdeltitular = $conclusionesdeltitular;

        return $this;
    }

    public function getRecomendacion(): ?string
    {
        return $this->recomendacion;
    }

    public function setRecomendacion(?string $recomendacion): self
    {
        $this->recomendacion = $recomendacion;

        return $this;
    }

    /**
     * @return Collection|Seguimiento[]
     */
    public function getSeguimientos(): Collection
    {
        return $this->seguimientos;
    }

    public function addSeguimiento(Seguimiento $seguimiento): self
    {
        if (!$this->seguimientos->contains($seguimiento)) {
            $this->seguimientos[] = $seguimiento;
            $seguimiento->setEvaluacion($this);
        }

        return $this;
    }

    public function removeSeguimiento(Seguimiento $seguimiento): self
    {
        if ($this->seguimientos->removeElement($seguimiento)) {
            // set the owning side to null (unless already changed)
            if ($seguimiento->getEvaluacion() === $this) {
                $seguimiento->setEvaluacion(null);
            }
        }

        return $this;
    }
}
