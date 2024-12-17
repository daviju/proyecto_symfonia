<?php

namespace App\Entity;

use App\Repository\PreguntaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PreguntaRepository::class)]
class Pregunta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    public ?string $titulo = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public ?\DateTimeInterface $fecha_inicio = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $fecha_fin = null;

    /**
     * @var Collection<int, Respuesta>
     */
    
    #[ORM\OneToMany(targetEntity: Respuesta::class, mappedBy: 'pregunta', orphanRemoval: true)]
    public Collection $respuesta;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $r1 = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $r2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $r3 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $r4 = null;

    public function __construct()
    {
        $this->respuesta = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fecha_inicio;
    }

    public function setFechaInicio(\DateTimeInterface $fecha_inicio): static
    {
        $this->fecha_inicio = $fecha_inicio;

        return $this;
    }

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fecha_fin;
    }

    public function setFechaFin(?\DateTimeInterface $fecha_fin): static
    {
        $this->fecha_fin = $fecha_fin;

        return $this;
    }

    /**
     * @return Collection<int, Respuesta>
     */
    public function getRespuesta(): Collection
    {
        return $this->respuesta;
    }

    public function addRespuestum(Respuesta $respuestum): static
    {
        if (!$this->respuesta->contains($respuestum)) {
            $this->respuesta->add($respuestum);
            $respuestum->setPregunta($this);
        }

        return $this;
    }

    public function removeRespuestum(Respuesta $respuestum): static
    {
        if ($this->respuesta->removeElement($respuestum)) {
            // set the owning side to null (unless already changed)
            if ($respuestum->getPregunta() === $this) {
                $respuestum->setPregunta(null);
            }
        }

        return $this;
    }

    public function getR1(): ?string
    {
        return $this->r1;
    }

    public function setR1(string $r1): static
    {
        $this->r1 = $r1;

        return $this;
    }

    public function getR2(): ?string
    {
        return $this->r2;
    }

    public function setR2(?string $r2): static
    {
        $this->r2 = $r2;

        return $this;
    }

    public function getR3(): ?string
    {
        return $this->r3;
    }

    public function setR3(?string $r3): static
    {
        $this->r3 = $r3;

        return $this;
    }

    public function getR4(): ?string
    {
        return $this->r4;
    }

    public function setR4(string $r4): static
    {
        $this->r4 = $r4;

        return $this;
    }
}
