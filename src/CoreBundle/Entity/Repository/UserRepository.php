<?php

namespace CoreBundle\Entity\Repository;


use CoreBundle\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;

class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function search(array $search = [])
    {
        $query = $this->createQueryBuilder('u')->select('u');

        if (!empty($search['query'])) {
            $query->orWhere($query->expr()->orX(
                $query->expr()->like('u.username', ':query'),
                $query->expr()->like('u.firstName', ':query'),
                $query->expr()->like('u.lastName', ':query'),
                $query->expr()->like('u.email', ':query')
            ))->setParameter('query', '%' . $search['query'] . '%');
        }


        if (isset($search['role']) && in_array($search['role'], ['user', 'admin'])) {
            switch ($search['role']) {
                case 'admin':
                    $query->andWhere($query->expr()->like('u.roles', ':role'));
                    break;
                case 'user':
                    $query->andWhere($query->expr()->notLike('u.roles', ':role'));
                    break;
            }

            $query->setParameter('role', '%ROLE_ADMIN%');
        }

        if (isset($search['orderBy']) && is_array($search['orderBy'])) {
            foreach ($search['orderBy'] as $orderBy) {
                if (preg_match('/^(id|firstName|lastName|gender) (ASC|DESC)/', $orderBy)) {
                    list($column, $dir) = explode(' ', $orderBy);
                    $query->addOrderBy('u.' . $column, $dir);
                }
            }
        }

        if (isset($search['gender']) && in_array($search['gender'], ['male', 'female'])) {
            $query->andWhere('u.gender = :gender')->setParameter('gender', $search['gender']);
        }

        if (isset($search['active']) && in_array($search['active'], ['true', 'false'])) {
            switch ($search['active']) {
                case 'true':
                    $query->andWhere('u.enabled = 1');
                    break;
                case 'false':
                    $query->andWhere('u.enabled = 0');
                    break;
            }
        }

        $paginator = new Paginator($query->getQuery());

        $paginator->getQuery()
            ->setFirstResult($search['limit'] * ($search['page'] - 1))
            ->setMaxResults($search['limit']);

        return [
            'data' => $paginator->getQuery()->getResult(),
            'pagesCount' => (int) ceil($paginator->count() / $paginator->getQuery()->getMaxResults()),
            'totalItems' => (int) $paginator->count(),
        ];
    }
}
