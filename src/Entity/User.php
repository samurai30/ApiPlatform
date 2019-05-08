<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ApiResource(
 *     itemOperations={
 *     "get"={
 *      "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *     "normalization_context"={
 *          "groups" = {"get"}
 *     }
 *     },
 *     "put"={
 *     "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *     "denormalization_context"={
 *          "groups" = {"put"}
 *     },
 *     "normalization_context"={
 *          "groups" = {"get"}
 *     }
 *     }
 *     },
 *     collectionOperations={
 *     "post"={
 *          "denormalization_context"={
 *           "groups" = {"post"}
 *   },
 *     "normalization_context"={
 *          "groups" = {"get"}
 *     }
 *     }
 * },
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"},message="This username is already taken")
 * @UniqueEntity(fields={"email"},message="This email is already taken")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("get")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get","post"})
     * @Assert\NotBlank(message="input username please")
     * @Assert\Length(min="5",max="240",maxMessage="too long",minMessage="too short")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","put"})
     * @Assert\NotBlank(message="input password please")
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{6,}/",
     *     message="Password must be 6 character long and contains at least one digit, one Upper case letter and one Lower case letter"
     * )
     */
    private $password;


    /**
     * @Assert\NotBlank()
     * @Groups({"post","put"})
     * @Assert\Expression(
     *     "this.getPassword() === this.getRetypePassword()",
     *     message="Password does not match"
     * )
     */
    private $retypePassword;

    /**
     * @return mixed
     */
    public function getRetypePassword()
    {
        return $this->retypePassword;
    }

    /**
     * @param mixed $retypePassword
     */
    public function setRetypePassword($retypePassword): void
    {
        $this->retypePassword = $retypePassword;
    }



    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get","post","put"})
     * @Assert\NotBlank(message="input name please")
     * @Assert\Length(min="3",max="200")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","put"})
     * @Assert\NotBlank(message="input email please")
     * @Assert\Email(message="Not A valid email")
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Posts", mappedBy="author", orphanRemoval=true)
     * @Groups({"get","put","post"})
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author")
     * @Groups({"get"})
     */
    private $comments;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Posts[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Posts $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Posts $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
       return ['ROLE_USER'];
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
         return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

    }
}
