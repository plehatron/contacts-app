<?php

namespace App\Elasticsearch\Index;

final class ContactIndex
{
    /**
     * @var string
     */
    private $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function getName(): string
    {
        $prefix = $this->namespace ? $this->namespace.'_' : '';

        return $prefix.'contact';
    }

    public function getType(): string
    {
        return '_doc';
    }

    public function getMappings(): array
    {

        return [
            $this->getType() => [
                'properties' => [
                    'id' => [
                        'type' => 'keyword',
                    ],
                    'first_name' => [
                        'type' => 'text',
                    ],
                    'last_name' => [
                        'type' => 'text',
                    ],
                    'email_address' => [
                        'type' => 'text',
                    ],
                    'favourite' => [
                        'type' => 'keyword',
                    ],
                    'phone_numbers' => [
                        'type' => 'nested',
                        'include_in_parent' => true, // flattens the nested resource as an object in parent
                        'properties' => [
                            'id' => [
                                'type' => 'keyword',
                            ],
                            'number' => [
                                'type' => 'text',
                            ],
                            'label' => [
                                'type' => 'text',
                            ],
                        ],
                        'dynamic' => 'strict',
                    ],
                ],
                'dynamic' => 'strict',
            ],
        ];
    }
}