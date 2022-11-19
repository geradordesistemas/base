<?php

namespace App\Application\Project\ContentBundle\Service;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Sonata\MediaBundle\Provider\Pool;

class SerializerObjects
{
    protected array $mediaProperty = [];
    protected array $galleryProperty = [];
    protected mixed $data;

    public function __construct(
        protected Pool $providerPool
    )
    {}

    /**
     * @throws ReflectionException
     */
    public function normalizer(mixed $data, array $attributes): array
    {
        //dd($data, $attributes);
        $response = [];

        if(is_array($data)){
            foreach ($data as $object) {
                $response[] = $this->startNormalizer($object, $attributes);
            }
        }else{
            $response = $this->startNormalizer($data, $attributes);
        }

        return $response;
    }

    /** @throws ReflectionException */
    protected function startNormalizer($object, $attributes): array
    {
        $data = [];
        $reflectionClass = new ReflectionClass($object);

        foreach ($attributes as $attribute){
            foreach ($reflectionClass->getProperties() as $reflectionProperty){
                if($reflectionProperty->getName() !== $attribute)
                    continue;

                $data[$attribute] = $this->getValueProperty($reflectionProperty, $reflectionClass->getMethods(), $object);

            }
        }

        return $data;
    }


    protected function getValueProperty(ReflectionProperty $reflectionProperty, $reflectionMethods, $object)
    {
        $typeProperty = $this->getTypeProperty( $reflectionProperty->getAttributes() );

        $getterName = $this->getNameMethodGetterProperty($reflectionProperty->getName(), $reflectionMethods);
        if(!$getterName)
            return null;

        //dd($typeProperty, $getterName);
        return match ($typeProperty) {
            'defaultProperty', 'primaryKey' => $this->getValueDefaultProperty($object->$getterName()),
            'media' => $this->getMedia($object->$getterName()),
            'gallery' => $this->getGallery($object->$getterName()),
            'foreingKey' => $this->getForeingKey($object->$getterName()),
            default => null,
        };


    }

####################################################################################################################
####################################################################################################################

    /**
     * @throws ReflectionException
     */
    protected function getForeingKey($objectValue)
    {
        if(!$objectValue)
            return null;

        $className = str_replace("Proxies\__CG__\\","",  get_class($objectValue));

        $classRelationship = new ReflectionClass($className);

        foreach ($classRelationship->getProperties() as $property){
            foreach ($property->getAttributes() as $attribute){
                if($this->isPrimaryKey($attribute) === true){
                    $getterName = $this->getNameMethodGetterProperty($property->getName(), $classRelationship->getMethods());
                    if(!$getterName)
                        return null;

                    return $this->getValueDefaultProperty($objectValue->$getterName());
                    //dd($getterName);

                }
            }
        }

        return null;
    }

    protected function getValueDefaultProperty($objectValue)
    {
        if($objectValue)
            return $objectValue;

        return null;
    }

    protected function getGallery($galleryMedias)
    {
        if(!$galleryMedias)
            return null;

        if(!$galleryMedias->getEnabled())
            return null;

        $gallery = [
            'nome' => $galleryMedias->getName(),
            'arquivos' => []
        ];

        foreach ($galleryMedias->getGalleryItems() as $item){
            if(!$item->getEnabled())
                continue;

            $media = $this->getMedia($item->getMedia());
            if($media)
                $gallery['arquivos'][] =  $media;
        }

        return $gallery;
    }

    protected function getMedia($media): ?array
    {
        if(!$media)
            return null;

        if(!$media->getEnabled())
            return null;

        $url = [];

//        if($media->getContentType() === "video/x-flv")
//            dd($media);

        $provider = $this->providerPool->getProvider($media->getProviderName());
        $formats = $provider->getFormats();

        $url['reference'] =  $provider->generatePublicUrl($media, 'reference');

        foreach ($formats as $formatName => $format){
            if(str_contains($formatName, 'admin'))
                continue;

            $url[$formatName] =  $provider->generatePublicUrl($media, $formatName);
        }

        return [
            'id'                   => $media->getId(),
            'nome'                 => $media->getName(),
            'autor'                => $media->getAuthorName(),
            'descricao'            => $media->getDescription(),
            'width'                => $media->getWidth(),
            'height'               => $media->getHeight(),
            'referencia'           => $media->getProviderReference(),
            'contentType'          => $media->getContentType(),
            'copyright'            => $media->getCopyright(),
            'url'                  => $url,
            'metadata'             => $media->getProviderMetadata(),
        ];
    }



