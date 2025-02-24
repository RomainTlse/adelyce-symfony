<?php

namespace App\Entity;

use App\Repository\BasketArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: BasketArticleRepository::class)]
class BasketArticle
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'basketArticles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('relation_basketarticle_baskets')]
    private ?Baskets $basket = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'basketArticles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('relation_basketarticle_articles')]
    private ?Articles $article = null;

    #[ORM\Column]
    #[Groups('infos_basketarticles')]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'basketArticles')]
    private ?Users $associated_user = null;

    public function getBasket(): ?Baskets
    {
        return $this->basket;
    }

    public function setBasket(?Baskets $basket): static
    {
        $this->basket = $basket;

        return $this;
    }

    public function getArticle(): ?Articles
    {
        return $this->article;
    }

    public function setArticle(?Articles $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getAssociatedUser(): ?Users
    {
        return $this->associated_user;
    }

    public function setAssociatedUser(?Users $associated_user): static
    {
        $this->associated_user = $associated_user;

        return $this;
    }
}
