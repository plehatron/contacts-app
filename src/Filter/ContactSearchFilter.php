<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Custom filter that uses the request's query parameter to filter contacts.
 *
 * @package App\Filter
 */
class ContactSearchFilter extends AbstractContextAwareFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ) {
        $searchTermsString = $context['filters']['query'] ?? null;

        if (null === $searchTermsString) {
            return;
        }

        // Note that max search term length is limited to N characters (including spaces)
        $searchTermsString = mb_substr(filter_var($searchTermsString, FILTER_SANITIZE_STRING), 0, 100);

        $searchTerms = explode(' ', $searchTermsString);
        $searchTerms = array_filter(
            $searchTerms,
            function ($item) {
                return $item;
            }
        );

        // Note that number of search terms is hardcoded
        $searchTerms = array_chunk($searchTerms, 5)[0];

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->leftJoin($rootAlias.'.phoneNumbers', 'pn');

        $searchColumns = [
            $rootAlias.'.firstName' => ['searchTermStart', 'searchTermWord'],
            $rootAlias.'.lastName' => ['searchTermStart', 'searchTermWord'],
            $rootAlias.'.emailAddress' => ['searchTermPartial'],
            'pn.number' => ['searchTermPartial'],
            'pn.label' => ['searchTermPartial'],
        ];

        foreach ($searchTerms as $num => $searchTerm) {

            $likeStatements = [];
            foreach ($searchColumns as $searchColumn => $parameters) {
                foreach ($parameters as $parameter) {
                    $likeStatements[] = $queryBuilder->expr()->like($searchColumn, ':'.$parameter.$num);
                }
            }

            $orX = $queryBuilder->expr()->orX(...$likeStatements);

            $queryBuilder->setParameter('searchTermStart'.$num, $searchTerm.'%');
            $queryBuilder->setParameter('searchTermWord'.$num, '% '.$searchTerm.'%');
            $queryBuilder->setParameter('searchTermPartial'.$num, '%'.$searchTerm.'%');

            $queryBuilder->andWhere($orX);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(string $resourceClass): array
    {
        return [
            'query' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'Search for contacts',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ) {
    }
}