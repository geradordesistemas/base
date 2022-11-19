<?php

namespace App\Application\Project\ContentBundle\Service;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class FilterDoctrine
{
    protected Criteria $criteria;
    protected int $page = 1;
    protected int $pageSize = 10;
    protected array $orderBy = [];

    protected string $path;

    /***
     *
     * @param $repository
     * @param Request $request
     * @param array $attributesFilters
     */
    public function __construct(
        protected mixed $repository,
        protected Request $request,
        protected array $attributesFilters,
    )
    {
        $this->criteria = New Criteria();
        $this->path =  $this->request->getPathInfo();
    }

    /**
     * Filtros
     *
     * @return object|array
     * @throws QueryException
     */
    public function getResult(): object|array
    {
        $this->validateParameters();

        /** Create Query Builder */
        $query = $this->repository->createQueryBuilder('d');

        $query->addCriteria($this->criteria);

        return $this->genetatePaginator($query);
    }

    protected function genetatePaginator($query)
    {
        $paginator = new Paginator($query);

        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $this->pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($this->pageSize * ($this->page-1))
            ->setMaxResults($this->pageSize);

        $result = $paginator->getQuery()->getResult();

        $path = $this->path . "?pagina=";
        $pageActual = $path . $this->page;
        $pageFirst = $path . 1;
        $pageLast = $path  . intval($pagesCount);
        $pagePrevious = ( ($this->page > 1)? $path . $this->page-1 : null );
        $pageNext = ( ($this->page < $pagesCount)? $path . $this->page+1 : null );

        $paginator = [
            "totalItems" => $totalItems,
            "paginaTamanho" => $this->pageSize,
            "paginas" => [
                "atual" => $pageActual,
                "primeira" => $pageFirst,
                "ultima" => $pageLast,
                "anterior" => $pagePrevious,
                "proxima" =>  $pageNext,
            ],
        ];

        return (object) [
            "data" => $result,
            "paginator" => $paginator
        ];

    }

    protected function validateParameters(): void
    {
        $param = $this->request->query->all();

        //dd($param);
        /** Get parameter page */
        $page = isset( $param['pagina'] ) ? intval($param['pagina']) : null;
        $this->page = ($page > 0) ? $page : 1 ;

        /** Get parameter page */
        $pageSize = isset($param['paginaTamanho']) ? intval($param['paginaTamanho']) : null;
        $this->pageSize = ($pageSize > 0) ? $pageSize : 10 ;

        /**
         * Percorre todos os atributos e implementa os filtros.
         */
        foreach ($this->attributesFilters as $attribute) {
            $attributeFilters = $param[$attribute] ?? null;

            if(!$attributeFilters)
                continue;

            foreach ($this->getFilterList() as $filter){

                if(!isset($attributeFilters[$filter]))
                    continue;

                //dd($filter, $attribute, $attributeFilters[$filter]);

                $this->implementCriteria($filter, $attribute, $attributeFilters[$filter]);
            }
        }

        //dd($this->orderBy);
        if(!empty($this->orderBy))
            $this->order_by();

    }


    public function getFilterList(): array
    {
        return [
            'igual', 'diferente', 'maior', 'maior_ou_igual',
            'menor', 'menor_ou_igual', 'nulo', 'nao_nulo',
            'comeca_com', 'termina_com', 'contem', 'ordenar_por'
        ];
    }

    /**
     * Transforma a query em um array mapeado dos filtros
     * @param $filter
     * @param $field
     * @param $value
     * @return void
     */
    protected function implementCriteria($filter, $field, $value): void
    {
        match ($filter) {
            'igual' => $this->eq( $field, $value ),
            'diferente' => $this->neq( $field, $value ),
            'maior' => $this->gt( $field, $value ),
            'maior_ou_igual' => $this->gte( $field, $value ),
            'menor' => $this->lt( $field, $value ),
            'menor_ou_igual' => $this->lte( $field, $value ),
            'nulo' => $this->is_null( $field),
            'nao_nulo' => $this->is_not_null( $field ),
            'comeca_com' => $this->starts_with( $field, $value ),
            'termina_com' => $this->ends_with( $field, $value ),
            'contem' => $this->contains( $field, $value ),
            'ordenar_por' => ( strtoupper($value) == "ASC" || strtoupper($value) == "DESC")? $this->orderBy[$field] = strtoupper($value): '',
            default => '',
        };
    }

    /**
     * Igualdade
     * @param string $field
     * @param string $value
     * @return void
     */
    protected function eq(string $field, string $value): void
    {
        $this->criteria->andWhere(Criteria::expr()->eq($field, $value));
    }

    /**
     * Desigualdade
     * @param string $field
     * @param string $value
     * @return void
     */
    protected function neq(string $field, string $value): void
    {
        $this->criteria->andWhere(Criteria::expr()->neq($field, $value));
    }

    /**
     * Maior que
     * @param string $field
     * @param string $value
     * @return void
     */
    protected function gt(string $field, string $value): void
    {
        $this->criteria->andWhere(Criteria::expr()->gt($field, $value));
    }

    /**
     * Maior ou Igual
     * @param string $field
     * @param string $value
     * @return void
     */
    protected function gte(string $field, string $value): void
    {
        $this->criteria->andWhere(Criteria::expr()->gte($field, $value));
    }

    /**
     * Menor que
     *
     * @param string $field
     * @param string $value
     * @return void
     */
    protected function lt(string $field, string $value): void
    {
        $this->criteria->andWhere(Criteria::expr()->lt($field, $value));
    }

    /**
     * Menor ou igual
     *
     * @param string $field
     * @param string $value
     * @return void
     */
    protected function lte(string $field, string $value): void
    {
        $this->criteria->andWhere(Criteria::expr()->lte($field, $value));
    }

    /**
     * É nulo
     *
     * @param string $field
     * @return void
     */
    protected function is_null(string $field): void
    {
        $this->criteria->andWhere(Criteria::expr()->isNull($field));
    }

    /**
     * Não é nulo
     *
     * @param string $field
     * @return void
     */
    protected function is_not_null(string $field): void
    {
        $this->criteria->andWhere(Criteria::expr()->neq($field, null));
    }

    /**
     * Começa com
     *
     * @param string $field
     * @param string $value
     * @return void
     */
    protected function starts_with(string $field, string $value): void
    {
        $this->criteria->andWhere(Criteria::expr()->startsWith($field, $value));
    }

    /**
     * Termina com
     *
     * @param string $field
     * @param string $value
     * @return void
     */
    protected function ends_with(string $field, string $value): void
    {
        $this->criteria->andWhere(Criteria::expr()->endsWith($field, $value));
    }

    /**
     * Contém
     *
     * @param string $field
     * @param string $value
     * @return void
     */
    protected function contains(string $field, string $value): void
    {
        $this->criteria->andWhere(Criteria::expr()->contains($field, $value));
    }

    /**
     * Ordenação
     * @return void
     */
    protected function order_by(): void
    {
        $this->criteria->orderBy($this->orderBy);
    }

}