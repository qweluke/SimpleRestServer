<?php
/**
 * Created by PhpStorm.
 * User: lmalicki
 * Date: 11.11.15
 * Time: 12:48
 */

namespace CoreBundle\DataFixtures\ORM;

use CoreBundle\Entity\Company;
use CoreBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadCompanyData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $_container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->_container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $userRepo = $manager->getRepository(User::class);

        $list = $this->companyList();

        foreach ($list as $companyArr) {

            /** @var $addedBy User */
            $addedBy = $userRepo->findOneBy(['username' => $companyArr['addedBy']]);

            /** @var $company Company */
            $company = new Company();

            $company
                ->setName($companyArr['name'])
                ->setDescription($companyArr['description'])
                ->setCreatedBy($addedBy)
                ->setUpdatedBy($addedBy)
                ;

            $manager->persist($company);

        }

        $manager->flush();

    }

    public function companyList()
    {
        $list = [
            [
                'name' => 'Polcode',
                'description' => 'Best dev\'s ever!',
                'addedBy' => 'root'
            ],
            [
                'name' => 'UPC',
                'description' => 'Libert Global\'s Polish Cable TV',
                'addedBy' => 'root'
            ],            [
                'name' => 'Orange',
                'description' => null,
                'addedBy' => 'user1'
            ],            [
                'name' => 'Telekomunikacja Polska',
                'description' => 'Non existing Polish telephone operator.',
                'addedBy' => 'user1'
            ],            [
                'name' => 'Zanox',
                'description' => 'Largest affiliate marketing network',
                'addedBy' => 'user2'
            ],
        ];

        return $list;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 2;
    }
}