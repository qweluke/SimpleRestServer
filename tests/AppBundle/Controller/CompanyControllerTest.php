<?php

namespace Tests\AppBundle\Controller;


class CompanyControllerTest extends BaseTestController
{

    private $client;

    public function __construct()
    {
        $this->client = parent::createAuthenticatedClient('root', 'root');
    }

    /**
     * /api/company/
     */
    public function testGetCompanies()
    {
        $this->client->request('GET', '/api/company/');

        $users = json_decode($this->client->getResponse()->getContent());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertInternalType('array', $users->data);
        $this->assertInternalType('object', $users->data[0]);
    }

    /**
     * /api/company/new
     */
    public function testNewCompany()
    {
        $data = [
            'name' => 'Test PHP',
            'description' => 'Unit',
        ];

        $this->client->request('POST', '/api/company/new', $data);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * GET /api/company/{company}
     */
    public function testGetCompany()
    {

        $params = ['query' => 'Test PHP'];
        $this->client->request('GET', '/api/company/', $params);

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $response = json_decode($this->client->getResponse()->getContent());

        $this->client->request('GET', '/api/company/' . $response->data[0]->id);


        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertInternalType('object', $response->data[0]);
    }

    /**
     * PATCH /api/company/{company}
     */
    public function testPatchCompany()
    {
        $params = ['query' => 'Test PHP'];
        $this->client->request('GET', '/api/company/', $params);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $response = json_decode($this->client->getResponse()->getContent());

        $user1 = parent::createAuthenticatedClient('user1', 'user1');


        $user1->request('PATCH', '/api/company/' . $response->data[0]->id, [
            'description' => 'user1',
        ]);

        $this->client->request('PATCH', '/api/company/' . $response->data[0]->id, [
            'description' => 'root',
        ]);

        $this->assertTrue($user1->getResponse()->isSuccessful());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * DELETE /api/company/{company}/{contact}
     */
    public function testDeleteCompanyContact()
    {

        $params = ['query' => 'Test PHP'];
        $this->client->request('GET', '/api/company/', $params);
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $contactData = [
            'firstName' => 'Test PHP',
            'lastName' => 'Unit',
            'company' => $response->data[0]->id,
            'jobTitle' => 'phpUnit',
            'birthDate' => '1987-01-01',
            'editableAll' => 1,
        ];

        $this->client->request('POST', '/api/contact/new', $contactData);

        $contact = json_decode($this->client->getResponse()->getContent());

        $user1 = parent::createAuthenticatedClient('user1', 'user1');

        $user1->request('DELETE', '/api/company/' . $response->data[0]->id . '/' . $contact->id);
        $this->assertTrue($user1->getResponse()->isSuccessful());
    }

    /**
     * DELETE /api/company/{company}
     */
    public function testDeleteCompany()
    {

        $params = ['query' => 'Test PHP'];
        $this->client->request('GET', '/api/company/', $params);
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        /**
         * we are creating some contacts and assign them to the newly created company
         * just to be sure that they will be removed after company deleting
         */

        for ($i = 0; $i < 3; $i++) {
            $contactData = [
                'firstName' => 'Test PHP - ' . $i,
                'lastName' => 'Unit',
                'company' => $response->data[0]->id,
                'jobTitle' => 'phpUnit',
                'birthDate' => '1987-01-01',
                'editableAll' => 0,
            ];

            $this->client->request('POST', '/api/contact/new', $contactData);
        }

        $user1 = parent::createAuthenticatedClient('user1', 'user1');

        $user1->request('DELETE', '/api/company/' . $response->data[0]->id);
        $this->assertTrue($user1->getResponse()->isSuccessful());
    }


}
