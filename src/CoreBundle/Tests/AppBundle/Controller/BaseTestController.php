<?php

namespace CoreBundle\Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseTestController extends WebTestCase
{

    /**
     * Create a client with a default Authorization header.
     *
     * @param $username
     * @param $password
     * @return \Symfony\Bundle\FrameworkBundle\Client
     * @throws \BadRequestHttpException
     */
    protected function createAuthenticatedClient($username, $password)
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/security/login',
            array(
                '_username' => $username,
                '_password' => $password,
            )
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        if(!$data['token']) {
            throw new \BadRequestHttpException('Unable to run test! Token not returned by API!');
        }

        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }
}
