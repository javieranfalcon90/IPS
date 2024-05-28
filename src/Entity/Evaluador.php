<?php

namespace App\Entity;

use App\Repository\EvaluadorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=EvaluadorRepository::class)
 * @UniqueEntity("nombre", message= "No se pudo relizar la operación correctamente porque el valor {{ value }} del campo NOMBRE ya está registrado.")
 * @Audit\Auditable()
 */
class Evaluador
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
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="evaluador")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firma;

    /**
     * @ORM\OneToMany(targetEntity=Asignacion::class, mappedBy="evaluador")
     */
    private $asignaciones;


    public function __toString(){
        return $this->nombre;
    }

    public function __construct()
    {
        $this->asignaciones = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setEvaluador(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getEvaluador() !== $this) {
            $user->setEvaluador($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getFirma(): ?string
    {
        return $this->firma;
    }

    public function setFirma(string $firma): self
    {
        $this->firma = $firma;

        return $this;
    }

    /**
     * @return Collection<int, Asignacion>
     */
    public function getAsignaciones(): Collection
    {
        return $this->asignaciones;
    }

    public function addAsignacione(Asignacion $asignacione): self
    {
        if (!$this->asignaciones->contains($asignacione)) {
            $this->asignaciones[] = $asignacione;
            $asignacione->setEvaluador($this);
        }

        return $this;
    }

    public function removeAsignacione(Asignacion $asignacione): self
    {
        if ($this->asignaciones->removeElement($asignacione)) {
            // set the owning side to null (unless already changed)
            if ($asignacione->getEvaluador() === $this) {
                $asignacione->setEvaluador(null);
            }
        }

        return $this;
    }

}
