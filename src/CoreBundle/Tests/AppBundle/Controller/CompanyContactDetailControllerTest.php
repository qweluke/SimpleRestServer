<?php

namespace CoreBundle\Tests\AppBundle\Controller;


class CompanyContactDetailControllerTest extends BaseTestController
{

    private $client;

    public function __construct()
    {
        $this->client = parent::createAuthenticatedClient('root', 'root');
    }

    /**
     * we are creating new contact to test it details
     */
    public function testCreateNewContact()
    {
        $contactData = [
            'firstName' => 'Test PHP - CompanyContactDetailTest',
            'lastName' => 'Unit',
            'jobTitle' => 'phpUnit',
            'gender' => 'male',
            'birthDate' => '1987-01-01',
            'editableAll' => 1,
            'contactDetails' => [
                ['type' => 'EMAIL', 'value' => 'test@gmail.com'],
                ['type' => 'MOBILE', 'value' => '+48733974111'],

            ]
        ];

        $this->client->request('POST', '/api/contact/new', $contactData);

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $testContact = json_decode($this->client->getResponse()->getContent());

        $this->assertInternalType('object', $testContact);
        $this->assertInternalType('array', $testContact->contactDetails);
        $this->assertInternalType('object', $testContact->contactDetails[0]);
    }

    /**
     * /api/contact/{contact}/detail
     */
    public function testGetContactDetails()
    {
        $params = ['query' => 'CompanyContactDetailTest'];
        $this->client->request('GET', '/api/contact/', $params);
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $response = json_decode($this->client->getResponse()->getContent());

        $contact = $response->data[0];

        $this->client->request('GET', '/api/contact/' . $contact->id . '/detail');

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertInternalType('array', $response);
        $this->assertInternalType('object', $response[0]);
    }

    /**
     * adding new contact details to exsisitng contact
     * POST /api/contact/{contact}/new
     */
    public function testNewContactDetail()
    {
        $params = ['query' => 'CompanyContactDetailTest'];
        $this->client->request('GET', '/api/contact/', $params);
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $response = json_decode($this->client->getResponse()->getContent());

        $contact = $response->data[0];

        $contactData = [
            ['type' => 'EMAIL', 'value' => 'test@gmail.com'],
            ['type' => 'PHONE', 'value' => '+48322255225'],
            ['type' => 'MOBILE', 'value' => '+48733971111'],
            ['type' => 'FAX', 'value' => '+48322255555'],
        ];

        /** test email field */
        $this->client->request('POST', '/api/contact/' . $contact->id . '/detail/new', $contactData[0]);
        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertInternalType('object', $response);

        /** test phone field */
        $this->client->request('POST', '/api/contact/' . $contact->id . '/detail/new', $contactData[1]);
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertInternalType('object', $response);

        /** test mobile field */
        $this->client->request('POST', '/api/contact/' . $contact->id . '/detail/new', $contactData[2]);
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertInternalType('object', $response);

        /** test fax field */
        $this->client->request('POST', '/api/contact/' . $contact->id . '/detail/new', $contactData[3]);
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertInternalType('object', $response);

        /** test WRONG field */
        $this->client->request('POST', '/api/contact/' . $contact->id . '/detail/new', []);
        $this->assertFalse($this->client->getResponse()->isSuccessful());
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());

    }

    /**
     * PATCH /api/contact/{contact}/detail/{detail}
     */
    public function testPatchContactDetail()
    {
        $params = ['query' => 'CompanyContactDetailTest'];
        $this->client->request('GET', '/api/contact/', $params);
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $response = json_decode($this->client->getResponse()->getContent());

        $contact = $response->data[0];

        $this->client->request(
            'PATCH',
            '/api/contact/' . $contact->id . '/detail/' . $contact->contactDetails[0]->id,
            [
                'type' => 'EMAIL',
                'value' => 'update@outlook.com'
            ]
        );

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertInternalType('object', $response);
    }


    /**
     * DELETE /api/contact/{contact}/detail/{detail}
     */
    public function testDeleteContact()
    {
        $params = ['query' => 'CompanyContactDetailTest'];
        $this->client->request('GET', '/api/contact/', $params);
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $response = json_decode($this->client->getResponse()->getContent());

        $contact = $response->data[0];

        $this->client->request(
            'DELETE',
            '/api/contact/' . $contact->id . '/detail/' . $contact->contactDetails[0]->id
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        /** try to delete not existing contact */
        $this->client->request('DELETE', '/api/contact/' . $contact->id . '/detail/0');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());

        /** try to delete contact detail from different contact than submitted */

        $this->client->request('GET', '/api/contact/', []);
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $response = json_decode($this->client->getResponse()->getContent());
        $randomContact = $response->data[0]->contactDetails[0]->id;

        $this->client->request('DELETE', '/api/contact/' . $contact->id . '/detail/' . $randomContact);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());

        /** delete test contact */

        $this->client->request('DELETE', '/api/contact/' . $contact->id);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }


}
