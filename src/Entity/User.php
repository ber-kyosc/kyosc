<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Il existe déjà un compte avec ce mail")
 * @UniqueEntity(fields={"pseudo"}, message="Ce pseudo est déjà utilisé")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class User implements UserInterface, Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"message_author"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Veuillez renseigner un prénom.")
     * @Groups({"message_author"})
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Veuillez renseigner un nom de famille.")
     * @Groups({"message_author"})
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"message_author"})
     */
    private ?string $pseudo = null;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private ?string $address = null;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Assert\Length(max="5", min="5", minMessage="Un code postal doit contenir 5 chiffres.",
     * maxMessage="Un code postal doit contenir 5 chiffres.")
     */
    private ?string $postalCode = null;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private ?string $city = null;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\Email(message="Cet email n'est pas valide")
     * @Assert\NotBlank(message="Veuillez renseigner un mail.")
     */
    private string $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(max="100", min="8", minMessage="Votre mot de passe doit contenir au minimum 8 caractères.")
     */
    private string $password;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $biography = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *     type="integer",
     *     message="Seulement les nombres entiers sont acceptés."
     * )
     * @Assert\Positive(
     *     message = "Seulement les valeurs positives sont acceptées."
     * )
     */
    private ?int $age = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $points = 0;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Groups({"message_author"})
     */
    private ?string $profilPhoto;

    /**
     * @Vich\UploadableField(mapping="profil_photo", fileNameProperty="profilPhoto")
     * @var File|null
     * @Assert\Image(
     *     uploadErrorMessage="Une erreur est survenue lors du téléchargement.",
     *     maxSize="15097152",
     *     maxSizeMessage="Votre image est trop grande. Veuillez selectionner une image de moins de 15Mo.",
     *     detectCorrupted=true,
     *     sizeNotDetectedMessage= true,
     *     mimeTypes = {
     *          "image/png",
     *          "image/jpeg",
     *          "image/jpg",
     *      },
     *     mimeTypesMessage="Seuls les formats png, jpeg, jpg sont acceptés."
     * )
     * @Groups({"message_author"})
     */
    private $profilPhotoFile;

    /**
     * @ORM\Column(type="json")
     * @var array<string>
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $isVerified = false;

    /**
     * @ORM\ManyToMany(targetEntity=Challenge::class, mappedBy="participants")
     */
    private Collection $challenges;

    /**
     * @ORM\OneToMany(targetEntity=Challenge::class, mappedBy="creator", cascade={"remove"})
     */
    private Collection $createdChallenges;

    /**
     * @var bool
     */
    private bool $isAdmin = false;

    /**
     * @ORM\ManyToMany(targetEntity=Sport::class, inversedBy="users")
     */
    private Collection $favoriteSports;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTimeInterface|null
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=Brand::class, inversedBy="users")
     */
    private Collection $favoriteBrands;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $favoriteDestination;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $testimony;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $brandSuggestion;

    /**
     * @ORM\ManyToMany(targetEntity=Clan::class, mappedBy="members")
     */
    private Collection $clans;

    /**
     * @ORM\OneToMany(targetEntity=Clan::class, mappedBy="creator", cascade={"remove"})
     */
    private Collection $createdClans;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="author", cascade={"remove"})
     */
    private Collection $messages;

    /**
     * @ORM\OneToMany(targetEntity=Video::class, mappedBy="author", cascade={"remove"})
     */
    private Collection $videos;

    /**
     * @ORM\OneToMany(targetEntity=Invitation::class, mappedBy="creator", cascade={"remove"})
     */
    private Collection $invitationsSent;

    /**
     * @ORM\OneToMany(targetEntity=Invitation::class, mappedBy="invitedUser", cascade={"remove"})
     */
    private Collection $invitationsReceived;

    /**
     * @ORM\OneToMany(targetEntity=JoinRequest::class, mappedBy="creator", cascade={"remove"})
     */
    private Collection $requestsSent;

    /**
     * @ORM\OneToMany(targetEntity=JoinRequest::class, mappedBy="requestedUser", cascade={"remove"})
     */
    private Collection $requestsReceived;

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="author", cascade={"remove"})
     */
    private Collection $pictures;

    public function __construct()
    {
        $this->challenges = new ArrayCollection();
        $this->favoriteSports = new ArrayCollection();
        $this->updatedAt = new DateTimeImmutable('now');
        $this->favoriteBrands = new ArrayCollection();
        $this->clans = new ArrayCollection();
        $this->createdClans = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->invitationsSent = new ArrayCollection();
        $this->invitationsReceived = new ArrayCollection();
        $this->requestsSent = new ArrayCollection();
        $this->requestsReceived = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        $firstName = $this->firstName;
        $lastName = $this->lastName;
        $pseudo = $this->pseudo;

        return $firstName . ' ' . $lastName . ' - " ' . ($pseudo ?: 'pas de pseudo') . ' "';
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return int|null
     */
    public function getPoints(): ?int
    {
        return $this->points;
    }

    /**
     * @param int|null $points
     * @return $this
     */
    public function setPoints(?int $points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProfilPhoto(): ?string
    {
        return $this->profilPhoto;
    }

    /**
     * @param string|null $profilPhoto
     * @return $this
     */
    public function setProfilPhoto(?string $profilPhoto)
    {
        $this->profilPhoto = $profilPhoto;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVerified()
    {
        return $this->isVerified;
    }

    /**
     * @param bool $isVerified
     * @return $this
     */
    public function setIsVerified(bool $isVerified)
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }


    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

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
            $challenge->addParticipant($this);
        }

        return $this;
    }

    public function addCreatedChallenge(Challenge $challenge): self
    {
        if (!$this->createdChallenges->contains($challenge)) {
            $this->createdChallenges[] = $challenge;
            $challenge->setCreator($this);
        }
        return $this;
    }

    public function removeChallenge(Challenge $challenge): self
    {
        if ($this->challenges->removeElement($challenge)) {
            $challenge->removeParticipant($this);
        }
        return $this;
    }

    public function removeCreatedChallenge(Challenge $challenge): self
    {
        if ($this->createdChallenges->removeElement($challenge)) {
            // set the owning side to null (unless already changed)
            if ($challenge->getCreator() === $this) {
                $challenge->setCreator(null);
            }
        }
        return $this;
    }

    public function getIsAdmin(): ?bool
    {
        return in_array('ROLE_ADMIN', $this->roles);
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        if ($isAdmin) {
            if (!in_array('ROLE_ADMIN', $this->roles)) {
                $this->roles[] = 'ROLE_ADMIN';
            }
        } else {
            $key = array_search('ROLE_ADMIN', $this->roles);
            if ($key !== false) {
                unset($this->roles[$key]);
            }
        }
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function setProfilPhotoFile(File $image = null): User
    {
        $this->profilPhotoFile = $image;
        if (null !== $image) {
            $this->updatedAt = new DateTimeImmutable();
        }
        return $this;
    }

    public function getProfilPhotoFile(): ?File
    {
        return $this->profilPhotoFile;
    }

    /**
     * @return Collection|Sport[]
     */
    public function getFavoriteSports(): Collection
    {
        return $this->favoriteSports;
    }

    public function addFavoriteSport(Sport $favoriteSport): self
    {
        if (!$this->favoriteSports->contains($favoriteSport)) {
            $this->favoriteSports[] = $favoriteSport;
        }

        return $this;
    }

    public function removeFavoriteSport(Sport $favoriteSport): self
    {
        $this->favoriteSports->removeElement($favoriteSport);

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

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            ) = unserialize($serialized);
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return Collection|Brand[]
     */
    public function getFavoriteBrands(): Collection
    {
        return $this->favoriteBrands;
    }

    public function addFavoriteBrand(Brand $favoriteBrand): self
    {
        if (!$this->favoriteBrands->contains($favoriteBrand)) {
            $this->favoriteBrands[] = $favoriteBrand;
        }

        return $this;
    }

    public function removeFavoriteBrand(Brand $favoriteBrand): self
    {
        $this->favoriteBrands->removeElement($favoriteBrand);

        return $this;
    }

    public function getFavoriteDestination(): ?string
    {
        return $this->favoriteDestination;
    }

    public function setFavoriteDestination(?string $favoriteDestination): self
    {
        $this->favoriteDestination = $favoriteDestination;

        return $this;
    }

    public function getTestimony(): ?string
    {
        return $this->testimony;
    }

    public function setTestimony(?string $testimony): self
    {
        $this->testimony = $testimony;

        return $this;
    }

    public function getBrandSuggestion(): ?string
    {
        return $this->brandSuggestion;
    }

    public function setBrandSuggestion(?string $brandSuggestion): self
    {
        $this->brandSuggestion = $brandSuggestion;

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
            $clan->addMember($this);
        }

        return $this;
    }

    public function removeClan(Clan $clan): self
    {
        if ($this->clans->removeElement($clan)) {
            $clan->removeMember($this);
        }

        return $this;
    }

    /**
     * @return Collection|Clan[]
     */
    public function getCreatedClans(): Collection
    {
        return $this->createdClans;
    }

    public function addCreatedClan(Clan $createdClan): self
    {
        if (!$this->createdClans->contains($createdClan)) {
            $this->createdClans[] = $createdClan;
            $createdClan->setCreator($this);
        }

        return $this;
    }

    public function removeCreatedClan(Clan $createdClan): self
    {
        if ($this->createdClans->removeElement($createdClan)) {
            // set the owning side to null (unless already changed)
            if ($createdClan->getCreator() === $this) {
                $createdClan->setCreator(null);
            }
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
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
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
            $video->setAuthor($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getAuthor() === $this) {
                $video->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Invitation[]
     */
    public function getInvitationsSent(): Collection
    {
        return $this->invitationsSent;
    }

    public function addInvitationsSent(Invitation $invitationsSent): self
    {
        if (!$this->invitationsSent->contains($invitationsSent)) {
            $this->invitationsSent[] = $invitationsSent;
            $invitationsSent->setCreator($this);
        }

        return $this;
    }

    public function removeInvitationsSent(Invitation $invitationsSent): self
    {
        if ($this->invitationsSent->removeElement($invitationsSent)) {
            // set the owning side to null (unless already changed)
            if ($invitationsSent->getCreator() === $this) {
                $invitationsSent->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Invitation[]
     */
    public function getInvitationsReceived(): Collection
    {
        return $this->invitationsReceived;
    }

    public function addInvitationsReceived(Invitation $invitationsReceived): self
    {
        if (!$this->invitationsReceived->contains($invitationsReceived)) {
            $this->invitationsReceived[] = $invitationsReceived;
            $invitationsReceived->setInvitedUser($this);
        }

        return $this;
    }

    public function removeInvitationsReceived(Invitation $invitationsReceived): self
    {
        if ($this->invitationsReceived->removeElement($invitationsReceived)) {
            // set the owning side to null (unless already changed)
            if ($invitationsReceived->getInvitedUser() === $this) {
                $invitationsReceived->setInvitedUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|JoinRequest[]
     */
    public function getRequestsSent(): Collection
    {
        return $this->requestsSent;
    }

    public function addRequestsSent(JoinRequest $requestsSent): self
    {
        if (!$this->requestsSent->contains($requestsSent)) {
            $this->requestsSent[] = $requestsSent;
            $requestsSent->setCreator($this);
        }

        return $this;
    }

    public function removeRequestsSent(JoinRequest $requestsSent): self
    {
        if ($this->requestsSent->removeElement($requestsSent)) {
            // set the owning side to null (unless already changed)
            if ($requestsSent->getCreator() === $this) {
                $requestsSent->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|JoinRequest[]
     */
    public function getRequestsReceived(): Collection
    {
        return $this->requestsReceived;
    }

    public function addRequestsReceived(JoinRequest $requestsReceived): self
    {
        if (!$this->requestsReceived->contains($requestsReceived)) {
            $this->requestsReceived[] = $requestsReceived;
            $requestsReceived->setRequestedUser($this);
        }

        return $this;
    }

    public function removeRequestsReceived(JoinRequest $requestsReceived): self
    {
        if ($this->requestsReceived->removeElement($requestsReceived)) {
            // set the owning side to null (unless already changed)
            if ($requestsReceived->getRequestedUser() === $this) {
                $requestsReceived->setRequestedUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setAuthor($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getAuthor() === $this) {
                $picture->setAuthor(null);
            }
        }

        return $this;
    }
}
