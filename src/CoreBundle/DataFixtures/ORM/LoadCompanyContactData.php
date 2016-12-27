<?php
/**
 * Created by PhpStorm.
 * User: lmalicki
 * Date: 11.11.15
 * Time: 12:48
 */

namespace CoreBundle\DataFixtures\ORM;

use CoreBundle\Entity\Company;
use CoreBundle\Entity\Contact;
use CoreBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadCompanyContactData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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

        $companyRepo = $manager->getRepository(Company::class);
        $userRepo = $manager->getRepository(User::class);

        $list = $this->companyContactsList();

        foreach ($list as $companyContactArr) {

            /** @var $addedBy User */
            $addedBy = $userRepo->findOneBy(['username' => $companyContactArr['createdBy']]);

            /** @var $company Company */
            $contact = new Contact();

            $contact
                ->setFirstName($companyContactArr['firstName'])
                ->setLastName($companyContactArr['lastName'])
                ->setJobTitle($companyContactArr['jobTitle'])
                ->setGender($companyContactArr['gender'])
                ->setBirthDate((new \DateTime($companyContactArr['birthDate'])))
                ->setVisibleAll($companyContactArr['visibleAll'])
                ->setCreatedBy($addedBy)
                ->setUpdatedBy($addedBy)
                ;



                /** @var $addedBy User */
                $company = $companyRepo->findOneBy(['name' => $companyContactArr['companyTitle']]);
                $company->addContact($contact);


            $manager->persist($contact);
            $manager->persist($company);

        }

        $manager->flush();

    }

    public function companyContactsList()
    {
        $list = [
            [
                'companyTitle' => 'Orange',
                'firstName' => 'Tomas',
                'lastName' => 'Hikaru',
                'jobTitle' => 'Director',
                'gender' => 'male',
                'birthDate' => '1973-11-23',
                'visibleAll' => true,
                'createdBy' => 'root',
            ],
            [
                'companyTitle' => 'Orange',
                'firstName' => 'Juliette',
                'lastName' => 'Venegaas',
                'jobTitle' => 'Sales Director',
                'gender' => 'female',
                'birthDate' => '1953-04-14',
                'visibleAll' => true,
                'createdBy' => 'root',
            ],
            [
                'companyTitle' => 'UPC',
                'firstName' => 'Simon',
                'lastName' => 'Boyd',
                'jobTitle' => 'Owner',
                'gender' => 'male',
                'birthDate' => '1980-02-14',
                'visibleAll' => true,
                'createdBy' => 'user1',
            ],
            [
                'companyTitle' => 'UPC',
                'firstName' => 'Eve',
                'lastName' => 'Fist',
                'jobTitle' => null,
                'gender' => 'female',
                'birthDate' => '1988-03-18',
                'visibleAll' => false,
                'createdBy' => 'user1',
            ],
            [
                'companyTitle' => 'UPC',
                'firstName' => 'Beata',
                'lastName' => 'Iron',
                'jobTitle' => 'Billing specialist',
                'gender' => 'female',
                'birthDate' => '1985-12-18',
                'visibleAll' => true,
                'createdBy' => 'user1',
            ],
            [
                'companyTitle' => 'Zanox',
                'firstName' => 'Violetta',
                'lastName' => 'Carma',
                'jobTitle' => 'Billing specialist',
                'gender' => 'female',
                'birthDate' => '1986-01-25',
                'visibleAll' => true,
                'createdBy' => 'user2',
            ],
            [
                'companyTitle' => 'Polcode',
                'firstName' => 'Olga',
                'lastName' => 'Mikrofalov',
                'jobTitle' => 'Economy specialist',
                'gender' => 'female',
                'birthDate' => '1986-10-18',
                'visibleAll' => true,
                'createdBy' => 'user3',
            ],
            [
                'companyTitle' => 'Polcode',
                'firstName' => 'Jajami',
                'lastName' => 'Omate',
                'jobTitle' => 'HelpDesk engineer',
                'gender' => 'male',
                'birthDate' => '1990-03-22',
                'visibleAll' => false,
                'createdBy' => 'user3',
            ],
            [
                'companyTitle' => 'Polcode',
                'firstName' => 'Leyla',
                'lastName' => 'Moore',
                'jobTitle' => 'HelpDesk engineer',
                'gender' => 'female',
                'birthDate' => '1991-07-22',
                'visibleAll' => true,
                'createdBy' => 'user3',
            ],
            [
                'companyTitle' => 'UPC',
                'firstName' => 'Kosi',
                'lastName' => 'Mimazaki',
                'jobTitle' => 'Senior HelpDesk engineer',
                'gender' => 'male',
                'birthDate' => '1994-08-28',
                'visibleAll' => true,
                'createdBy' => 'user3',
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
        return 3;
    }
}