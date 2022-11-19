<?php

namespace App\Application\Project\ContentBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

class BaseEntity
{
    #[ORM\Column(name: 'created_at', type: "datetime", nullable: true, options: ["default" => "CURRENT_TIMESTAMP"])]
    protected DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: "datetime", nullable: true, options: ["default" => "CURRENT_TIMESTAMP"])]
    protected DateTime $updatedAt;

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