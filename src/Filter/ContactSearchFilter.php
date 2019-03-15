<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Elasticsearch\Index\ContactIndex;
use App\EventListener\ContactSearchEngineIndexer;
use Doctrine\ORM\QueryBuilder;
use Elasticsearch\Client;

/**
 * Custom filter that uses the request's query parameter to filter contacts.
 *
 * @package App\Filter
 */
final class ContactSearchFilter extends AbstractContextAwareFilter
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var ContactIndex
     */
    private $index;

    public function __construct(Client $client, ContactIndex $index)
    {
        $this->client = $client;
        $this->index = $index;
    }

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

        $searchParams = [
            'index' => $this->index->getName(),
            'type' => $this->index->getType(),
            'body' => [
                'query' => [
                    'simple_query_string' => [
                        'query' => $searchTermsString,
                        'fields' => [
                            'first_name',
                            'last_name',
                            'email_address',
                            'phone_numbers.label',
                            'phone_numbers.number',
                        ],
                        'default_operator' => 'or',
                    ],
                ],
            ],
        ];
        $result = $this->client->search($searchParams);

        $hits = $result['hits']['total'] ?? 0;
        if ($hits > 0) {
            $hitIds = array_map(
                function ($doc) {
                    return $doc['_id'];
                },
                $result['hits']['hits']
            );
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere($queryBuilder->expr()->in($rootAlias.'.id', $hitIds));
        } else {
            $queryBuilder->andWhere($queryBuilder->expr()->eq(0, 1));
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