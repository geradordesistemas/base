<?php

namespace App\Application\Project\ContentBundle\Admin\Base;

use App\Application\Project\ContentBundle\Service\AdminACL;
use App\Application\Project\ContentBundle\Service\ApiACL;
use App\Application\Project\ContentBundle\Service\RolesIdentifierService;
use App\Application\Project\ContentBundle\Service\WebACL;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class BaseAdmin extends AbstractAdmin
{
    protected UserPasswordHasherInterface $passwordHasher;
    protected ?UserInterface $user = null;
    protected AdminACL $adminACL;
    protected ApiACL $apiACL;
    protected WebACL $webACL;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        Security $security,
        ContainerBagInterface $containerInterface,
    )
    {
        parent::__construct();

        /** Não Remove chamada */
        $this->removeAclCacheSonataAdmin($containerInterface->get('kernel.project_dir'));

        $this->user = $security->getUser();
        $this->passwordHasher = $passwordHasher;
        $this->adminACL = new AdminACL($containerInterface);
        $this->apiACL = new ApiACL($containerInterface);
        $this->webACL = new WebACL($containerInterface);
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        //parent::configureRoutes($collection);

        /** Não Remove chamada - Faz controle de acesso painel admin */
        $this->removeRoutesByPermission($this->getBaseControllerName(), $collection);
    }

    /**
     * Remove Todas as rotas que o usuário não possui permissão
     *
     * @param $controllerPath
     * @param RouteCollectionInterface $collection
     * @return bool
     */
    protected function removeRoutesByPermission($controllerPath, RouteCollectionInterface $collection): bool
    {
        if(is_null($this->user))
            return false;

        if(in_array('ROLE_SUPER_ADMIN', $this->user->getRoles()))
            return false;

        $adminGroupRoles = $this->adminACL->getAdminGroupRoles();

        $routesRemove = [];
        $routesPermission = [];
        foreach ($adminGroupRoles as $adminGroupRole){
            if( !isset($adminGroupRole['controllerNamespace']) )
                continue;

            if($adminGroupRole['controllerNamespace'] !== $controllerPath)
                continue;

            foreach ($adminGroupRole['routes'] as $groupRoutes){
                if( !in_array($groupRoutes['role'], $this->user->getRoles() ) ){
                    $routesRemove[] = str_replace('Action', '', $groupRoutes['router']);
                }else{
                    $routesPermission[] = str_replace('Action', '', $groupRoutes['router']);
                }
            }

        }

        if(!empty($routesRemove)){
            foreach ($routesRemove as $remove)
                $collection->remove($remove);
        }else if (empty($routesPermission)){
            $collection->clear();
        }

        return true;
    }


    /**
     * Remove os arquivos de cache da parte de ACL do Sonata Admin.
     * Sonata coloca em cache as definições de acl da parte visual do menu, consequencia
     * a parte visual do sonata fica a mesma para todos os usuários
     *
     * @param $projectDir
     * @return void
     */
    private function removeAclCacheSonataAdmin($projectDir){
        $cacheDirDev = $projectDir . '/var/cache/dev/sonata/';
        $cacheDirProd = $projectDir . '/var/cache/prod/sonata/';
        $this->removeRecursiveDir($cacheDirDev);
        $this->removeRecursiveDir($cacheDirProd);
    }

    /**
     * Remove um diretorio recursivamente
     *
     * @param $dir - fullpath
     * @return void
     */
    public function removeRecursiveDir($dir) {
        if(!is_dir($dir))
            return;

        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->removeRecursiveDir("$dir/$file") : unlink("$dir/$file");
        }
    }
}