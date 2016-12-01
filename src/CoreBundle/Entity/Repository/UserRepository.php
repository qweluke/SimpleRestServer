<?php

namespace CoreBundle\Entity\Repository;


use CoreBundle\Entity\User;

class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function search(array $query = [])
    {

        $rows = $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from('CoreBundle:User', 'u');

        if (!empty($query['query'])) {
            $rows->orWhere('u.username like :query')
                ->orWhere('u.firstName like :query')
                ->orWhere('u.lastName like :query')
                ->orWhere('u.email like :query')
                ->setParameter('query', '%' . $query['query'] . '%');
        }

        if (isset($query['role']) && in_array($query['role'], ['user', 'admin'])) {
            switch ($query['role']) {
                case 'admin':
                    $rows->andWhere('u.roles like :role')->setParameter('role', '%ROLE_ADMIN%');
                    break;
                case 'user':
                    $rows->andWhere('u.roles not like :role')->setParameter('role', '%ROLE_ADMIN%');
                    break;
            }
        }

        if (isset($query['gender']) && in_array($query['gender'], ['male', 'female'])) {
            $rows->andWhere('u.gender = :gender')->setParameter('gender', $query['gender']);
        }

        if (isset($query['active']) && in_array($query['active'], ['true', 'false'])) {
            switch ($query['active']) {
                case 'true':
                    $rows->andWhere('u.enabled = 1');
                    break;
                case 'false':
                    $rows->andWhere('u.enabled = 0');
                    break;
            }
        }

        if (isset($query['orderBy']) && is_array($query['orderBy'])) {
            foreach ($query['orderBy'] as $orderBy) {
                if (preg_match('/^(id|firstName|lastName|gender) (ASC|DESC)/', $orderBy)) {
                    list($column, $dir) = explode(' ', $orderBy);
                    $rows->addOrderBy('u.' . $column, $dir);
                }
            }
        }


        return $rows->getQuery()->getResult();
    }
}
