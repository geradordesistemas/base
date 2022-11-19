<?php

namespace App\Application\Project\ContentBundle\Service;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class BaseACL
{
    protected ContainerBagInterface $containerInterface;

    public function __construct(ContainerBagInterface $containerInterface)
    {
        $this->containerInterface = $containerInterface;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     * @throws NotFoundExceptionInterface
     */
    protected function getControllerPath($prefix): array
    {
       $bundles = $this->containerInterface->get('kernel.bundles');

        //dd($bundles);

        $controllers = [];
        foreach ($bundles as $bundle) {
            //dump($bundle);
            $reflection = new ReflectionClass($bundle);
            $controllerDirectory = dirname($reflection->getFileName()) . '/Controller';
            if (file_exists($controllerDirectory)) {
                $d = dir($controllerDirectory);
                while (false !== ($entry = $d->read())) {
                    if (preg_match("/^([A-Z0-9-_]+Controller).php/i", $entry, $matches)) {
                        if(str_contains($entry, $prefix)){
                            //$controllers[] = ['fileName' => $controllerDirectory. '/'. $entry, 'class' => $reflection->getNamespaceName() . '\Controller\\' . $matches[1]];
                            $controllers[] = $reflection->getNamespaceName() . '\Controller\\' . $matches[1];

                        }
                    }
                }
                $d->close();
            }
        }

        return array_merge($controllers,  $this->getControllerDefaultPath($prefix));
    }

    protected function getControllerDefaultPath($prefix): array
    {
        $controllerDirectory = $this->containerInterface->get('kernel.project_dir').'/src/Controller';

        $controllers = [];

        if (file_exists($controllerDirectory)) {
            $d = dir($controllerDirectory);
            while (false !== ($entry = $d->read())) {
                if (preg_match("/^([A-Z0-9-_]+Controller).php/i", $entry, $matches)) {
                    if(str_contains($entry, $prefix)){
                        $controllers[] = "App\Controller\\" . str_replace('.php','',$entry);
                    }
                }
            }
            $d->close();
        }
        return $controllers;
    }

}