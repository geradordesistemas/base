<?php

namespace App\Application\Project\SettingBundle\Entity;

use App\Application\Project\SettingBundle\Repository\SmtpEmailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SmtpEmailRepository::class)]
class SmtpEmail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'email', type: 'string', nullable: true)]
    private ?string $email = null;

    #[ORM\Column(name: 'username', type: 'string', nullable: true)]
    private ?string $username = null;

    #[ORM\Column(name: 'host', type: 'string', nullable: true)]
    private ?string $host = null;

    #[ORM\Column(name: 'port', type: 'decimal', nullable: true)]
    private ?string $port = null;

    #[ORM\Column(name: 'password', type: 'string', nullable: true)]
    private ?string $password = null;

    #[ORM\Column(name: 'typeSecurity', type: 'string', nullable: true)]
    private ?string $typeSecurity = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }


    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param string|null $host
     */
    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return string|null
     */
    public function getPort(): ?string
    {
        return $this->port;
    }

    /**
     * @param string|null $port
     */
    public function setPort(?string $port): void
    {
        $this->port = $port;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getTypeSecurity(): ?string
    {
        return $this->typeSecurity;
    }

    /**
     * @param string|null $typeSecurity
     */
    public function setTypeSecurity(?string $typeSecurity): void
    {
        $this->typeSecurity = $typeSecurity;
    }



}
