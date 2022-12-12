<?php

namespace App\Application\Project\ContentBundle\Controller\Base;

use App\Application\Project\ContentBundle\Service\ApiACL;
use App\Application\Project\ContentBundle\Service\ObjectTransformer;
use App\Application\Project\ContentBundle\Service\SerializerObjects;
use App\Application\Project\MediaBundle\Service\MediaService;
use Doctrine\Persistence\ManagerRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseApiController extends AbstractController
{
    protected SerializerObjects $serializerObjects;
    protected ObjectTransformer $objectTransformer;
    protected MediaService $mediaService;

    public function __construct(
        protected ApiACL $apiACL,
        protected Pool $providerPool,
        protected JWTTokenManagerInterface $JWTTokenManager,
        protected UserPasswordHasherInterface $passwordHasher,
        protected ValidatorInterface $validator,
        protected ManagerRegistry $doctrine,
        ContainerBagInterface $containerInterface,

    )
    {
        $this->serializerObjects = new SerializerObjects(providerPool: $this->providerPool);
        $this->objectTransformer = new ObjectTransformer($this->doctrine, $this->providerPool);
        $this->mediaService = new MediaService($this->doctrine, $containerInterface->get('kernel.project_dir'));
    }

    protected function transformParametersToObject($parameters): object
    {
        $stdClass = (object) $parameters;
        foreach ($stdClass as $index => $property) {
            $stdClass->$index = (object) $property;
        }
        return $stdClass;
    }

    protected function validateJsonRequestBody($requestBody, $parameters): bool|JsonResponse
    {
        $parameters = $this->transformParametersToObject($parameters);

        foreach ($parameters as $parameterName => $parameter){

            $propertyExist = property_exists($requestBody, $parameterName);

            /** Valida se a propriedade existe e se é requisitada  */
            if( !$propertyExist && $parameter->required )
                return $this->createResponseStatus(message: "Invalid content, this property { $parameterName } is required");


            /** Valida se a propriedade existe e pode ser nula ou vazia  */
            if( $propertyExist && !$parameter->nullable && ( $requestBody->$parameterName === "" || $requestBody->$parameterName === null ) )
                return $this->createResponseStatus(message: "Invalid content, this property { $parameterName } can't be empty");


            /** Valida se a propriedade existe possui o tipo requisitado  */
            if( $propertyExist && ( gettype($requestBody->$parameterName) !== $parameter->type ) )
                return $this->createResponseStatus(message: "Invalid content, this property { $parameterName } must be the type { $parameter->type }");




            /*if( property_exists($requestBody, $parameterName) && $requestBody->$parameter === '')
                return $this->createStatusResponse(400,"Invalid content, [" . $parameter . '] is empty');*/
        }

        return false;

    }

    protected function createResponseStatus(string $message, array $extra = [], int $statusCode = 406): JsonResponse
    {
        $array['code'] = $statusCode;
        $array['message'] = $message;
        $array = array_merge( $array, $extra );
        return new JsonResponse($array, $statusCode);
    }


    /**
     * Validate access as routes
     * @param string $actionName
     * @return void
     */
    public function validateAccess(string $actionName): void
    {
        if($this->isGranted("ROLE_SUPER_API"))
            return;

        $class = new \ReflectionClass($this);

        $roleValidate = '#_ERROR_#';


        foreach ($this->apiACL->getApiGroupRoles() as $groupRoles) {

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

    public function validateConstraintErros($object): bool|array
    {
        $errors = $this->validator->validate($object);
        if (!count($errors))
            return false;

        $responseErrors = [
            'message' => 'Os seguintes erros foram encontrados!',
            'errors' => []
        ];

        foreach ($errors as $error){
            $responseErrors['errors'][] = [
                'propertyPath' => $error->getPropertyPath(),
                'message' => $error->getMessage(),
                'invalidValue' => $error->getInvalidValue(),
            ];
        }

        return $responseErrors;
    }


}