<?php

namespace App\Entity\Image;

use App\Entity\Publication;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
class PublicationCover
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * @Assert\Image(
     *     maxWidth=4000,
     *     maxHeight=4000,
     *     maxSize="4Mi"
     * )
     * @Vich\UploadableField(mapping="publication_image_cover", fileNameProperty="imageName", size="imageSize")
     *
     * @var File
     */
    private $imageFile;
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $imageName;
    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $imageSize;
    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTimeInterface
     */
    private $updatedAt;
    /**
     * @var Publication
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Publication")
     */
    private $publication;

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile|null $image
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setImageFile(?File $image = null)
    {
        $this->imageFile = $image;
        if (null !== $image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @param string|null $imageName
     *
     * @return $this
     */
    public function setImageName(?string $imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize)
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Publication
     */
    public function getPublication(): Publication
    {
        return $this->publication;
    }

    /**
     * @param Publication $publication
     *
     * @return PublicationCover
     */
    public function setPublication(?Publication $publication): PublicationCover
    {
        $this->publication = $publication;

        return $this;
    }
}
