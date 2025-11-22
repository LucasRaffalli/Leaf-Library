<?php

namespace App\Entity;

use App\Repository\BorrowRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BorrowRepository::class)]
class Borrow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $borrowDate = null;

    #[ORM\Column]
    private ?\DateTime $returnDueDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $returnDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $returnedCondition = null;

    #[ORM\Column]
    private ?bool $hasDeposit = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $depositAmount = null;

    #[ORM\Column]
    private ?bool $isDepositReturned = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $additionalFees = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adminNotes = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'borrows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'borrows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\ManyToOne(inversedBy: 'borrows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BorrowStatus $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorrowDate(): ?\DateTime
    {
        return $this->borrowDate;
    }

    public function setBorrowDate(\DateTime $borrowDate): static
    {
        $this->borrowDate = $borrowDate;

        return $this;
    }

    public function getReturnDueDate(): ?\DateTime
    {
        return $this->returnDueDate;
    }

    public function setReturnDueDate(\DateTime $returnDueDate): static
    {
        $this->returnDueDate = $returnDueDate;

        return $this;
    }

    public function getReturnDate(): ?\DateTime
    {
        return $this->returnDate;
    }

    public function setReturnDate(?\DateTime $returnDate): static
    {
        $this->returnDate = $returnDate;

        return $this;
    }

    public function getReturnedCondition(): ?string
    {
        return $this->returnedCondition;
    }

    public function setReturnedCondition(?string $returnedCondition): static
    {
        $this->returnedCondition = $returnedCondition;

        return $this;
    }

    public function hasDeposit(): ?bool
    {
        return $this->hasDeposit;
    }

    public function setHasDeposit(bool $hasDeposit): static
    {
        $this->hasDeposit = $hasDeposit;

        return $this;
    }

    public function getDepositAmount(): ?string
    {
        return $this->depositAmount;
    }

    public function setDepositAmount(string $depositAmount): static
    {
        $this->depositAmount = $depositAmount;

        return $this;
    }

    public function isDepositReturned(): ?bool
    {
        return $this->isDepositReturned;
    }

    public function setIsDepositReturned(bool $isDepositReturned): static
    {
        $this->isDepositReturned = $isDepositReturned;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getStatus(): ?BorrowStatus
    {
        return $this->status;
    }

    public function setStatus(?BorrowStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAdditionalFees(): ?string
    {
        return $this->additionalFees;
    }

    public function setAdditionalFees(?string $additionalFees): static
    {
        $this->additionalFees = $additionalFees;

        return $this;
    }

    public function getAdminNotes(): ?string
    {
        return $this->adminNotes;
    }

    public function setAdminNotes(?string $adminNotes): static
    {
        $this->adminNotes = $adminNotes;

        return $this;
    }
}
