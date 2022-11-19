<?php

namespace App\Application\Project\SecurityAdminBundle\Entity;

use App\Application\Project\ContentBundle\Entity\BaseAdminUser;
use App\Application\Project\SecurityAdminBundle\Repository\UserAdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: 'security_admin_user')]
#[ORM\Entity(repositoryClass: UserAdminRepository::class)]
class UserAdmin extends BaseAdminUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: false, nullable: false)]
    private string $name;

    #[ORM\ManyToMany(targetEntity: GroupAdmin::class)]
    #[ORM\JoinTable(name: "security_admin_user_group")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "group_id", referencedColumnName: "id")]
    private $groups;

    #[ORM\Column(name: 'admin_roles', unique: false)]
    private array $adminRoles = [];

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
        /**  Roles de todos os grupos que o usuário participa */
        $groupRoles = [];
        foreach ($this->getGroups() as $group)
            $groupRoles = array_merge( $groupRoles, $group->getRoles() );

        /** Merge de todas as roles do usuário */
        $roles = array_merge( $this->roles, $this->adminRoles, $groupRoles);

        /** Limpa roles repedidas e retorna todas as roles do usuário */
        return array_unique(array_values(array_filter($roles)));
    }

    public function getAdminRoles(): array
    {
        return $this->adminRoles;
    }

    public function setAdminRoles(array $adminRoles): void
    {
        $this->adminRoles = $adminRoles;
    }


}