<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $logo;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private string $color;

    /**
     * @ORM\OneToMany(targetEntity=Sport::class, mappedBy="category")
     */
    private Collection $sports;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $picto;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $goutte;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $carousel;

    public function __construct()
    {
        $this->sports = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection|Sport[]
     */
    public function getSports(): Collection
    {
        return $this->sports;
    }

    public function addSport(Sport $sport): self
    {
        if (!$this->sports->contains($sport)) {
            $this->sports[] = $sport;
            $sport->setCategory($this);
        }

        return $this;
    }

    public function getPicto(): ?string
    {
        return $this->picto;
    }

    public function setPicto(string $picto): self
    {
        $this->picto = $picto;

        return $this;
    }

    public function getGoutte(): ?string
    {
        return $this->goutte;
    }

    public function setGoutte(string $goutte): self
    {
        $this->goutte = $goutte;

        return $this;
    }

    public function getCarousel(): ?string
    {
        return $this->carousel;
    }

    public function setCarousel(?string $carousel): self
    {
        $this->carousel = $carousel;

        return $this;
    }
}
