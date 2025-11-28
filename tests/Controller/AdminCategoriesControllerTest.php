<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of AdminCategoriesControllerTest
 *
 * @author 
 */
class AdminCategoriesControllerTest extends WebTestCase {
    /**
     * Se connecte et crée un client de connexion. Si la connexion échoue, renvoi une erreur
     * @return type
     * @throws \Exception
     */
    private function connexion()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('se connecter')->form();
        $form['_username'] = 'admin';
        $form['_password'] = 'W@Ch@oseT@G@T@Th@M@on010823';

        $crawler = $client->submit($form);
        $client->followRedirect();
        $uri = $client->getRequest()->getUri();
        if (str_contains($uri, '/login')) {
            throw new \Exception("Connexion échouée, toujours sur la page login");
        }
        return $client;
    }
    
    /**
     * Crée une copie du fichier html contenu dans la page lue par le test.
     * Fonction ayant été utile pour concevoir les tests.
     * @param type $crawler
     */
    private function debug($crawler)
    {
        file_put_contents(__DIR__.'/debug.html', $crawler->html());
    }
    
    public function testAccessPage()
    {
        $client = $this->connexion();
        $crawler = $client->request('GET', 'admin/categories');
        $this->assertResponseStatusCodeSame('200');
        $first = $crawler->filter('h5')->first()->text();
        $this->assertEquals('Java', $first);
    }
    
    public function testAjout()
    {
        $client = $this->connexion();
        $client->request('GET', '/admin/categories');
        $crawler = $client->submitForm('Ajouter', [
            'nom' => 'testajout'
        ]);
        $crawler = $client->followRedirect();
        $last = $crawler->filter('h5')->last()->text();
        $this->assertEquals('testajout', $last);
    }
    
    public function testClicSuppr()
    {
        $client = $this->connexion();
        $client->request('GET', '/admin/categories');
        $client->clickLink('Supprimer');
        $this->assertEquals('http://localhost/admin/categorie/suppr/1', $client->getRequest()->getUri());
    }
}
