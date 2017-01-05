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
use CoreBundle\Entity\ContactDetail;
use CoreBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadCompanyContactData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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

        $list = $this->companyContactsList();

        $validator = $this->_container->get('validator');

        foreach ($list as $companyContactArr) {

            /** @var $contact Contact */
            $contact = new Contact();

            $contact
                ->setFirstName($companyContactArr['firstName'])
                ->setLastName($companyContactArr['lastName'])
                ->setJobTitle($companyContactArr['jobTitle'])
                ->setGender($companyContactArr['gender'])
                ->setBirthDate((new \DateTime($companyContactArr['birthDate'])))
                ->setEditableAll($companyContactArr['editableAll'])
                ->setCreatedBy($this->getReference('user-' . $companyContactArr['createdBy']))
                ->setUpdatedBy($this->getReference('user-' . $companyContactArr['createdBy']))
                ;

            foreach ($companyContactArr['contactDetail'] as $type => $value) {
                $contactDetail = new ContactDetail();
                $contactDetail
                    ->setType($type)
                    ->setValue($value)
                    ->setCreatedBy($this->getReference('user-' . $companyContactArr['createdBy']))
                    ->setUpdatedBy($this->getReference('user-' . $companyContactArr['createdBy']));

                $contact->addContactDetail($contactDetail);
            }

                /** @var $company Company */
                $company = $this->getReference('company-' . $companyContactArr['companyTitle']);
                $company->addContact($contact);


            $errors = $validator->validate($contact);
            if(count($errors) > 0) {
                throw new \Exception((string) $errors );
            }

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
                'editableAll' => false,
                'createdBy' => 'root',
                'contactDetail' => [
                    ContactDetail::TYPE_EMAIL => 'tomas.kigaru@orange.pl',
                    ContactDetail::TYPE_EMAIL => 'tomas.kigaru@orange.com',
                    ContactDetail::TYPE_EMAIL => 'tomas.kigaru@gmail.com',
                    ContactDetail::TYPE_MOBILE=> '+48 733 733 733',
                    ContactDetail::TYPE_PHONE=> '+48 32 225 57 73',
                ]
            ],
            [
                'companyTitle' => 'Orange',
                'firstName' => 'Juliette',
                'lastName' => 'Venegaas',
                'jobTitle' => 'Sales Director',
                'gender' => 'female',
                'birthDate' => '1953-04-14',
                'editableAll' => true,
                'createdBy' => 'root',
                'contactDetail' => [
                    ContactDetail::TYPE_EMAIL => 'juliette.venegaas@orange.pl',
                    ContactDetail::TYPE_MOBILE=> '+48 732 654 654',
                ]
            ],
            [
                'companyTitle' => 'UPC',
                'firstName' => 'Simon',
                'lastName' => 'Boyd',
                'jobTitle' => 'Owner',
                'gender' => 'male',
                'birthDate' => '1980-02-14',
                'editableAll' => true,
                'createdBy' => 'user1',
                'contactDetail' => [
                    ContactDetail::TYPE_EMAIL => 'simon.boyd@upc.com.pl',
                    ContactDetail::TYPE_MOBILE => '+48 725 541 147',
                ]
            ],
            [
                'companyTitle' => 'UPC',
                'firstName' => 'Eve',
                'lastName' => 'Fist',
                'jobTitle' => null,
                'gender' => 'female',
                'birthDate' => '1988-03-18',
                'editableAll' => true,
                'createdBy' => 'user1',
                'contactDetail' => [
                    ContactDetail::TYPE_EMAIL => 'eve.fist@upc.com.pl',
                    ContactDetail::TYPE_EMAIL => 'eve.fist@upc.com',
                    ContactDetail::TYPE_MOBILE=> '+48 733 529 123',
                    ContactDetail::TYPE_PHONE=> '+48 22 289 47 71',
                ]
            ],
            [
                'companyTitle' => 'UPC',
                'firstName' => 'Beata',
                'lastName' => 'Iron',
                'jobTitle' => 'Billing specialist',
                'gender' => 'female',
                'birthDate' => '1985-12-18',
                'editableAll' => false,
                'createdBy' => 'user1',
                'contactDetail' => [
                    ContactDetail::TYPE_EMAIL => 'beata.iron@upc.com.pl',
                    ContactDetail::TYPE_EMAIL => 'beata.iron@upc.com',
                    ContactDetail::TYPE_MOBILE => '+48 722 529 123',
                    ContactDetail::TYPE_FAX => '+48 22 211 17 71',
                ]
            ],
            [
                'companyTitle' => 'Zanox',
                'firstName' => 'Violetta',
                'lastName' => 'Carma',
                'jobTitle' => 'Billing specialist',
                'gender' => 'female',
                'birthDate' => '1986-01-25',
                'editableAll' => true,
                'createdBy' => 'user2',
                'contactDetail' => [
                    ContactDetail::TYPE_EMAIL => 'violetta.carma@zanox.com',
                    ContactDetail::TYPE_EMAIL => 'violetta.carma@zanox.com',
                    ContactDetail::TYPE_MOBILE => '+48 732 888 123',
                    ContactDetail::TYPE_FAX => '+48 22 999 88 71',
                ]
            ],
            [
                'companyTitle' => 'Polcode',
                'firstName' => 'Olga',
                'lastName' => 'Mikrofalov',
                'jobTitle' => 'Economy specialist',
                'gender' => 'female',
                'birthDate' => '1986-10-18',
                'editableAll' => true,
                'createdBy' => 'user3',
                'contactDetail' => [
                    ContactDetail::TYPE_EMAIL => 'olga.mikrofalov@polcode.net',
                    ContactDetail::TYPE_EMAIL => 'olga.mikrofalov@polcode.com',
                    ContactDetail::TYPE_MOBILE => '+48 734 741 283',
                    ContactDetail::TYPE_FAX => '+48 22 528 21 67',
                    ContactDetail::TYPE_PHONE => '+48 22 528 21 64',
                ]
            ],
            [
                'companyTitle' => 'Polcode',
                'firstName' => 'Jajami',
                'lastName' => 'Omate',
                'jobTitle' => 'HelpDesk engineer',
                'gender' => 'male',
                'birthDate' => '1990-03-22',
                'editableAll' => false,
                'createdBy' => 'user3',
                'contactDetail' => [
                    ContactDetail::TYPE_EMAIL => 'jajami.omate@polcode.net',
                    ContactDetail::TYPE_EMAIL => 'jajami.omate@polcode.com',
                    ContactDetail::TYPE_MOBILE => '+48 733 741 271',
                    ContactDetail::TYPE_FAX => '+48 22 528 21 84',
                    ContactDetail::TYPE_PHONE => '+48 22 528 21 47',
                ]
            ],
            [
                'companyTitle' => 'Polcode',
                'firstName' => 'Leyla',
                'lastName' => 'Moore',
                'jobTitle' => 'HelpDesk engineer',
                'gender' => 'female',
                'birthDate' => '1991-07-22',
                'editableAll' => true,
                'createdBy' => 'user3',
                'contactDetail' => [
                    ContactDetail::TYPE_EMAIL => 'leyna.moore@polcode.net',
                    ContactDetail::TYPE_EMAIL => 'leyna.moore@polcode.com',
                    ContactDetail::TYPE_MOBILE => '+48 736 222 479',
                    ContactDetail::TYPE_FAX => '+48 22 741 14 21',
                    ContactDetail::TYPE_PHONE => '+48 22 741 15 93',
                ]
            ],
            [
                'companyTitle' => 'UPC',
                'firstName' => 'Kosi',
                'lastName' => 'Mimazaki',
                'jobTitle' => 'Senior HelpDesk engineer',
                'gender' => 'male',
                'birthDate' => '1994-08-28',
                'editableAll' => false,
                'createdBy' => 'user3',
                'contactDetail' => [
                    ContactDetail::TYPE_EMAIL => 'kosi.mimazaki@polcode.net',
                    ContactDetail::TYPE_EMAIL => 'kosi.mimazaki@polcode.com',
                    ContactDetail::TYPE_MOBILE => '+48 731 222 479',
                    ContactDetail::TYPE_FAX => '+48 22 741 14 21',
                    ContactDetail::TYPE_PHONE => '+48 22 741 15 93',
                ]
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