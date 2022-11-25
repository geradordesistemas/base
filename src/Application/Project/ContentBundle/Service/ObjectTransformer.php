<?php

namespace App\Application\Project\ContentBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use ReflectionClass;
use ReflectionObject;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ObjectTransformer
{

    public function __construct(
        protected ManagerRegistry $doctrine,
    )
    {
    }


    public function JsonToObject(string|object $class, Request $request, array $attributes): object
    {
        $body = json_decode( $request->getContent() );

        $object = $this->cast($class, $body, $attributes);

       // dd($object);

        return $object;
    }


    /**
     * Class casting
     *
     * @param string|object $destination - Classe de Destino
     * @param object $sourceObject - Classe de Origem - Request Body
     * @param array $attributes - Atributos a serem mapeados
     * @return object|string
     */
    function cast(string|object $destination, object $sourceObject, array $attributes): object|string
    {
        if (is_string($destination))
            $destination = new $destination();

        /** Classe de Origem - Request */
        $sourceReflection = new ReflectionObject($sourceObject);

        /** Classe de Destino - Request */
        $destinationReflection = new ReflectionObject($destination);

        /** Propriedades da Classe de Destino */
        $sourceProperties = $sourceReflection->getProperties();

        /** Percorre todas as propriedades da Classe de Destino */
        foreach ($sourceProperties as $sourceProperty) {

            /** Verifica se a propriedade da classe de destino pode ser mapeados  */
            if(!in_array($sourceProperty->name, $attributes))
                continue;

            $sourceProperty->setAccessible(true);

            /** nome da propriedade da Classe de Origem */
            $name = $sourceProperty->getName();

            /** valor da propriedade da Classe de Origem */
            $value = $sourceProperty->getValue($sourceObject);
            //dd($name, $value);

            if ($destinationReflection->hasProperty($name)) {

                /** Pega a propriedade de destino  */
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);

                /** Caso seja um relacionamento, traz o targetEntity e se é uma collection ou entity */
                $relationship = $this->getTypeRelationShip($propDest);

                if($relationship){
                    //dd($relationship);

                    /** Caso a propriedade seja um relacionamento com outra classe */
                    if($relationship->type === "entity"){
                        $value = $this->doctrine->getRepository($relationship->targetEntity)
                            ->findOneBy([$relationship->targetPrimaryKey => $value]);
                    }

                    /** Caso a propriedade seja um CollectionType */
                    if($relationship->type === "collection"){
                        $collection = [];
                        if(is_array($value)){
                            foreach ($value as $val){
                                $val = $this->doctrine->getRepository($relationship->targetEntity)
                                    ->findOneBy([$relationship->targetPrimaryKey => $val]);
                                if($val)
                                    $collection[] = $val;
                            }


                        }
                        $value = new ArrayCollection($collection);
                    }
                }

                $propDest->setValue($destination, $value);

            } else {
                $destination->$name = $value;
            }

        }

        return $destination;
    }


    protected function getTypeRelationShip(ReflectionProperty $reflectionProperty): bool|object
    {
        //dd($reflectionProperty);
        $relationship = false;

        foreach ($reflectionProperty->getAttributes() as $reflectionAttribute) {

            $base = "Doctrine\ORM\Mapping\\";
            $entity = [ $base."OneToOne", $base."ManyToOne" ];
            $collection = [ $base."ManyToMany", $base."OneToMany" ];

            if(in_array($reflectionAttribute->getName(), $entity))
                $relationship = (object)[
                    'type' => 'entity',
                    'targetEntity' => $reflectionAttribute->getArguments()["targetEntity"],
                ];

            if(in_array($reflectionAttribute->getName(), $collection))
                $relationship = (object)[
                    'type' => 'collection',
                    'targetEntity' => $reflectionAttribute->getArguments()["targetEntity"],
                ];

        }

        if($relationship)
        {
            /** Pega a propriade chave primaria da classe relacionada! */
            $targetReflection = new ReflectionClass($relationship->targetEntity);
            foreach ($targetReflection->getProperties() as $reflectionProperty)
                foreach ($reflectionProperty->getAttributes() as $reflectionAttribute)
                    if($reflectionAttribute->getName() === "Doctrine\ORM\Mapping\Id")
                        $relationship->targetPrimaryKey = $reflectionProperty->getName();
        }


        return $relationship;
    }





    public function ObjectToJson(object|array $data, array $attributes): array
    {
        //dd($attributes);

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();
        $serializer = new Serializer([$normalizer], [$encoder]);

        $response = $serializer->normalize( $data, 'json', [
            AbstractNormalizer::ATTRIBUTES => $attributes,
            'circular_reference_limit' => 5,
        ]);

        return $response;
    }


}