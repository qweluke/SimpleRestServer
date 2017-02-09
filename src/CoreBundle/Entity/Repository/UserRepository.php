<?php

namespace CoreBundle\Entity\Repository;


use CoreBundle\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;

class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function search(array $search = [])
    {
        if(!isset($search['page']) || $search['page'] <= 0) {
            $page = 1;
        } else {
            $page = $search['page'];
        }

        if(!isset($search['limit']) || $search['limit'] <= 0) {
            $limit = 100;
        } else {
            $limit = $search['limit'];
        }


        $query = $this->createQueryBuilder('e')->select('e');

        if (!empty($search['query'])) {
            $query->orWhere('u.username like :query')
                ->orWhere('u.firstName like :query')
                ->orWhere('u.lastName like :query')
                ->orWhere('u.email like :query')
                ->setParameter($query->expr()->like('query', $search['query']));
        }


        if (isset($search['role']) && in_array($search['role'], ['user', 'admin'])) {
            switch ($search['role']) {
                case 'admin':
                    $search->andWhere('u.roles like :role')->setParameter('role', '%ROLE_ADMIN%');
                    break;
                case 'user':
                    $search->andWhere('u.roles not like :role')->setParameter('role', '%ROLE_ADMIN%');
                    break;
            }
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
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return [
            'data' => $paginator->getQuery()->getResult(),
            'pagesCount' => (int) ceil($paginator->count() / $paginator->getQuery()->getMaxResults()),
            'totalItems' => (int) $paginator->count(),
        ];
    }
}
