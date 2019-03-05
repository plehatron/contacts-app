<?php

namespace App\Tests\Api;

trait AssertJsonValidSchema
{
    /**
     * @param string $data
     * @param string $schema
     */
    public function assertJsonValidSchema(string $data, string $schema)
    {
        $data = json_decode($data);
        $validator = new \JsonSchema\Validator();
        $isNotValid = $validator->validate($data, json_decode($schema));
        $errors = $validator->getErrors();
        $errorMessage = array_reduce(
            $errors,
            function ($carry, $item) {
                return $carry."\n".sprintf(
                        "Property %s (%s) invalid: %s",
                        $item['property'],
                        $item['pointer'],
                        $item['message']
                    );
            }
        );
        $this->assertEquals(0, $isNotValid, $errorMessage);
    }
}