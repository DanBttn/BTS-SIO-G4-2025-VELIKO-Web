<?php

namespace App\Entity;

use App\Repository\StationFavoriRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StationFavoriRepository::class)]
class StationFavori
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "bigint")]
    private ?int $id_station = null;

    #[ORM\Column]
    private ?int $id_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdStation(): ?int
    {
        return $this->id_station;
    }

    public function setIdStation(int $id_station): static
    {
        $this->id_station = $id_station;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }
}
