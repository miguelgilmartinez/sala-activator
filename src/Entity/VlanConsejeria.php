<?php

namespace App\Entity;

use App\Repository\VlanConsejeriaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VlanConsejeriaRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_vlan_consejeria', columns: ['vlanid', 'consejeria'])]
class VlanConsejeria {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, options: ["default" => "0"])]
    private ?string $vlanid = '0';

    #[ORM\Column(length: 255, options: ["default" => "Hacienda"])]
    private ?string $consejeria = 'Hacienda';

    public function getId(): ?int {
        return $this->id;
    }

    public function getVlanid(): ?string {
        return $this->vlanid;
    }

    public function setVlanid(string $vlanid): static {
        $this->vlanid = $vlanid;

        return $this;
    }

    public function getConsejeria(): ?string {
        return $this->consejeria;
    }

    public function setConsejeria(string $consejeria): static {
        $this->consejeria = $consejeria;

        return $this;
    }
}
