<?php

namespace App\Application\Project\ContentBundle\Entity;

use App\Application\Project\ContentBundle\Repository\SmtpEmailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SmtpEmailRepository::class)]
class Content
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    public function getId(): ?int
    {
        return $this->id;
    }
}