   protected function getNameMethodGetterProperty($propertyName, $reflectionMethods): string
   {
       foreach ($reflectionMethods as $reflectionMethod){
           if( str_contains( strtolower($reflectionMethod->getName()), strtolower($propertyName) ) &&
               str_contains( strtolower($reflectionMethod->getName()), strtolower('get') )
           ){
               return $reflectionMethod->getName();
           }
       }

       return false;
   }



####################################################################################################################
####################################################################################################################

    protected function getTypeProperty($propertyAttributes): string
    {
        //dd($propertyAttributes);
        foreach ($propertyAttributes as $propertyAttribute){

            if($this->isPrimaryKey($propertyAttribute) === true)
                return "primaryKey";

            if($this->isMedia($propertyAttribute) === true)
                return "media";

            if($this->isGallery($propertyAttribute) === true)
                return "gallery";

            if($this->isForeingKey($propertyAttribute) === true)
                return "foreingKey";

        }

        return "defaultProperty";
    }

    protected function isPrimaryKey($propertyAttributes){
        if($propertyAttributes->getName() === "Doctrine\ORM\Mapping\Id")
            return true;

        return false;
    }

    protected function isMedia($propertyAttributes){
        if($propertyAttributes->getName() !== "Doctrine\ORM\Mapping\ManyToOne")
            return false;

        if($propertyAttributes->getArguments()["targetEntity"] === "App\Entity\SonataMediaMedia")
            return true;

        return false;
    }

    protected function isGallery($propertyAttributes){
        if($propertyAttributes->getName() !== "Doctrine\ORM\Mapping\ManyToOne")
            return false;

        if($propertyAttributes->getArguments()["targetEntity"] === "App\Entity\SonataMediaGallery")
            return true;

        return false;
    }

    protected function isForeingKey($propertyAttribute)
    {
        if($propertyAttribute->getName() === "Doctrine\ORM\Mapping\ManyToOne")
            return true;

        if($propertyAttribute->getName() === "Doctrine\ORM\Mapping\OneToMany")
            return true;

        if($propertyAttribute->getName() === "Doctrine\ORM\Mapping\OneToOne")
            return true;

        if($propertyAttribute->getName() === "Doctrine\ORM\Mapping\ManyToMany")
            return false;
        //dd($propertyAttribute);


        return false;
    }

####################################################################################################################
####################################################################################################################


































    /**
     * @throws ReflectionException
     */
    protected function findMediaFieldRecursive(ReflectionClass $reflection, $attributes, $parent = ""): void
    {
        foreach ($attributes as $index => $attribute) {

            if(!is_array($attribute)){
                $property = $reflection->getProperty($attribute);

                //dd($property);
                foreach ($property->getAttributes() as $propertyAttribute){

                    if($this->isMedia($propertyAttribute)){
                        if( $parent ){
                            $this->mediaProperty[$parent] = $property->getName();
                        }else{
                            $this->mediaProperty[] = $property->getName();
                        }
                    }

                    if($this->isGallery($propertyAttribute)){
                        if( $parent ){
                            $this->galleryProperty[$parent] = $property->getName();
                        }else{
                            $this->galleryProperty[] = $property->getName();
                        }
                    }

                }
            }

            if(is_array($attribute)){
                $property = $reflection->getProperty($index);

                foreach ($property->getAttributes() as $propertyAttribute){
                    $targetEntity = $this->getTargetEntity($propertyAttribute);
                    if($targetEntity){
                        //dd($targetEntity);
                        $targetEntity = new ReflectionClass($targetEntity);
                        $this->findMediaFieldRecursive($targetEntity, $attribute, $index);
                    }
                }
                //dd($index, $attribute, $property);

            }

        }
    }

    protected function getTargetEntity($propertyAttributes)
    {
        //dd($propertyAttributes);
        if( !str_contains($propertyAttributes->getName(),  "Doctrine\ORM\Mapping\\") )
            return false;

        if(!isset($propertyAttributes->getArguments()["targetEntity"]))
            return false;

        if($propertyAttributes->getArguments()["targetEntity"])
            return $propertyAttributes->getArguments()["targetEntity"];

        return false;
    }




    protected function serializerMedia($response): string|int|bool|\ArrayObject|array|null|float
    {
        foreach ($this->mediaProperty as $index => $property) {
            if(is_int($index)){
                $name = ucfirst( $property );
                if (method_exists($this->data, ($method = 'get'.$name))){
                    $response[$property] = $this->getMediaUrl($this->data->$method());
                }
            }

            if(is_string($index)){
                $name = ucfirst( $index );
                if (method_exists($this->data, ($method = 'get'.$name))){
                    $name2 = ucfirst( $property );
                    if (method_exists($this->data->$method(), ($method2 = 'get'.$name2))){
                        $response[$index][$property] = $this->getMediaUrl($this->data->$method()->$method2());
                    }
                }
            }

        }

        return $response;
    }









}