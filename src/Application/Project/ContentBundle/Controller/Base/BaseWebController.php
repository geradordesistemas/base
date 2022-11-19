<?php

namespace App\Application\Project\ContentBundle\Controller\Base;

use App\Application\Project\ContentBundle\Service\WebACL;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseWebController extends AbstractController
{
    public function __construct(
        protected WebACL $webACL)
    {}


    /**
     * Validate access as routes
     * @param string $actionName
     * @return void
     */
    public function validateAccess(string $actionName): void
    {
        if($this->isGranted("ROLE_SUPER_WEB"))
            return;

        $class = new \ReflectionClass($this);

        $roleValidate = '#_ERROR_#';

        foreach ($this->webACL->getWebGroupRoles() as $groupRoles) {

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