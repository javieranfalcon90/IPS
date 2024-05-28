<?php

namespace App\Entity;

use App\Repository\ProductoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ProductoRepository::class)
 * @UniqueEntity("nombre", message= "No se pudo relizar la operación correctamente porque el valor {{ value }} del campo NOMBRE ya está registrado.")
 * @Audit\Auditable()
 */
class Producto
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
     * @ORM\OneToMany(targetEntity=Asignacion::class, mappedBy="producto")
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
            $asignacione->setProducto($this);
        }

        return $this;
    }

    public function removeAsignacione(Asignacion $asignacione): self
    {
        if ($this->asignaciones->removeElement($asignacione)) {
            // set the owning side to null (unless already changed)
            if ($asignacione->getProducto() === $this) {
                $asignacione->setProducto(null);
            }
        }

        return $this;
    }

}
