<?php

namespace App\Application\Project\SecurityUserBundle\Entity;

use App\Application\Project\ContentBundle\Entity\BaseUser;
use App\Application\Project\SecurityUserBundle\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: 'security_user')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User extends BaseUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: false, nullable: false)]
    private string $name;

    #[ORM\ManyToMany(targetEntity: Group::class)]
    #[ORM\JoinTable(name: "security_user_group")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "group_id", referencedColumnName: "id")]
    private $groups;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function setGroups($groups): void
    {
        $this->groups = $groups;
    }

    public function getRoles(): array
    {
        $apiRoles = $this->getApiRoles();
        $webRoles = $this->getWebRoles();

        foreach ($this->getGroups() as $group){
            $apiRoles = array_merge( $apiRoles, $group->getApiRoles() );
            $webRoles = array_merge( $webRoles, $group->getWebRoles() );
        }

        /** Merge de todas as roles do usuário */
        $roles = array_merge( $this->roles, $apiRoles, $webRoles);


        /** Limpa roles repedidas e retorna todas as roles do usuário */
        return array_unique(array_values(array_filter($roles)));;
    }

}