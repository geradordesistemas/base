<?php

namespace App\Application\Project\SecurityAdminBundle\Repository;

use App\Application\Project\SecurityAdminBundle\Entity\GroupAdmin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupAdmin>
 *
 * @method GroupAdmin|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupAdmin|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupAdmin[]    findAll()
 * @method GroupAdmin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupAdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupAdmin::class);
    }

}
