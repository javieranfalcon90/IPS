<?php

namespace App\Entity;

use App\Repository\ResultadoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ResultadoRepository::class)
 * @UniqueEntity("nombre", message= "No se pudo relizar la operación correctamente porque el valor {{ value }} del campo NOMBRE ya está registrado.")
 * @Audit\Auditable()
 */
class Resultado
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
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity=Evaluacion::class, mappedBy="resultado")
     */
    private $evaluaciones;

    public function __toString(){
        return $this->nombre;
    }

    public function __construct()
    {
        $this->evaluaciones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @return Collection|Evaluacion[]
     */
    public function getEvaluaciones(): Collection
    {
        return $this->evaluaciones;
    }

    public function addEvaluacione(Evaluacion $evaluacione): self
    {
        if (!$this->evaluaciones->contains($evaluacione)) {
            $this->evaluaciones[] = $evaluacione;
            $evaluacione->setResultado($this);
        }

        return $this;
    }

    public function removeEvaluacione(Evaluacion $evaluacione): self
    {
        if ($this->evaluaciones->removeElement($evaluacione)) {
            // set the owning side to null (unless already changed)
            if ($evaluacione->getResultado() === $this) {
                $evaluacione->setResultado(null);
            }
        }

        return $this;
    }
}
