<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;

// Arefeen : Use  for Schema.

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company {

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

    #[ORM\Column(type: 'text', length: 4294967295, nullable: true)]
    private ?string $finances = null;

    #[ORM\Column]
    private ?bool $deleted = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deleted_at = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getRegistrationCode(): ?int {
        return $this->registration_code;
    }

    public function setRegistrationCode(int $registration_code): static {
        $this->registration_code = $registration_code;

        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;

        return $this;
    }

    public function getDetails(): array {
        return $this->details;
    }

    public function setDetails(array $details): static {
        $this->details = $details;

        return $this;
    }

    public function getFinances(): ?string {
        return $this->finances;
    }

    public function setFinances(?string $finances): static {
        $this->finances = $finances;

        return $this;
    }

    public function isDeleted(): ?bool {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): static {
        $this->deleted = $deleted;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeImmutable $deleted_at): static {
        $this->deleted_at = $deleted_at;

        return $this;
    }
}
