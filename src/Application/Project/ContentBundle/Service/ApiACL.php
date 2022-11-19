<?php

namespace App\Application\Project\ContentBundle\Service;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ApiACL extends BaseACL
{
    private string $acl = 'Api';
    private string $aclBase = "App\Application\Project\ContentBundle\Attributes\Acl\\";

    public function __construct(ContainerBagInterface $containerInterface)
    {
        parent::__construct($containerInterface);

    }

    private function getSuperApiRole(): array
    {
        return [
            "title" => "Super Administrador API",
            "description" => "Tem Acesso a todos os Módulos API.",
            "controllerName"=> "",
            "controllerNamespace"=> "",
            "routes" => [[
                "router" => "AllActions",
                "role"=> "ROLE_SUPER_API",
                "title" => "Permissão Total ",
                "description" => "Tem Acesso a todos os Módulos API.",
            ]]
        ];
    }

    public function getApiGroupRoles(): array
    {
        return array_merge([$this->getSuperApiRole()], $this->start());
    }

    public function getApiRoles(): array
    {
        $roles = [];
        foreach ($this->start() as $action) {
            foreach ($action['routes'] as $route) {
                $roles[] = $route['role'];
            }
        }
        return array_merge(['ROLE_SUPER_API'], $roles);
    }


    /**
     * @throws ReflectionException
     */
    private function start(): array
    {
        $controllers = $this->getControllerPath($this->acl . 'Controller');

        //dump($controllers);

        $configuration = [];
        foreach ($controllers as $index => $controller) {

            $reflection = new ReflectionClass($controller);

            /** Percorre Todos os Atributos da Classe */
            $controllerConfig = $this->getHeaderConfig($reflection->getAttributes(), $controller);
            if( $controllerConfig === false )
                continue;

            /** Percorre Todos os metodos da Classe */
            foreach ($reflection->getMethods() as $method) {

                $router = $this->getRouterConfig($method);
                if(!$router)
                    continue;

                $controllerConfig['routes'][] = $router;
            }

            $configuration[] = $controllerConfig;
        }

        return $configuration;
    }



    private function getHeaderConfig($attributes, $controllerNamespace): bool|array
    {
        $headerConfig = [];
        foreach ($attributes as $attribute) {
            if($attribute->getName() === $this->aclBase . $this->acl){

                $args = $attribute->getArguments();

                if(!$args['enable'])
                    return false;

                if( isset($args['title']) )
                    $headerConfig['title'] = $args['title'];

                if( isset($args['description']) )
                    $headerConfig['description'] = $args['description'];

                if( isset($controllerNamespace) ){
                    $arrayNamespace = explode('\\', $controllerNamespace);
                    $headerConfig['controllerName'] = end($arrayNamespace) ;
                    $headerConfig['controllerNamespace'] = $controllerNamespace;
                }

                return $headerConfig;
            }
        }

        return false;
    }


    private function getRouterConfig(ReflectionMethod $method): bool|array
    {
        if(!str_contains($method->getName(), 'Action'))
            return false;

        $routerConfig = [];

        //dump($method);

        foreach ($method->getAttributes() as $attribute) {
            if($attribute->getName() === $this->aclBase . $this->acl ){

                $args = $attribute->getArguments();

                if(!$args['enable'])
                    return false;

                $routerConfig['role'] = $this->createRoleName($method->class, $method->getName());

                $routerConfig['router'] = $method->getName();

                if( isset($args['title']) )
                    $routerConfig['title'] = $args['title'];

                if( isset($args['description']) )
                    $routerConfig['description'] = $args['description'];

                return $routerConfig;
            }
        }


        return false;
    }


    private function createRoleName($nameSpace, $router): string
    {
        $role = "ROLE_" . $this->acl . "_";
        $arrayNamespace = explode('\\', $nameSpace);

        //dump($arrayNamespace);

        if(count($arrayNamespace) > 3){
            $role .= $arrayNamespace[2] . "_" .
                str_replace('Bundle', '', $arrayNamespace[3]) . "_" .
                str_replace($this->acl . 'Controller', '', $arrayNamespace[5]) . "_" .
                str_replace('Action', '', $router);
        }else{
            $role .= str_replace($this->acl . 'Controller', '', $arrayNamespace[2]);
        }

        return strtoupper($role);
    }



}