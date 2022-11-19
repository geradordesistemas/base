<?php

namespace App\Application\Project\SecurityUserBundle\Command;

use App\Application\Project\SecurityUserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'security:create-user',
    description: 'Cria um novo usuário padrão!',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected UserPasswordHasherInterface $passwordHasher,
        string $name = null,
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('nome', InputArgument::OPTIONAL, 'Nome do usuário!')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email do usuário!')
            ->addArgument('senha', InputArgument::OPTIONAL, 'Senha do usuário!')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $nome = $input->getArgument('nome');
        $email = $input->getArgument('email');
        $senha = $input->getArgument('senha');

        if (!$nome) {
            $io->note(sprintf('Informe o nome do usuário!'));
            exit;
        }

        if (!$email) {
            $io->note(sprintf('Informe o email do usuário!'));
            exit;
        }

        if (!$senha) {
            $io->note(sprintf('Informe a senha do usuário!'));
            exit;
        }

        $em = $this->entityManager;

        $user = $em->getRepository(User::class)->findOneBy(['email'=>$email]);
        if($user){
            $io->note(sprintf('Email já cadastrado no sistema!'));
            exit;
        }

        $user = new User();
        $user->setName($nome);
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $senha));
        $user->setWebRoles(['ROLE_SUPER_WEB']);
        $user->setApiRoles(['ROLE_SUPER_API']);

        $em->persist($user);
        $em->flush();

        $io->success('Usuário criado com sucesso!');

        return Command::SUCCESS;
    }
}
