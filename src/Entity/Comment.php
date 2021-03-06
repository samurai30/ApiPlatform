<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     itemOperations={
 *     "get",
 *     "put"={
 *     "access_control"="is_granted('ROLE_EDITOR') or (is_granted('ROLE_COMMENTATOR') and object.getAuthor() == user)"
 *     }
 * },
 *     collectionOperations={
 *     "get",
 *     "post"={
 *             "access_control"="is_granted('ROLE_COMMENTATOR')"
 *     }
 *     },
 *     subresourceOperations={
 *     "api_posts_comments_get_subresource"={
 *          "normalization_context"={
 *                 "groups" = {"getComments"}
 *          }
 *     }
 *     },
 *     denormalizationContext={
 *          "groups"={"post"}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment implements AuthoredEntityInterface,PublishedDateEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"getComments"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(min="5",max="3000")
     * @Groups({"post","getComments"})
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"getComments"})
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"getComments"})
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Posts", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post"})
     */
    private $post;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): PublishedDateEntityInterface
    {
        $this->published = $published;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;

        return $this;
    }

    public function getPost(): ?Posts
    {
        return $this->post;
    }

    public function setPost(?Posts $post): self
    {
        $this->post = $post;

        return $this;
    }
}
