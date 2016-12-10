<?php
/**
 * Created by PhpStorm.
 * User: lmalicki
 * Date: 11.11.15
 * Time: 12:48
 */

namespace CoreBundle\DataFixtures\ORM;

use CoreBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadUserData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        $userManager = $this->_container->get('fos_user.user_manager');
        $factory = $this->_container->get('security.encoder_factory');

        foreach ($this->userList() as $userArr) {

            /** @var $user User */
            $user = $userManager->createUser();

            $user
                ->setUsername($userArr['username'])
                ->setEmail(sprintf('%s@localhost.net', $userArr['username']))
                ->setRoles($userArr['roles'])
                ->setFirstName($userArr['firstName'])
                ->setBirthDate((new \DateTime($userArr['birthDate'])))
                ->setLastName($userArr['lastName'])
                ->setGender($userArr['gender'])
                ->setEnabled($userArr['enabled']);

            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($userArr['password'], $user->getSalt());
            $user->setPassword($password);

            $userManager->updateUser($user);

        }

    }

    public function userList()
    {
        $list = [
            [
                'username' => 'root', 'roles' => ['ROLE_ADMIN'], 'password' => 'root', 'enabled' => true,
                'firstName' => 'James T.', 'lastName' => 'Kirk', 'gender' => 'male', 'birthDate' => '1970-01-01'
            ],
            ['username' => 'user1', 'roles' => ['ROLE_USER'], 'password' => 'user1', 'enabled' => true,
                'firstName' => 'Deanna', 'lastName' => 'Troi', 'gender' => 'female', 'birthDate' => '2005-01-01'
            ],
            ['username' => 'user2', 'roles' => ['ROLE_USER'], 'password' => 'user2', 'enabled' => true,
                'firstName' => 'Nyota', 'lastName' => 'Uhura', 'gender' => 'female', 'birthDate' => '1987-01-01'
            ],
            ['username' => 'user3', 'roles' => ['ROLE_USER'], 'password' => 'user3', 'enabled' => true,
                'firstName' => 'Hikrau', 'lastName' => 'Sulu', 'gender' => 'male', 'birthDate' => '1990-01-01'
            ],
            ['username' => 'user4', 'roles' => ['ROLE_USER'], 'password' => 'user4', 'enabled' => false,
                'firstName' => 'Montgomery', 'lastName' => 'Scott', 'gender' => 'male', 'birthDate' => '1999-01-01'
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
        return 1;
    }
}