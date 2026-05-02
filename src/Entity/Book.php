<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\Book\Processor\ImageCreateProcessor;
use App\Repository\BookRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => [Book::GROUP_READ]]
        ),
        new Post(
            normalizationContext: ['groups' => [Book::GROUP_READ]]
        ),
        new Delete(),
        new GetCollection(
            normalizationContext: ['groups' => [Book::GROUP_READ]]
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
            openapi: new Operation(
                summary: 'Upload image to the book',
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'required' => ['image'],
                                'properties' => [
                                    'image' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                        'description' => 'Image file (jpeg or png). Only ONE image allowed per request.',
                                    ]
                                ],
                            ],
                        ],
                    ]),
                )
            ),
            normalizationContext: ['groups' => [Book::GROUP_READ]],
            deserialize: false,
            processor: ImageCreateProcessor::class
        ),
    ]
)]
#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: 'app_book')]
class Book
{
    const GROUP_READ = 'book:read';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([Book::GROUP_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Groups([Book::GROUP_READ])]
    private ?string $title = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups([Book::GROUP_READ])]
    private ?int $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([Book::GROUP_READ])]
    private ?string $resume = null;

    #[ORM\Column]
    #[Groups([Book::GROUP_READ])]
    private ?DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Groups([Book::GROUP_READ])]
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

    #[Groups([Book::GROUP_READ])]
    public function getImagePath(): ?string
    {
        if (null === $this->image) {
            return null;
        }

        return sprintf('/images/%s', $this->image->getPath());
    }
}
