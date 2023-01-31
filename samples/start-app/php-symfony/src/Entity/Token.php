<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
class Token
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $accessToken = null;

    public static function create(string $token): self
    {
        return (new self())->setAccessToken($token);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    private function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }
}
