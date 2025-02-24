<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('infos_users')]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups('infos_users')]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups('infos_users')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups('infos_users')]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups('infos_users')]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups('infos_users')]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Groups('infos_users')]
    private ?string $username = null;

    /**
     * @var Collection<int, Baskets>
     */
    #[ORM\OneToMany(targetEntity: Baskets::class, mappedBy: 'user')]
    #[Groups('relation_user_baskets')]
    private Collection $baskets;

    /**
     * @var Collection<int, BasketArticle>
     */
    #[ORM\OneToMany(targetEntity: BasketArticle::class, mappedBy: 'associated_user')]
    private Collection $basketArticles;


    public function __construct()
    {
        $this->baskets = new ArrayCollection();
        $this->basketArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @return list<string>
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
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, Baskets>
     */
    public function getBaskets(): Collection
    {
        return $this->baskets;
    }

    public function addBasket(Baskets $basket): static
    {
        if (!$this->baskets->contains($basket)) {
            $this->baskets->add($basket);
            $basket->setUser($this);
        }

        return $this;
    }

    public function removeBasket(Baskets $basket): static
    {
        if ($this->baskets->removeElement($basket)) {
            // set the owning side to null (unless already changed)
            if ($basket->getUser() === $this) {
                $basket->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BasketArticle>
     */
    public function getBasketArticles(): Collection
    {
        return $this->basketArticles;
    }

    public function addBasketArticle(BasketArticle $basketArticle): static
    {
        if (!$this->basketArticles->contains($basketArticle)) {
            $this->basketArticles->add($basketArticle);
            $basketArticle->setAssociatedUser($this);
        }

        return $this;
    }

    public function removeBasketArticle(BasketArticle $basketArticle): static
    {
        if ($this->basketArticles->removeElement($basketArticle)) {
            // set the owning side to null (unless already changed)
            if ($basketArticle->getAssociatedUser() === $this) {
                $basketArticle->setAssociatedUser(null);
            }
        }

        return $this;
    }
}
