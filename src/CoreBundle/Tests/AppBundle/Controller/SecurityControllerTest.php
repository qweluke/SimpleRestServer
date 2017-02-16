<?php

namespace CoreBundle\Tests\AppBundle\Controller;

class SecurityControllerTest extends BaseTestController
{

    private $client;

    public function __construct()
    {
        $this->client = parent::createAuthenticatedClient('root','root');
    }

    public function testIndex()
    {
        $this->client->request('GET', '/api/user/');

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

}
