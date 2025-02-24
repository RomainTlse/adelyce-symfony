<?php

namespace App\Entity;

use App\Repository\NotificationsRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationsRepository::class)]
class Notifications
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('infos_notifications')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('infos_notifications')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups('infos_notifications')]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups('infos_notifications')]
    private ?DateTimeInterface $dt_created = null;

    #[ORM\Column]
    #[Groups('infos_notifications')]
    private ?bool $is_open = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function isOpen(): ?bool
    {
        return $this->is_open;
    }

    public function setIsOpen(bool $is_open): static
    {
        $this->is_open = $is_open;

        return $this;
    }
}
