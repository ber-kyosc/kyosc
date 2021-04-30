<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\ChallengeRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ChallengeRepository::class)
 * @Vich\Uploadable
 */
class Challenge
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Veuillez choisir un titre pour votre challenge")
     * @Assert\Length(max="100", min="2", minMessage="Veuillez choisir un titre faisant plus de 2 caractères",
     * maxMessage="Un maximum de 100 caractères est autorisé")
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank(message="Veuillez choisir une citation pour votre challenge")
     * @Assert\Length(max="100", min="2", minMessage="Veuillez choisis un titre faisant plus de 2 caractères",
     * maxMessage="Un maximum de 100 caractères est autorisé")
     */
    private string $quotation;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez choisir une descritpion pour votre challenge")
     *
     */
    private string $description;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Veuillez entrer une localisation")
     * TODO vérifier les caractères
     */
    private string $location;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * TODO faire la verification de locationEnd
     */
    private string $locationEnd;

    /**
     * @Vich\UploadableField(mapping="challenge_photo", fileNameProperty="challengePhoto")
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
    private $challengePhotoFile;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type(type="DateTimeInterface", message="Custom Message")
     * @Assert\NotNull(message="Veuillez entrer une date")
     * @Assert\GreaterThanOrEqual(
     *     value="today",
     *     message="La date du challenge doit être supérieure ou égale à celle d'aujourd'hui"
     * )
     */
    private ?DateTimeInterface $dateStart;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez décrire le parcours du challenge")
     */
    private string $journey;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Positive(message="La distance doit être une valeure numérique positive")
     */
    private float $distance;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez remplir les informations pratiques")
     */
    private string $information;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\Type(type="bool")
     */
    private bool $isPublic;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=Sport::class, inversedBy="challenges")
     * @Assert\Count(min=1, minMessage="Selectionner au moins un sport")
     * @Assert\Valid()
     */
    private Collection $sports;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="challenges")
     */
    private Collection $participants;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="createdChallenges")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $creator;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private ?string $challengePhoto = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isFeatured = false;

    /**
     * @ORM\ManyToMany(targetEntity=Clan::class, mappedBy="challenges")
     */
    private Collection $clans;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="challenge")
     */
    private Collection $messages;

    /**
     * @ORM\OneToMany(targetEntity=Video::class, mappedBy="challenge")
     */
    private Collection $videos;

    public function __construct()
    {
        $this->sports = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->clans = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getQuotation(): ?string
    {
        return $this->quotation;
    }

    public function setQuotation(string $quotation): self
    {
        $this->quotation = $quotation;

        return $this;
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getDateStart(): ?DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(?DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
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
        }

        return $this;
    }

    public function removeSport(Sport $sport): self
    {
        $this->sports->removeElement($sport);

        return $this;
    }

    public function getLocationEnd(): ?string
    {
        return $this->locationEnd;
    }

    public function setLocationEnd(string $locationEnd): self
    {
        $this->locationEnd = $locationEnd;

        return $this;
    }

    public function getJourney(): ?string
    {
        return $this->journey;
    }

    public function setJourney(string $journey): self
    {
        $this->journey = $journey;

        return $this;
    }

    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(string $information): self
    {
        $this->information = $information;

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
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        $this->participants->removeElement($participant);
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

    /**
     * @param File|UploadedFile|null $image
     * @param File|null $image
     * @return $this
     */
    public function setChallengePhotoFile(File $image = null): Challenge
    {
        $this->challengePhotoFile = $image;
        if (null !== $image) {
            $this->updatedAt = new DateTimeImmutable();
        }
        return $this;
    }

    public function getChallengePhotoFile(): ?File
    {
        return $this->challengePhotoFile;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getChallengePhoto(): ?string
    {
        return $this->challengePhoto;
    }

    public function setChallengePhoto(?string $challengePhoto): self
    {
        $this->challengePhoto = $challengePhoto;

        return $this;
    }

    public function getIsFeatured(): ?bool
    {
        return $this->isFeatured;
    }

    public function setIsFeatured(bool $isFeatured): self
    {
        $this->isFeatured = $isFeatured;

        return $this;
    }

    /**
     * @return Collection|Clan[]
     */
    public function getClans(): Collection
    {
        return $this->clans;
    }

    public function addClan(Clan $clan): self
    {
        if (!$this->clans->contains($clan)) {
            $this->clans[] = $clan;
            $clan->addChallenge($this);
        }

        return $this;
    }

    public function removeClan(Clan $clan): self
    {
        if ($this->clans->removeElement($clan)) {
            $clan->removeChallenge($this);
        }

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
            $message->setChallenge($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getChallenge() === $this) {
                $message->setChallenge(null);
            }
        }

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
            $video->setChallenge($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getChallenge() === $this) {
                $video->setChallenge(null);
            }
        }

        return $this;
    }
}
