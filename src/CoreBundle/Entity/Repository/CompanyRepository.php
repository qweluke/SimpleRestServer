<?php

namespace CoreBundle\Entity\Repository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * CompanyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompanyRepository extends \Doctrine\ORM\EntityRepository
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
            $query->orWhere('c.name like :query')
                ->orWhere('c.description like :query')
                ->setParameter('query', '%' . $query['query'] . '%');
        }

        if (isset($search['orderBy']) && is_array($search['orderBy'])) {
            foreach ($search['orderBy'] as $orderBy) {
                if (preg_match('/^(id|name|createdAt|updatedAt) (ASC|DESC)/', $orderBy)) {
                    list($column, $dir) = explode(' ', $orderBy);
                    $query->addOrderBy('c.' . $column, $dir);
                }
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
