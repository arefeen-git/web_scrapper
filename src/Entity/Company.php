<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;

// Arefeen : Use  for Schema.

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $registration_code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private array $details = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $finances = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegistrationCode(): ?int
    {
        return $this->registration_code;
    }

    public function setRegistrationCode(int $registration_code): static
    {
        $this->registration_code = $registration_code;

        return $this;
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

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(array $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function getFinances(): ?string
    {
        return $this->finances;
    }

    public function setFinances(?string $finances): static
    {
        $this->finances = $finances;

        return $this;
    }
}
