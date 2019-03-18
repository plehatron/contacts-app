<?php

namespace App\EventListener;

use App\Elasticsearch\Index\ContactIndex;
use App\Entity\Contact;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Elasticsearch\Client;

final class ContactSearchEngineIndexer
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var ContactIndex
     */
    private $index;

    /**
     * @var array
     */
    private $forRemoval;

    public function __construct(Client $client, ContactIndex $index)
    {
        $this->client = $client;
        $this->index = $index;
    }

    public function postPersist(Contact $contact, LifecycleEventArgs $args)
    {
        $this->index($contact);
    }

    public function preRemove(Contact $contact, LifecycleEventArgs $args)
    {
        // Doctrine removes entity ID before postRemove, make sure we keep a map of object's ID and entity ID
        $this->forRemoval[spl_object_id($contact)] = $contact->getId();
    }

    public function postRemove(Contact $contact, LifecycleEventArgs $args)
    {
        $objectId = spl_object_id($contact);
        $contactId = $this->forRemoval[$objectId] ?? null;
        if ($contactId) {
            $this->removeById($contactId);
        }
        unset($this->forRemoval[$objectId]);
    }

    /**
     * @param Contact $contact
     * @return array
     */
    public function index($contact)
    {
        $phoneNumbersBody = [];
        foreach ($contact->getPhoneNumbers() as $phoneNumber) {
            $phoneNumbersBody[] = [
                'id' => $phoneNumber->getId(),
                'number' => $phoneNumber->getNumber(),
                'label' => $phoneNumber->getLabel(),
            ];
        }
        $params = [
            'index' => $this->index->getName(),
            'type' => $this->index->getType(),
            'id' => $contact->getId(),
            'body' => [
                'id' => $contact->getId(),
                'first_name' => $contact->getFirstName(),
                'last_name' => $contact->getLastName(),
                'email_address' => $contact->getEmailAddress(),
                'favourite' => $contact->getFavourite(),
                'phone_numbers' => $phoneNumbersBody,
            ],
        ];
        $response = $this->client->index($params);

        return $response;
    }

    /**
     * @param $id
     * @return array
     */
    public function removeById($id)
    {
        return $this->client->delete(
            [
                'index' => $this->index->getName(),
                'type' => $this->index->getType(),
                'id' => $id,
            ]
        );
    }
}