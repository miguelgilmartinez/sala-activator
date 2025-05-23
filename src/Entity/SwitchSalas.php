<?php

namespace App\Entity;

use App\Repository\SwitchSalasRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Miguel Gil Martínez <miguel.gil.martinez@juntadeandalucia.es>
 *
 * Configuración de salas - IP del switch - Puertos ethernet asignados en el switch
 */
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

    #[ORM\Column(length: 255, options: ['default' => '0'])]
    private ?string $puertos = '0';

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

    public function getVlan(): ?string {
        return $this->vlan;
    }

    public function setVlan(string $vlan): static {
        $this->vlan = $vlan;

        return $this;
    }

    public function getPuertos(): ?string {
        return $this->puertos;
    }

    public function setPuertos(string $puertos): static {
        $this->puertos = $puertos;
        return $this;
    }

    public function toArray(): array {
        $salas['id'] = $this->id;
        $salas['ip'] = $this->ip;
        $salas['nombre'] = $this->nombre;
        $salas['puertos'] = $this->puertos;
         
        return $salas;
    }
}
