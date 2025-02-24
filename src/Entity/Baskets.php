<?php

namespace App\Entity;

use App\Repository\BasketsRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BasketsRepository::class)]
class Baskets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('infos_baskets')]
    private ?int $id = null;

    #[ORM\Column(length: 12)]
    #[Groups('infos_baskets')]
    private ?string $basket_number = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups('infos_baskets')]
    private ?DateTimeInterface $dt_created = null;

    #[ORM\ManyToOne(inversedBy: 'baskets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('relation_basket_user')]
    private ?Users $user = null;

    /**
     * @var Collection<int, BasketArticle>
     */
    #[ORM\OneToMany(targetEntity: BasketArticle::class, mappedBy: 'basket')]
    #[Groups('relation_basket_basketArticles')]
    private Collection $basketArticles;

    public function __construct()
    {
        $this->basketArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBasketNumber(): ?string
    {
        return $this->basket_number;
    }

    public function setBasketNumber(string $basket_number): static
    {
        $this->basket_number = $basket_number;

        return $this;
    }

    public function getDtCreated(): ?DateTimeInterface
    {
        return $this->dt_created;
    }

    public function setDtCreated(DateTimeInterface $dt_created): static
    {
        $this->dt_created = $dt_created;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): static
    {
        $this->user = $user;

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
            $basketArticle->setBasket($this);
        }

        return $this;
    }

    public function removeBasketArticle(BasketArticle $basketArticle): static
    {
        if ($this->basketArticles->removeElement($basketArticle)) {
            // set the owning side to null (unless already changed)
            if ($basketArticle->getBasket() === $this) {
                $basketArticle->setBasket(null);
            }
        }

        return $this;
    }
}
