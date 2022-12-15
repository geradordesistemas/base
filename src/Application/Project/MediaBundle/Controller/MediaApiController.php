<?php

namespace App\Application\Project\MediaBundle\Controller;

use App\Application\Project\ContentBundle\Controller\Base\BaseApiController;
use App\Application\Project\ContentBundle\Service\FilterDoctrine;
use App\Application\Project\ContentBundle\Attributes\Acl as ACL;
use App\Application\Project\SecurityUserBundle\Entity\User;
use App\Entity\SonataMediaMedia;
use Doctrine\Persistence\ObjectRepository;
use Nelmio\ApiDocBundle\Model\Model;
use OpenApi\Attributes as OA;
use OpenApi\Annotations as OAA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Query\QueryException;
use ReflectionException;

#[Route('/api/midia', name: 'api_midia_')]
#[OA\Tag(name: 'Midia')]
#[ACL\Api(enable: true, title: 'Mídia', description: 'Permissões do modulo Mídia')]
class MediaApiController extends BaseApiController
{
    public function getClass(): string
    {
        return SonataMediaMedia::class;
    }

    public function getRepository(): ObjectRepository
    {
        return $this->doctrine->getManager()->getRepository($this->getClass());
    }


    public function getAllMediaPropertys(){
        return [
            'id', 'name', 'description', 'enabled', 'providerName', 'providerStatus', 'providerReference',
            'providerMetadata', 'width', 'height', 'length', 'size', 'contentType',  'copyright',
            'authorName', 'context', 'cdnIsFlushable', 'cdnFlushIdentifier', 'cdnFlushAt', 'cdnStatus',
            'updatedAt', 'createdAt', 'binaryContent', 'previousProviderReference', 'category'
        ];
    }
    public function getMediaPropertys(){
        return [
            'id', 'name', 'description', 'enabled', 'width', 'height', 'length', 'size', 'contentType',
            'copyright', 'authorName',
        ];
    }

        public function getMediaPropertysPost(){
        return [
            'name', 'description', 'enabled', 'copyright', 'authorName',
        ];
    }

    /**
     * Recupera a coleção de recursos — Media.
     * Recupera a coleção de recursos — Media.
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
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'enabled', type: 'integer'),
                new OA\Property(property: 'width', type: 'integer'),
                new OA\Property(property: 'height', type: 'integer'),
                new OA\Property(property: 'length', type: 'integer'),
                new OA\Property(property: 'size', type: 'integer'),
                new OA\Property(property: 'contentType', type: 'string'),
                new OA\Property(property: 'copyright', type: 'string'),
                new OA\Property(property: 'authorName', type: 'string'),
            ],
            type: 'object'
        )
    )]
    #[Route('', name: 'list', methods: ['GET'])]
    #[ACL\Api(enable: true, title: 'Listar', description: 'Listar Media')]
    public function listAction(Request $request): Response
    {
        $this->validateAccess(actionName: "listAction");

        $filter = new FilterDoctrine(
            repository:  $this->getRepository(),
            request: $request,
            attributesFilters: $this->getMediaPropertys(),
        );

        //dd($filter->getResult()->data);

        $response = $this->objectTransformer->ObjectToJson( $filter->getResult()->data, $this->getMediaPropertys());

        return $this->json([
            'resultado' => $response,
            'paginacao' => $filter->getResult()->paginator,
        ]);

    }

    /** ****************************************************************************************** */
    /**
     * Cria o Recurso — Media.
     * Cria o Recurso — Media.
     */
    #[OA\Parameter( name: 'media', description: 'arquivos', in: 'query', required: false, allowEmptyValue: true, example: 1)]
    #[Route('', name: 'create', methods: ['POST'])]
    #[ACL\Api(enable: true, title: 'Criar', description: 'Criar Media')]
    public function createAction(Request $request): Response
    {
        $this->validateAccess("createAction");

        $arquivos = $request->files->get('media');

        if(!$arquivos)
            return $this->json(['status' => 'error', 'message' => 'Anexe ao menos 1 arquivo!'], 404);

        $media = $this->mediaService->createMedia($arquivos);

       // dd($media);
        $response = $this->objectTransformer->ObjectToJson( $media, $this->getMediaPropertys());

        return $this->json($response, 201);

    }

    /** ****************************************************************************************** */
    /**
     * Recupera o recurso — Media.
     * Recupera o recurso — Media.
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
    #[ACL\Api(enable: true, title: 'Visualizar', description: 'Visualizar Media')]
    public function showAction(Request $request, int $id): Response
    {
        $this->validateAccess("showAction");

        $data = $this->getRepository()->find($id);
        if (!$data)
            return $this->json(['status' => false, 'message' => 'Media não encontrado!'], 404);

        $response = $this->objectTransformer->ObjectToJson( $data, $this->getMediaPropertys());

        return $this->json($response);
    }

    /** ****************************************************************************************** */
    /**
     * Substitui o recurso — Media.
     * Substitui o recurso — Media.
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
    #[Route('/{id}', name: 'edit', methods: ['POST'])]
    #[ACL\Api(enable: true, title: 'Editar', description: 'Editar Media')]
    public function editAction(Request $request, $id): Response
    {
        $this->validateAccess("editAction");

        $arquivos = $request->files->get('media');

        //dd($arquivos);
        if(!$arquivos)
            return $this->json(['status' => 'error', 'message' => 'Anexe ao menos 1 arquivo!'], 404);

        $media = $this->getRepository()->find($id);
        if (!$media)
            return $this->json(['status' => false, 'message' => 'Media não encontrado!'], 404);

        $media = $this->mediaService->createMedia($arquivos, $media);

        $response = $this->objectTransformer->ObjectToJson( $media, $this->getMediaPropertys());

        return $this->json($response, 201);
    }

    /** ****************************************************************************************** */
    /**
     * Remove o recurso — Media.
     * Remove o recurso — Media.
     */
    #[OA\Parameter( name: 'id', description: 'Identificador do recurso', in: 'path')]
    #[OA\Response(response: 204, description: 'Recurso excluído')]
    #[OA\Response(response: 404, description: 'Recurso não encontrado')]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[ACL\Api(enable: true, title: 'Deletar', description: 'Deletar Media')]
    public function deleteAction(int $id): Response
    {
        $this->validateAccess("deleteAction");

        $object = $this->getRepository()->find($id);
        if (!$object)
            return $this->json(['status' => false, 'message' => 'Media não encontrado.'], 404);

        $em = $this->doctrine->getManager();
        $em->remove($object);
        $em->flush();

        return $this->json(['status' => true, 'message' => 'Media removido com sucesso.'], 204);
    }

}