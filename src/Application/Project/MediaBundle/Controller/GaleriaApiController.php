<?php

namespace App\Application\Project\MediaBundle\Controller;

use App\Application\Project\ContentBundle\Controller\Base\BaseApiController;
use App\Application\Project\ContentBundle\Service\FilterDoctrine;
use App\Application\Project\ContentBundle\Attributes\Acl as ACL;
use App\Application\Project\SecurityUserBundle\Entity\User;
use App\Entity\SonataMediaGallery;
use Doctrine\Persistence\ObjectRepository;
use Nelmio\ApiDocBundle\Model\Model;
use OpenApi\Attributes as OA;
use OpenApi\Annotations as OAA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Query\QueryException;
use ReflectionException;

#[Route('/api/galeria', name: 'api_galeria_')]
#[OA\Tag(name: 'Galeria')]
#[ACL\Api(enable: true, title: 'Galeria', description: 'Permissões do modulo Galeria')]
class GaleriaApiController extends BaseApiController
{
    public function getClass(): string
    {
        return SonataMediaGallery::class;
    }

    public function getRepository(): ObjectRepository
    {
        return $this->doctrine->getManager()->getRepository($this->getClass());
    }

    /**
     * Recupera a coleção de recursos — Galeria.
     * Recupera a coleção de recursos — Galeria.
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
                new OA\Property(property: 'enabled', type: 'boolean'),
                new OA\Property(property: 'context', type: 'string'),
                new OA\Property(property: 'midias', type: 'object'),
            ],
            type: 'object'
        )
    )]
    #[Route('', name: 'list', methods: ['GET'])]
    #[ACL\Api(enable: true, title: 'Listar', description: 'Listar Galeria')]
    public function listAction(Request $request): Response
    {
        $this->validateAccess(actionName: "listAction");

        $filter = new FilterDoctrine(
            repository:  $this->getRepository(),
            request: $request,
            attributesFilters: [
                'id', 'name', 'enabled', 'context',
            ]);

        //dd($filter->getResult()->data);

        $response = $this->objectTransformer->ObjectToJson( $filter->getResult()->data,[
            'id', 'name', 'enabled', 'context',
        ]);

        return $this->json([
            'resultado' => $response,
            'paginacao' => $filter->getResult()->paginator,
        ]);

    }

    /** ****************************************************************************************** */
    /**
     * Cria o Recurso — Galeria.
     * Cria o Recurso — Galeria.
     */
    #[OA\Response(
        response: 200,
        description: 'Return list',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'enabled', type: 'boolean'),
                new OA\Property(property: 'context', type: 'string'),
                new OA\Property(property: 'midias', type: 'object'),
            ],
            type: 'object'
        )
    )]
    #[OA\RequestBody(
        description: 'Json Payload',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'enabled', type: 'boolean'),
                new OA\Property(property: 'context', type: 'string'),
                new OA\Property(property: 'midias', type: 'object'),
            ],
            type: 'object'
        )
    )]
    #[Route('', name: 'create', methods: ['POST'])]
    #[ACL\Api(enable: true, title: 'Criar', description: 'Criar Galeria')]
    public function createAction(Request $request): Response
    {
        $this->validateAccess("createAction");

        if(!$request->getContent())
            return $this->json(['status' => false, 'message' => 'Dados inválidos!'], 400);

        $object = $this->objectTransformer->JsonToObject( $this->getClass(), $request , [
            'id', 'name', 'enabled', 'context',
        ]);

        $errors = $this->validateConstraintErros($object);
        if($errors)
            return $this->json($errors);

        $em = $this->doctrine->getManager();
        $em->persist($object);
        $em->flush();

        $response = $this->objectTransformer->ObjectToJson( $object, [
            'id', 'name', 'enabled', 'context',
        ]);

        return $this->json($response, 201);
    }

    /** ****************************************************************************************** */
    /**
     * Recupera o recurso — Galeria.
     * Recupera o recurso — Galeria.
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
                new OA\Property(property: 'enabled', type: 'boolean'),
                new OA\Property(property: 'context', type: 'string'),
                new OA\Property(property: 'midias', type: 'object'),
            ],
            type: 'object'
        )
    )]
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[ACL\Api(enable: true, title: 'Visualizar', description: 'Visualizar Galeria')]
    public function showAction(Request $request, int $id): Response
    {
        $this->validateAccess("showAction");

        $data = $this->getRepository()->find($id);
        if (!$data)
            return $this->json(['status' => false, 'message' => 'Galeria não encontrado!'], 404);

        $response = $this->objectTransformer->ObjectToJson( $data, [
            'id', 'name', 'enabled', 'context',
        ]);

        return $this->json($response);
    }

    /** ****************************************************************************************** */
    /**
     * Substitui o recurso — Galeria.
     * Substitui o recurso — Galeria.
     */
    #[OA\Response(
        response: 200,
        description: 'Return list',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'enabled', type: 'boolean'),
                new OA\Property(property: 'context', type: 'string'),
                new OA\Property(property: 'midias', type: 'object'),
            ],
            type: 'object'
        )
    )]
    #[OA\RequestBody(
        description: 'Json Payload',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'enabled', type: 'boolean'),
                new OA\Property(property: 'context', type: 'string'),
                new OA\Property(property: 'midias', type: 'object'),
            ],
            type: 'object'
        )
    )]
    #[Route('/{id}', name: 'edit', methods: ['PUT', 'PATCH'])]
    #[ACL\Api(enable: true, title: 'Editar', description: 'Editar Galeria')]
    public function editAction(Request $request, $id): Response
    {
        $this->validateAccess("editAction");

        if(!$request->getContent())
            return $this->json(['status' => false, 'message' => 'Dados inválidos!'], 400);

        $object = $this->getRepository()->find($id);
        if(!$object)
            return $this->json(['status' => false, 'message' => 'Galeria não encontrado!'], 404);

        $object = $this->objectTransformer->JsonToObject( $object, $request , [
            'id', 'name', 'enabled', 'context',
        ]);

        $errors = $this->validateConstraintErros($object);
        if($errors)
            return $this->json($errors);

        $em = $this->doctrine->getManager();
        $em->persist($object);
        $em->flush();

        $response = $this->objectTransformer->ObjectToJson( $object, [
            'id', 'name', 'enabled', 'context',
        ]);

        return $this->json($response);
    }

    /** ****************************************************************************************** */
    /**
     * Remove o recurso — Galeria.
     * Remove o recurso — Galeria.
     */
    #[OA\Parameter( name: 'id', description: 'Identificador do recurso', in: 'path')]
    #[OA\Response(response: 204, description: 'Recurso excluído')]
    #[OA\Response(response: 404, description: 'Recurso não encontrado')]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[ACL\Api(enable: true, title: 'Deletar', description: 'Deletar Galeria')]
    public function deleteAction(int $id): Response
    {
        $this->validateAccess("deleteAction");

        $object = $this->getRepository()->find($id);
        if (!$object)
            return $this->json(['status' => false, 'message' => 'Galeria não encontrado.'], 404);

        $em = $this->doctrine->getManager();
        $em->remove($object);
        $em->flush();

        return $this->json(['status' => true, 'message' => 'Galeria removido com sucesso.'], 204);
    }

}