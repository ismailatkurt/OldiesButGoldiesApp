<?php

namespace App\Entity;

use App\Repository\Record\RecordRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=RecordRepository::class)
 * @ORM\Table(name="records")
 * @ORM\HasLifecycleCallbacks
 */
class Record implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $genre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $published_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cover_photo;

    /**
     * @ORM\ManyToOne(targetEntity="Artist", inversedBy="records")
     * @ORM\JoinColumn(name="artist_id", referencedColumnName="id")
     */
    private $artist;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCoverPhoto(): ?string
    {
        return $this->cover_photo;
    }

    /**
     * @param string|null $cover_photo
     *
     * @return $this
     */
    public function setCoverPhoto(?string $cover_photo): self
    {
        $this->cover_photo = $cover_photo;

        return $this;
    }

    /**
     * @param Artist $artist
     *
     * @return Record
     */
    public function setArtist(Artist $artist)
    {
        $this->artist = $artist;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @ORM\PrePersist()
     *
     * @throws Exception
     */
    public function setCreatedAt(): void
    {
        $this->created_at = new DateTime();
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @ORM\PreUpdate()
     *
     * @throws Exception
     */
    public function setUpdatedAt(): void
    {
        $this->updated_at = new DateTime();
    }

    /**
     * @return string|null
     */
    public function getGenre(): ?string
    {
        return $this->genre;
    }

    /**
     * @param string|null $genre
     *
     * @return $this
     */
    public function setGenre(?string $genre = ''): self
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription(?string $description = ''): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getPublishedAt(): ?DateTime
    {
        return $this->published_at;
    }

    /**
     * @param DateTime|null $published_at
     *
     * @return $this
     */
    public function setPublishedAt(?DateTime $published_at): self
    {
        $this->published_at = $published_at;

        return $this;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
