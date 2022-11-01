<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('main')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Title name cannot be longer than {{ limit }} characters',
    )]
    private ?string $title = null;
    #[Groups('main')]
    #[ORM\Column(length: 140, nullable: true)]
    #[Assert\Length(
        max: 140,
        maxMessage: 'Content cannot be longer than {{ limit }} characters',
    )]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'post')]
    private ?Blog $blog = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'readPosts')]
    private Collection $usersRead;

    public function __construct()
    {
        $this->usersRead = new ArrayCollection();
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

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): self
    {
        $this->blog = $blog;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersRead(): Collection
    {
        return $this->usersRead;
    }

    public function addUsersRead(User $usersRead): self
    {
        if (!$this->usersRead->contains($usersRead)) {
            $this->usersRead->add($usersRead);
            $usersRead->addReadPost($this);
        }

        return $this;
    }

    public function removeUsersRead(User $usersRead): self
    {
        if ($this->usersRead->removeElement($usersRead)) {
            $usersRead->removeReadPost($this);
        }

        return $this;
    }
}
