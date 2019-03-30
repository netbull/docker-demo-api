<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class TaskRepository
 * @package App\Entity
 */
class TaskRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getTasks()
    {
        $qb = $this->createQueryBuilder('t');

        return $qb
            ->orderBy('t.done', 'ASC')
            ->addOrderBy('t.createdAt', 'DESC')
            ->getQuery()->getArrayResult();
    }
}
