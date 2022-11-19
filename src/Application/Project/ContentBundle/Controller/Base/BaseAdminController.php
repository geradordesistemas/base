<?php

namespace App\Application\Project\ContentBundle\Controller\Base;

use App\Application\Project\ContentBundle\Service\AdminACL;
use App\Application\Project\ContentBundle\Service\ApiACL;
use App\Application\Project\ContentBundle\Service\WebACL;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\AdminBundle\Bridge\Exporter\AdminExporter;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseAdminController extends CRUDController
{

    public function __construct(
       protected AdminACL $adminACL,
       protected ApiACL $apiACL,
       protected WebACL  $webACL,
        protected ManagerRegistry $managerRegistry,
    )
    {}


    /**
     * Validate access as routes
     * @param string $actionName
     * @return void
     */
    public function validateAccess(string $actionName): void
    {
        if($this->isGranted("ROLE_SUPER_ADMIN"))
            return;

        $class = new \ReflectionClass($this);

        $roleValidate = '#_ERROR_#';

        foreach ($this->adminACL->getAdminGroupRoles() as $groupRoles) {

            if($class->getName() !== $groupRoles['controllerNamespace'])
                continue;

            foreach ($groupRoles['routes'] as $route){
                if($actionName !== $route['router'])
                    continue;

                $roleValidate = $route['role'];
            }

        }

        $this->denyAccessUnlessGranted($roleValidate);
    }

}