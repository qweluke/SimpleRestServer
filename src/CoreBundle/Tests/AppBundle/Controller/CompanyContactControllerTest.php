<?php

namespace CoreBundle\Tests\AppBundle\Controller;


class CompanyContactControllerTest extends BaseTestController
{

    private $client;

    public function __construct()
    {
        $this->client = parent::createAuthenticatedClient('root', 'root');
    }

    /**
     * /api/contact/
     */
    public function testGetContacts()
    {
        $this->client->request('GET', '/api/contact/');

        $users = json_decode($this->client->getResponse()->getContent());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertInternalType('array', $users->data);
        $this->assertInternalType('object', $users->data[0]);
    }

    /**
     * /api/contact/new
     */
    public function testNewContact()
    {
        $contactData = [
            'firstName' => 'Test PHP',
            'lastName' => 'Unit',
            'jobTitle' => 'phpUnit',
            'gender' => 'male',
            'birthDate' => '1987-01-01',
            'editableAll' => 0,
        ];

        $this->client->request('POST', '/api/contact/new', $contactData);

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->client->request('POST', '/api/contact/new', []);

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());

    }

    /**
     * GET /api/contact/{user}
     */
    public function testGetContact()
    {

        $params = ['query' => 'Test PHP'];
        $this->client->request('GET', '/api/contact/', $params);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $response = json_decode($this->client->getResponse()->getContent());

        $this->client->request('GET', '/api/contact/' . $response->data[0]->id);


        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertInternalType('object', $response->data[0]);
    }

    /**
     * PATCH /api/contact/{user}
     */
    public function testPatchContact()
    {
        $params = ['query' => 'Test PHP'];
        $this->client->request('GET', '/api/contact/', $params);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $response = json_decode($this->client->getResponse()->getContent());

        $contactData = [
            'lastName' => 'Unit new',
            'jobTitle' => 'php developer',
            'birthDate' => '1988-01-01',
            'contactDetails' => [
                ['type' => 'PHONE', 'value' => '+48322255873'],
                ['type' => 'EMAIL', 'value' => 'thisis@gmail.com']
            ]
        ];

        $user1 = parent::createAuthenticatedClient('user1', 'user1');

        $user1->request('PATCH', '/api/contact/' . $response->data[0]->id, $contactData);
        $this->client->request('PATCH', '/api/contact/' . $response->data[0]->id, $contactData);

        $this->assertEquals(403, $user1->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        /** test wrong contact data */
        $this->client->request('PATCH', '/api/contact/' . $response->data[0]->id, ['editableAll' => 3]);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());


        /** test wrong contact details */
        $wrongContactData = [
            'lastName' => 'Unit new',
            'jobTitle' => 'php developer',
            'birthDate' => '1988-01-01',
            'contactDetails' => [
                ['type' => 'PHONE', 'value' => 'wrongNumber'],
                ['type' => 'MOBILE', 'value' => 'wrongNumber'],
                ['type' => 'EMAIL', 'value' => '123000']
            ]
        ];

        $this->client->request('PATCH', '/api/contact/' . $response->data[0]->id,$wrongContactData);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    /**
     * DELETE /api/contact/{user}
     */
    public function testDeleteContact()
    {

        $params = ['query' => 'Test PHP'];
        $this->client->request('GET', '/api/contact/', $params);
        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $user1 = parent::createAuthenticatedClient('user1', 'user1');

        $user1->request('DELETE', '/api/contact/' . $response->data[0]->id);
        $this->client->request('DELETE', '/api/contact/' . $response->data[0]->id);

        $this->assertEquals(403, $user1->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }


}
