<?php

namespace App\Repository;

use App\Entity\ContactPhoneNumber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ContactPhoneNumber|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactPhoneNumber|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactPhoneNumber[]    findAll()
 * @method ContactPhoneNumber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactPhoneNumberRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ContactPhoneNumber::class);
    }
}
