<?php

namespace App\Entity;

use App\Repository\Record\RecordRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\PrePersist;
use Exception;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

/**
 * @ORM\Entity(repositoryClass=RecordRepository::class)
 * @ORM\Table(name="records")
 * @Entity @HasLifecycleCallbacks
 */
class Record implements \JsonSerializable
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cover_photo;

    /**
     * @ManyToOne(targetEntity="Artist")
     * @JoinColumn(name="artist_id", referencedColumnName="id")
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
     * @PrePersist
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
     * @PrePersist
     *
     * @throws Exception
     */
    public function setUpdatedAt(): void
    {
        $this->updated_at = new DateTime();
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
