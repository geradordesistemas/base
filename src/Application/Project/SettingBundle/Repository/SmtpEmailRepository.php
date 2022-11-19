<?php

namespace App\Application\Project\SettingBundle\Repository;

use App\Application\Project\SettingBundle\Entity\SmtpEmail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SmtpEmail>
 *
 * @method SmtpEmail|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmtpEmail|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmtpEmail[]    findAll()
 * @method SmtpEmail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmtpEmailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmtpEmail::class);
    }

}
