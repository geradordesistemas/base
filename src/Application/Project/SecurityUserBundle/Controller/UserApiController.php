<?php

namespace App\Application\Project\SecurityUserBundle\Controller;

use App\Application\Project\SecurityUserBundle\Entity\User;

use App\Application\Project\ContentBundle\Controller\Base\BaseApiController;
use App\Application\Project\ContentBundle\Service\FilterDoctrine;
use App\Application\Project\ContentBundle\Attributes\Acl as ACL;
use Doctrine\Persistence\ObjectRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Query\QueryException;
use ReflectionException;

#[Route('/api/usuario', name: 'api_usuario_')]
#[OA\Tag(name: 'Usuario')]
#[ACL\Api(enable: true, title: 'Usuário', description: 'Permissões do modulo Usuário')]
class UserApiController extends BaseApiController
{
    public function getClass(): string
    {
        return User::class;
    }

    public function getRepository(): ObjectRepository
    {
        return $this->doctrine->getManager()->getRepository($this->getClass());
    }

    /**
     * Recupera a coleção de recursos — Usuario.
     * Recupera a coleção de recursos — Usuario.
     * @throws QueryException|ReflectionException
     */
    #[OA\Parameter( name: 'pagina', description: 'O número da página da coleção', in: 'query', required: false, allowEmptyValue: true, example: 1)]
    #[OA\Parameter( name: 'paginaTamanho', description: 'O tamanho da página da coleção', in: 'query', required: false, example: 10)]
    #[OA\Response(
        response: 200,
        description: 'Return list',
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
    #[Route('', name: 'list', methods: ['GET'])]
    #[ACL\Api(enable: true, title: 'Listar', description: 'Listar Usuário')]
    public function listAction(Request $request): Response
    {
        $this->validateAccess(actionName: "listAction");

        $filter = new FilterDoctrine(
            repository:  $this->getRepository(),
            request: $request,
            attributesFilters: ['id', 'name','email','roles',],
        );

        $response = $this->serializerObjects->normalizer($filter->getResult()->data, [
            'id', 'name','email','roles',
        ]);

        return $this->json([
            '@id' => $request->getPathInfo(),
            'result' => $response,
            'paginator' => $filter->getResult()->paginator,
        ]);

    }

    /** ****************************************************************************************** */
    /**
     * Cria o Recurso — Usuario.
     * Cria o Recurso — Usuario.
     */
    #[OA\Response(
        response: 200,
        description: 'Return list',
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
    #[OA\RequestBody(
        description: 'Json Payload',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
            ],
            type: 'object'
        )
    )]
    #[Route('', name: 'create', methods: ['POST'])]
    #[ACL\Api(enable: true, title: 'Criar', description: 'Criar Usuário')]
    public function createAction(Request $request): Response
    {
        $this->validateAccess("createAction");

        /** Pega o corpo da requisição e faz valida se não é nulo */
        $requestBody = json_decode($request->getContent());
        if(!$requestBody)
            return $this->json(['status' => false, 'message' => 'Corpo da requisição invalido!'], 400);

        $user = new User();
        if(isset($requestBody->name))
            $user->setName($requestBody->name);

        if(isset($requestBody->email))
            $user->setEmail($requestBody->email);

        if(isset($requestBody->password))
            $user->setPassword( $this->passwordHasher->hashPassword($user, $requestBody->password) );

        $user->setApiRoles(['ROLE_SUPER_API']);
        $user->setWebRoles(['ROLE_SUPER_WEB']);

        /** Faz validação da instância da classe! */
        $errorMessage = $this->validateConstraintErros($this->validator, $user);
        if($errorMessage)
            return $this->json($errorMessage, 400);

        /** Faz a persistência dos dados! */
        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        $response = $this->serializerObjects->normalizer($user, [
            'id', 'name','email','roles',
        ]);

        return $this->json([
            '@id' => $request->getPathInfo(),
            'result' => $response,
        ]);

    }

    /** ****************************************************************************************** */
    /**
     * Recupera o recurso — Usuario.
     * Recupera o recurso — Usuario.
     * @throws ReflectionException
     */
    #[OA\Parameter( name: 'id', description: 'Identificador do recurso', in: 'path')]
    #[OA\Response(
        response: 200,
        description: 'Return list',
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
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[ACL\Api(enable: true, title: 'Visualizar', description: 'Visualizar Usuário')]
    public function showAction(Request $request, int $id): Response
    {
        $this->validateAccess("showAction");

        $data = $this->getRepository()->find($id);
        if (!$data)
            return $this->json(['status' => false, 'message' => 'Usuário não encontrado!'], 404);

        $response = $this->serializerObjects->normalizer($data, [
            'id', 'name','email','roles',
        ]);

        return $this->json([
            '@id' => $request->getPathInfo(),
            'result' => $response,
        ]);
    }

    /** ****************************************************************************************** */
    /**
     * Substitui o recurso — Usuario.
     * Substitui o recurso — Usuario.
     */
    #[OA\Response(
        response: 200,
        description: 'Return list',
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
    #[OA\RequestBody(
        description: 'Json Payload',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
            ],
            type: 'object'
        )
    )]
    #[Route('/{id}', name: 'edit', methods: ['PUT','PATCH'])]
    #[ACL\Api(enable: true, title: 'Editar', description: 'Editar Usuário')]
    public function editAction(Request $request, $id): Response
    {
        $this->validateAccess("editAction");

        /** Pega o corpo da requisição e faz valida se não é nulo */
        $requestBody = json_decode($request->getContent());
        if(!$requestBody)
            return $this->json(['status' => false, 'message' => 'Corpo da requisição invalido!'], 400);

        $user = $this->getRepository()->find($id);
        if (!$user)
            return $this->json(['status' => false, 'message' => 'Usuário não encontrado!'], 404);

        if(isset($requestBody->name))
            $user->setName($requestBody->name);

        if(isset($requestBody->email))
            $user->setEmail($requestBody->email);

        if(isset($requestBody->password))
            $user->setPassword( $this->passwordHasher->hashPassword($user, $requestBody->password) );

        /** Faz validação da instância da classe! */
        $errorMessage = $this->validateConstraintErros($this->validator, $user);
        if($errorMessage)
            return $this->json($errorMessage, 400);

        /** Faz a persistência dos dados! */
        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        $response = $this->serializerObjects->normalizer($user, [
            'id', 'name','email','roles',
        ]);

        return $this->json([
            '@id' => $request->getPathInfo(),
            'result' => $response,
        ]);

    }

    /** ****************************************************************************************** */
    /**
     * Remove o recurso — Usuario.
     * Remove o recurso — Usuario.
     */
    #[OA\Parameter( name: 'id', description: 'Identificador do recurso', in: 'path')]
    #[OA\Response(response: 204, description: 'Recurso excluído')]
    #[OA\Response(response: 404, description: 'Recurso não encontrado')]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[ACL\Api(enable: true, title: 'Deletar', description: 'Deletar Usuário')]
    public function deleteAction(int $id): Response
    {
        $this->validateAccess("deleteAction");

        $object = $this->getRepository()->find($id);
        if (!$object)
            return $this->json(['status' => false, 'message' => 'Usuário não encontrado.'], 404);

        $em = $this->doctrine->getManager();
        $em->remove($object);
        $em->flush();

        return $this->json(['status' => true, 'message' => 'Usuário removido com sucesso.'], 204);
    }

}