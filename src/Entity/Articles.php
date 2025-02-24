<?php

namespace App\Entity;

use App\Repository\ArticlesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ArticlesRepository::class)]
class Articles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('infos_articles')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('infos_articles')]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups('infos_articles')]
    private ?int $quantity = null;

    /**
     * @var Collection<int, BasketArticle>
     */
    #[ORM\OneToMany(targetEntity: BasketArticle::class, mappedBy: 'article')]
    #[Groups('relation_article_basketarticles')]
    private Collection $basketArticles;

    public function __construct()
    {
        $this->basketArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $basketArticle->setArticle($this);
        }

        return $this;
    }

    public function removeBasketArticle(BasketArticle $basketArticle): static
    {
        if ($this->basketArticles->removeElement($basketArticle)) {
            // set the owning side to null (unless already changed)
            if ($basketArticle->getArticle() === $this) {
                $basketArticle->setArticle(null);
            }
        }

        return $this;
    }
}
