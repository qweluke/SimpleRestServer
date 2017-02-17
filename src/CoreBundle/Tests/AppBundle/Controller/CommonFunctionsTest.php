<?php

namespace CoreBundle\Tests\AppBundle\Controller;


class CommonFunctionsTest extends BaseTestController
{

    private $client;

    public function __construct()
    {
        $this->client = parent::createAuthenticatedClient('root', 'root');
    }

    /**
     * test not found exception
     */
    public function testNotFoundException()
    {
        $this->client->request('GET', '/api/this-route-does-not-exsists/');
        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertInternalType('object', $response);
    }

}
