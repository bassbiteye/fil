<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestcontrollerTest extends WebTestCase
{
    
    public function testAddPok()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'passer',
        ]);
      
        $crawler = $client->request('POST', '/api/addP',[],[],['CONTENT_TYPE'=>"application/json"],
        '{
           
            "raisonSocial": "sa",
            "ninea": 101236458974,
            "adresse":"dakar",
            "username":"775205028",
            "password":"passer",
            "nom":"amadou",
            "prenom":"niasse",
            "imageName":"bass.jpg"
           "telephone":775205028
           
        }');
         $test=$client->getResponse();
         var_dump($test);
        $this->assertSame(201,$client->getResponse()->getStatusCode());
        //$this->assertSelectorTextContains();
    }
    // public function testDepotok()
    // {
    //     $client = static::createClient([], [
    //         'PHP_AUTH_USER' => 'admin',
    //         'PHP_AUTH_PW'   => 'passer',
    //     ]);
    //     $crawler = $client->request('POST', '/api/depot',[],[],['CONTENT_TYPE'=>"application/json"],
    //     '{
           
    //         "numcompte":"1753459",
    //         "solde":100000
           
    //     }');
    //      $test=$client->getResponse();
    //    //  var_dump($test);
    //     $this->assertSame(200,$client->getResponse()->getStatusCode());
    //     //$this->assertSelectorTextContains();
    // }
    // public function testDepotko()
    // {     $client = static::createClient([], [
    //     'PHP_AUTH_USER' => 'admin',
    //     'PHP_AUTH_PW'   => 'passer',
    // ]);
    //     $crawler = $client->request('POST', '/api/depot',[],[],['CONTENT_TYPE'=>"application/json"],
    //     '{
    //         "numcompte":1753459,
    //         "solde":"null" 
    //     }');
    //      $test=$client->getResponse(); 
    //     // var_dump($test);
    //     $this->assertSame(500,$client->getResponse()->getStatusCode());
    //    // $this->assertSelectorTextContains();
    // }

}
