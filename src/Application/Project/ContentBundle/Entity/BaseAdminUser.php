<?php

namespace App\Application\Project\ContentBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('email', 'O email definido já está sendo utilizado')]
class BaseAdminUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    #[ORM\Column(length: 180, unique: true, nullable: false)]
    protected string $email;

    #[ORM\Column(name: 'password', nullable: false)]
    protected string $password;

    #[ORM\Column(name: 'roles', unique: false)]
    protected array $roles = [];

    #[ORM\Column(name: 'created_at', type: "datetime", nullable: true, options: ["default" => "CURRENT_TIMESTAMP"])]
    protected DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: "datetime", nullable: true, options: ["default" => "CURRENT_TIMESTAMP"])]
    protected DateTime $updatedAt;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /** @see PasswordAuthenticatedUserInterface */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword($password): self
    {
        if($password)
            $this->password = $password;

        return $this;
    }

    /** @see UserInterface */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /** @return DateTime */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /** @param DateTime $createdAt */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /** @return DateTime */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /** @param DateTime $updatedAt */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

}