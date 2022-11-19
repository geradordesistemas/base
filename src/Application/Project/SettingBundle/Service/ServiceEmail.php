<?php

namespace App\Application\Project\SettingBundle\Service;

use App\Application\Project\SettingBundle\Entity\SmtpEmail;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class ServiceEmail
{

    public Mailer $mailer;
    protected string $dns;

    public function __construct(
        protected ManagerRegistry $managerRegistry
    )
    {
        $this->getConfiguration();
        $this->configure();
    }

    protected function getConfiguration(): void
    {
        $smtpEmail = $this->managerRegistry->getRepository(SmtpEmail::class)->find(1);

        $user = $smtpEmail->getUsername();
        $password = $smtpEmail->getPassword();
        $host = $smtpEmail->getHost();
        $port = $smtpEmail->getPort();
        $security = $smtpEmail->getTypeSecurity();
        $from = $smtpEmail->getEmail();

        $this->dns = "$user:$password@$host:$port";
    }

    protected function configure(): void
    {
        $transport = Transport::fromDsn($this->dns);
        $this->mailer = new Mailer($transport);
    }

}