<?php

namespace App\Entity;

use App\Repository\ClanRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ClanRepository::class)
 * @Vich\Uploadable
 */
class Clan
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez choisir un nom pour votre clan")
     * @Assert\Length(max="255", min="2", minMessage="Veuillez choisir un nom faisant plus de 2 caractères",
     * maxMessage="Un maximum de 255 caractères est autorisé")
     */
    private string $name;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="clans")
     */
    private Collection $members;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $logo = null;

    /**
     * @Vich\UploadableField(mapping="clan_logo", fileNameProperty="logo")
     * @var File|null
     * @Assert\Image(
     *     uploadErrorMessage="Une erreur est survenue lors du téléchargement.",
     *     maxSize="10000000",
     *     maxSizeMessage="Votre image est trop grande. Veuillez selectionner une image de moins de 10Mo.",
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
    private $logoFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $banner = null;

    /**
     * @Vich\UploadableField(mapping="clan_banner", fileNameProperty="banner")
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
    private $bannerFile;

    /**
     * @ORM\Column(type="text")
     */
    private string $description;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="createdClans")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $creator;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $isPublic;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="clan", cascade={"remove"})
     */
    private Collection $messages;

    /**
     * @ORM\ManyToMany(targetEntity=Challenge::class, inversedBy="clans")
     */
    private Collection $challenges;

    /**
     * @ORM\OneToMany(targetEntity=Video::class, mappedBy="clan", cascade={"remove"})
     */
    private Collection $videos;

    /**
     * @ORM\OneToMany(targetEntity=Invitation::class, mappedBy="clan")
     */
    private Collection $invitations;

    /**
     * @ORM\OneToMany(targetEntity=JoinRequest::class, mappedBy="clan")
     */
    private Collection $requests;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->challenges = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->invitations = new ArrayCollection();
        $this->requests = new ArrayCollection();
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

    /**
     * @return Collection|User[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        $this->members->removeElement($member);

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(?string $banner): self
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * @param File|UploadedFile|null $image
     * @param File|null $image
     * @return $this
     */
    public function setBannerFile(File $image = null): Clan
    {
        $this->bannerFile = $image;
        if (null !== $image) {
            $this->updatedAt = new DateTimeImmutable();
        }
        return $this;
    }

    public function getBannerFile(): ?File
    {
        return $this->bannerFile;
    }

    /**
     * @param File|UploadedFile|null $image
     * @param File|null $image
     * @return $this
     */
    public function setLogoFile(File $image = null): Clan
    {
        $this->logoFile = $image;
        if (null !== $image) {
            $this->updatedAt = new DateTimeImmutable();
        }
        return $this;
    }

    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreator(): User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        /* @phpstan-ignore-next-line */
        $this->creator = $creator;

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

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setClan($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getClan() === $this) {
                $message->setClan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Challenge[]
     */
    public function getChallenges(): Collection
    {
        return $this->challenges;
    }

    public function addChallenge(Challenge $challenge): self
    {
        if (!$this->challenges->contains($challenge)) {
            $this->challenges[] = $challenge;
        }

        return $this;
    }

    public function removeChallenge(Challenge $challenge): self
    {
        $this->challenges->removeElement($challenge);

        return $this;
    }

    /**
     * @return Collection|Video[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setClan($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getClan() === $this) {
                $video->setClan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Invitation[]
     */
    public function getInvitations(): Collection
    {
        return $this->invitations;
    }

    public function addInvitation(Invitation $invitation): self
    {
        if (!$this->invitations->contains($invitation)) {
            $this->invitations[] = $invitation;
            $invitation->setClan($this);
        }

        return $this;
    }

    public function removeInvitation(Invitation $invitation): self
    {
        if ($this->invitations->removeElement($invitation)) {
            // set the owning side to null (unless already changed)
            if ($invitation->getClan() === $this) {
                $invitation->setClan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|JoinRequest[]
     */
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function addRequest(JoinRequest $request): self
    {
        if (!$this->requests->contains($request)) {
            $this->requests[] = $request;
            $request->setClan($this);
        }

        return $this;
    }

    public function removeRequest(JoinRequest $request): self
    {
        if ($this->requests->removeElement($request)) {
            // set the owning side to null (unless already changed)
            if ($request->getClan() === $this) {
                $request->setClan(null);
            }
        }

        return $this;
    }
}
