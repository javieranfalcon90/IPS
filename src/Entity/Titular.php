<?php

namespace App\Entity;

use App\Repository\TitularRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=TitularRepository::class)
 * @UniqueEntity("nombre", message= "No se pudo relizar la operación correctamente porque el valor {{ value }} del campo NOMBRE ya está registrado.")
 * @Audit\Auditable()
 */
class Titular
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
     * @ORM\OneToMany(targetEntity=Solicitud::class, mappedBy="titular")
     */
    private $solicitudes;

    public function __toString(){
        return $this->nombre;
    }

    public function __construct()
    {
        $this->solicitudes = new ArrayCollection();
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
     * @return Collection|Solicitud[]
     */
    public function getSolicitudes(): Collection
    {
        return $this->solicitudes;
    }

    public function addSolicitud(Solicitud $solicitud): self
    {
        if (!$this->solicitudes->contains($solicitud)) {
            $this->solicitudes[] = $solicitud;
            $solicitude->setTitular($this);
        }

        return $this;
    }

    public function removeSolicitud(Solicitud $solicitud): self
    {
        if ($this->solicitudes->removeElement($solicitud)) {
            // set the owning side to null (unless already changed)
            if ($solicitude->getTitular() === $this) {
                $solicitude->setTitular(null);
            }
        }

        return $this;
    }
}
