<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 * @Vich\Uploadable
 */
class Picture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $path = null;

    /**
     * @Vich\UploadableField(mapping="picture_path", fileNameProperty="path")
     * @var File|null
     * @Assert\Image(
     *     uploadErrorMessage="Une erreur est survenue lors du téléchargement.",
     *     maxSize="20000000",
     *     maxSizeMessage="Votre image est trop grande. Veuillez selectionner une image de moins de 20Mo.",
     *     detectCorrupted=true,
     *     sizeNotDetectedMessage= true,
     *     mimeTypes = {
     *          "image/png",
     *          "image/jpeg",
     *          "image/jpg",
     *      },
     *     mimeTypesMessage="Seuls les formats png, jpeg, jpg sont acceptés."
     * )
     */
    private $pathFile;

    /**
     * @ORM\ManyToOne(targetEntity=Challenge::class, inversedBy="pictures")
     */
    private ?Challenge $challenge;

    /**
     * @ORM\ManyToOne(targetEntity=Clan::class, inversedBy="pictures")
     */
    private ?Clan $clan;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pictures")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param File|UploadedFile|null $image
     * @param File|null $image
     * @return $this
     */
    public function setPathFile(File $image = null): Picture
    {
        $this->pathFile = $image;
        if (null !== $image) {
            $this->updatedAt = new DateTimeImmutable();
        }
        return $this;
    }

    public function getPathFile(): ?File
    {
        return $this->pathFile;
    }

    public function getChallenge(): ?Challenge
    {
        return $this->challenge;
    }

    public function setChallenge(?Challenge $challenge): self
    {
        $this->challenge = $challenge;

        return $this;
    }

    public function getClan(): ?Clan
    {
        return $this->clan;
    }

    public function setClan(?Clan $clan): self
    {
        $this->clan = $clan;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
