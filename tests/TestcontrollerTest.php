<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestcontrollerTest extends WebTestCase
{


    public function testNew()
     {
         $client = static::createClient([],[
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'passer',
         ] 

         );
         $client->request('POST', '/api/addP',[],[],
         ['CONTENT_TYPE'=>"application/json"],
         '{
             "raisonSocial":"Sonatel",
             "ninea":"101236458974",
             "adresse":"Colobane",
             "username":"adm",
             "password":"passer",
             "prenom":"GAYE",
             "nom":"Doudou Mohamet ",
             "telephone":null,
             "imageName":"/home/bass-codeur/sites/projetfil/public/images/User/bass.jpg"
             }'

     );
     $a=$client->getResponse();
     var_dump($a);
     $this->assertSame(500,$client->getResponse()->getStatusCode());
     }

    public function testDepotok()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'passer',
        ]);
        $crawler = $client->request(
            'POST',
            '/api/depot',
            [],
            [],
            ['CONTENT_TYPE' => "application/json"],
            '{
           
            "numCompte":"1753459",
            "solde":100000
           
        }'
        );
        $test = $client->getResponse();
        //  var_dump($test);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        //$this->assertSelectorTextContains();
    }
    public function testDepotko()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'passer',
        ]);
        $client->request(
            'POST',
            '/api/depot',
            [],
            [],
            ['CONTENT_TYPE' => "application/json"],
            '{
            "numCompte":1753459,
            "solde":"null" 
        }'
        );
        $test = $client->getResponse();
        // var_dump($test);
        $this->assertSame(500, $client->getResponse()->getStatusCode());
        // $this->assertSelectorTextContains();
    }
    public function loginok()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'passer',
        ]);
        $crawler = $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => "application/json"],
            '{
           
            "username":"admin",
            "password":passer
           
        }'
        );
        $test = $client->getResponse();
        //  var_dump($test);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        //$this->assertSelectorTextContains();
    }
    public function testloginko()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'passer',
        ]);
        $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => "application/json"],
            '{
          
            "username":"admin",
            "password":"null"
        }'
        );
        $test = $client->getResponse();
        // var_dump($test);
        $this->assertSame(200, $test->getStatusCode());
        // $this->assertSelectorTextContains();
    }
    public function register()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW'   => 'passer',
            ]

        );
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => "application/json"],
            '{
           
            "username":"admin",
            "password":"passer",
            "prenom":"GAYE",
            "nom":"Doudou Mohamet ",
            "telephone":782257053,
            "imageName":"/home/bass-codeur/sites/projetfil/public/images/User/bass.jpg"
            }'

        );
        $a = $client->getResponse();
        var_dump($a);
        $this->assertSame(201, $client->getResponse()->getStatusCode());
    }
    public function testliste()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW'   => 'passer',
            ]

        );
        $crawler = $client->request('GET', '/api/liste');
        $client->getResponse();
        $this->assertSame(200,$client->getResponse()->getStatusCode());
     
    }
    public function testlisteko()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW'   => 'passer',
            ]

        );
        $crawler = $client->request('GET', '/api/history');
        $client->getResponse();
        $this->assertSame(200,$client->getResponse()->getStatusCode());
     
    }
}
