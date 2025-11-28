<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of AcceuilControllerTest
 *
 * @author 
 */
class AccueilControllerTest extends WebTestCase {
    
    public function testAccessPage()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame('200');
    }
    
}