<?php

namespace AppBundle\Repository;

/**
 * configRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConfigRepository extends \Doctrine\ORM\EntityRepository
{

    public function views ()
    {
        $q = $this->createQueryBuilder('c');
        $q->select('c.value')
           ->where('c.name = :name')
           ->setParameter('name','views');
        return $q->getQuery()->getSingleScalarResult();
    }
}