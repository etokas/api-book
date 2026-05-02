<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Book\Processor\ImageCreateProcessor;
use App\Repository\BookRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['book:read']]
        ),
        new Post(
            normalizationContext: ['groups' => ['book:read']]
        ),
        new Delete(),
        new GetCollection(
            normalizationContext: ['groups' => ['book:read']]
        ),
        new Post(
            uriTemplate: '/books/{id}/image',
            inputFormats: ['multipart' => ['multipart/form-data']],
            uriVariables: [
                'id' => new Link(
                    fromClass: Book::class,
                    identifiers: ['id']
                ),
            ],
            normalizationContext: ['groups' => ['book:read']],
            deserialize: false,
            processor: ImageCreateProcessor::class
        ),
    ]
)]
#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: 'app_book')]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['book:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Groups(['book:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['book:read'])]
    private ?int $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['book:read'])]
    private ?string $resume = null;

    #[ORM\Column]
    #[Groups(['book:read'])]
    private ?DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read'])]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Image $image = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): static
    {
        $this->resume = $resume;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;

        return $this;
    }

    #[Groups(['book:read'])]
    public function getImagePath(): ?string
    {
        return sprintf('/images/%s',  $this->image->getPath());
    }
}
