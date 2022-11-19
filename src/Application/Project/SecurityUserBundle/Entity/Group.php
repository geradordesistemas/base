<?php

namespace App\Application\Project\SecurityUserBundle\Entity;

use App\Application\Project\SecurityUserBundle\Repository\GroupRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Table(name: 'security_group')]
#[ORM\Entity(repositoryClass: GroupRepository::class)]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true, nullable: false)]
    private string $name;

    #[ORM\Column(length: 180, unique: false, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'api_roles', unique: false)]
    private array $apiRoles = [];

    #[ORM\Column(name: 'web_roles', unique: false)]
    private array $webRoles = [];

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
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
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
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getApiRoles(): array
    {
        return $this->apiRoles;
    }

    public function setApiRoles(array $apiRoles): void
    {
        $this->apiRoles = $apiRoles;
    }

    public function getWebRoles(): array
    {
        return $this->webRoles;
    }

    public function setWebRoles(array $webRoles): void
    {
        $this->webRoles = $webRoles;
    }

}
