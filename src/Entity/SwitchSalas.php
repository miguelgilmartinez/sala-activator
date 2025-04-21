<?php

namespace App\Entity;

use App\Repository\SwitchSalasRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SwitchSalasRepository::class)]
class SwitchSalas {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 15, unique: true)]
    private ?string $ip = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getNombre(): ?string {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static {
        $this->nombre = $nombre;

        return $this;
    }

    public function getIp(): ?string {
        return $this->ip;
    }

    public function setIp(string $ip): static {
        $this->ip = $ip;

        return $this;
    }
}
