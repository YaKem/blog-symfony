<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @ORM\Table(name="articles")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class Article
{
    use Timestampable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Assert\Length(min=10, minMessage="Message trop court")
     */
    private $content;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="article_image", fileNameProperty="imageName")
     * @Assert\NotNull(message="Veuillez choisir une image")
     * @Assert\Image(maxSize="8M", maxSizeMessage="Image trop grande {{ size }}M > {{ limit }}M")
     * 
     * @var File|null
     */
     private $imageFile;

     /**
      * @ORM\Column(type="string")
      *
      * @var string|null
      */
     private $imageName;

     /**
      * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="articles")
      * @ORM\JoinColumn(nullable=false)
      */
     private $category;

     /**
      * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="article", orphanRemoval=true)
      */
     private $comments;

     public function __construct()
     {
         $this->comments = new ArrayCollection();
     }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
     public function setImageFile(?File $imageFile = null): void
     {
         $this->imageFile = $imageFile;
 
         if (null !== $imageFile) {
             // It is required that at least one field changes if you are using doctrine
             // otherwise the event listeners won't be called and the file is lost
             $this->setUpdatedAt(new \DateTimeImmutable);
         }
     }
 
     public function getImageFile(): ?File
     {
         return $this->imageFile;
     }
 
     public function setImageName(?string $imageName): self
     {
         $this->imageName = $imageName;

         return $this;
     }
 
     public function getImageName(): ?string
     {
         return $this->imageName;
     }

     public function __toString()
     {
         return $this->title;
     }

     public function getCategory(): ?Category
     {
         return $this->category;
     }

     public function setCategory(?Category $category): self
     {
         $this->category = $category;

         return $this;
     }

     /**
      * @return Collection|Comment[]
      */
     public function getComments(): Collection
     {
         return $this->comments;
     }

     public function addComment(Comment $comment): self
     {
         if (!$this->comments->contains($comment)) {
             $this->comments[] = $comment;
             $comment->setArticle($this);
         }

         return $this;
     }

     public function removeComment(Comment $comment): self
     {
         if ($this->comments->contains($comment)) {
             $this->comments->removeElement($comment);
             // set the owning side to null (unless already changed)
             if ($comment->getArticle() === $this) {
                 $comment->setArticle(null);
             }
         }

         return $this;
     }
}
