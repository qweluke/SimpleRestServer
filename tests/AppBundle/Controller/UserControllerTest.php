<?php

namespace Tests\AppBundle\Controller;


use CoreBundle\Entity\User;

class UserControllerTest extends BaseTestController
{

    private $client;

    public function __construct()
    {
        $this->client = parent::createAuthenticatedClient('root', 'root');
    }

    /**
     * /api/user/
     */
    public function testGetUsers()
    {
        $this->client->request('GET', '/api/user/');

        $users = json_decode($this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertInternalType('array', $users->data);
        $this->assertInternalType('object', $users->data[0]);
    }

    /**
     * /api/user/new
     */
    public function testNewUser()
    {
        $userData = [
            'username' => 'phpUnit',
            'email' => 'phpUnit@unit.local',
            'firstName' => 'Test PHP',
            'lastName' => 'Unit',
            'plainPassword' => 'phpUnit',
            'enabled' => 1,
            'gender' => 'male',
            'birthDate' => '1987-01-01',
            'roles' => [
                'admin'
            ]
        ];

        $user1 = parent::createAuthenticatedClient('user1', 'user1');

        $user1->request('POST', '/api/user/new', $userData);
        $this->client->request('POST', '/api/user/new', $userData);

        $this->assertEquals(403, $user1->getResponse()->getStatusCode());

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * GET /api/user/{user}
     */
    public function testGetUser()
    {

        $params = ['query' => 'phpUnit'];
        $this->client->request('GET', '/api/user/', $params);
        $response = json_decode($this->client->getResponse()->getContent());
        $user = $response->data[0];

        $this->client->request('GET', '/api/user/' . $user->id);

        $user = json_decode($this->client->getResponse()->getContent());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertInternalType('object', $user);
    }

    /**
     * PATCH /api/user/{user}
     */
    public function testPatchUser()
    {
        $params = ['query' => 'phpUnit'];
        $this->client->request('GET', '/api/user/', $params);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $response = json_decode($this->client->getResponse()->getContent());

        /** @var $user CoreBundle:User */
        $user = $response->data[0];

        $userData = [
            'email' => 'phpUnit_1@unit.local',
            'firstName' => 'Test PHP 1',
            'lastName' => 'Unit 1',
            'plainPassword' => 'phpUnit_2',
            'enabled' => 0,
            'gender' => 'female',
            'birthDate' => '1988-01-01',
            'roles' => [
                'admin'
            ]
        ];

        $user1 = parent::createAuthenticatedClient('user1', 'user1');

        $user1->request('PATCH', '/api/user/' . $user->id, $userData);
        $this->client->request('PATCH', '/api/user/' . $user->id, $userData);

        $this->assertEquals(403, $user1->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * DELETE /api/user/{user}
     */
    public function testDeleteUser()
    {

        $params = ['query' => 'phpUnit'];
        $this->client->request('GET', '/api/user/', $params);
        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $user1 = parent::createAuthenticatedClient('user1', 'user1');

        $user1->request('DELETE', '/api/user/' . $response->data[0]->id);
        $this->client->request('DELETE', '/api/user/' . $response->data[0]->id);

        $this->assertEquals(403, $user1->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * PATCH /api/user/
     */
    public function testPatchLoggedUser()
    {
        $userData = [
            'firstName' => 'Test PHP 1',
            'lastName' => 'Unit 1',
            'gender' => 'female',
            'birthDate' => '1988-01-01',
        ];

        $user1 = parent::createAuthenticatedClient('user1', 'user1');

        $user1->request('PATCH', '/api/user/', $userData);
        $this->client->request('PATCH', '/api/user/', $userData);

        $this->assertTrue($user1->getResponse()->isSuccessful());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }



}
