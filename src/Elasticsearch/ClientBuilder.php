<?php

namespace App\Elasticsearch;

use Elasticsearch\Client;

final class ClientBuilder
{
    /**
     * @param string $host
     * @return Client
     */
    public function build(string $host): Client
    {
        $builder = new \Elasticsearch\ClientBuilder();
        $builder->setHosts([$host]);
        $client = $builder->build();

        return $client;
    }
}
