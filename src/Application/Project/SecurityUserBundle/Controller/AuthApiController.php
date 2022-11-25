<?php

namespace App\Application\Project\SecurityUserBundle\Controller;

use App\Application\Project\ContentBundle\Controller\Base\BaseApiController;
use App\Application\Project\SecurityUserBundle\Entity\User;
use ReflectionException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;


#[OA\Tag(name: 'Autenticação')]
#[Route('/api/usuario', name: 'api_auth_usuario')]
class AuthApiController extends BaseApiController
{

    /**
     * Recupera Token JWT — Usuario.
     * Recupera Token JWT — Usuario.
     */
    #[OA\Response(
        response: 200,
        description: 'Return Token JWT',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'token', description: 'Token JWT', type: 'string', nullable: false),
            ],
            type: 'object'
        )
    )]
    #[OA\RequestBody(
        description: 'Json Payload',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email',  type: 'string', nullable: false),
                new OA\Property(property: 'password', type: 'string', nullable: false)
            ],
            type: 'object'
        )
    )]
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function loginAction(Request $request): JsonResponse
    {
        $entityManager = $this->doctrine->getManager();

        $parameters = [
            'email'     => [ 'type' => 'string', 'required' => true, 'nullable' => false ],
            'password'  => [ 'type' => 'string', 'required' => true, 'nullable' => false ],
        ];

        $requestBody = json_decode($request->getContent());

        if($this->validateJsonRequestBody($requestBody, $parameters))
            return $this->validateJsonRequestBody($requestBody, $parameters);

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $requestBody->email]);

        if(!$user || !$this->passwordHasher->isPasswordValid($user, $requestBody->password))
            return $this->createResponseStatus(message: 'Credenciais invalidas!');

        $token = $this->JWTTokenManager->create($user);

        return $this->json(['token' => $token]);
    }


    /**
     * Recupera Recurso Autenticado — Usuario.
     * Recupera Recurso Autenticado — Usuario.
     * @throws ReflectionException
     */
    #[OA\Response(
        response: 200,
        description: 'Return authenticated user',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'roles', type: 'object'),
            ],
            type: 'object'
        )
    )]
    #[Route('/usuario_autenticado', name: 'user_authenticated', methods: ['GET'])]
    public function userAuthenticatedAction(Request $request): JsonResponse
    {
        $this->validateAccess("IS_AUTHENTICATED_FULLY");

        $user = $this->getUser();

        $response = $this->serializerObjects->normalizer($user, [
            'id', 'name','email','roles',
        ]);

        /*$serializer = new Serializer([new ObjectNormalizer()]);
        $data = $serializer->normalize($user, null, [AbstractNormalizer::ATTRIBUTES => [
            'id', 'name', 'email', 'roles',
            'groups' => ['id', 'name', 'description']
        ] ]);*/

        return $this->json([
            '@id' => $request->getPathInfo(),
            'result' => $response,
        ]);
    }



}