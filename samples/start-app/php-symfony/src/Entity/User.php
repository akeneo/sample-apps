<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $sub = null;

    public static function create(string $email, string $firstname, string $lastname, string $sub): self
    {
        return (new self())->setEmail($email)->setFirstname($firstname)->setLastname($lastname)->setSub($sub);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    private function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    protected function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    protected function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    protected function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getSub(): ?string
    {
        return $this->sub;
    }

    public function setSub(?string $sub): self
    {
        $this->sub = $sub;

        return $this;
    }
}
