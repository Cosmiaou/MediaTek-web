<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of AdminPlaylistsControllerTest
 *
 * @author 
 */
class AdminPlaylistsControllerTest extends WebTestCase {
    
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

    public function testTriPlaylistDesc()
    {
        $client = $this->connexion();
        $crawler = $client->request('GET', '/admin/playlists/tri/name/DESC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('Visual Studio 2019 et C#', $premierTitre);
    }
    
    public function testTriPlaylistAsc()
    {
        $client = $this->connexion();
        $crawler = $client->request('GET', '/admin/playlists/tri/name/ASC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('3e test', $premierTitre);
    }
    
    public function testTriNombreAsc()
    {
        $client = $this->connexion();
        $crawler = $client->request('GET', '/admin/playlists/tri/number/ASC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('Bases de la programmation (C#)', $premierTitre);
    }
    
    public function testTriNombreDesc()
    {
        $client = $this->connexion();
        $crawler = $client->request('GET', '/admin/playlists/tri/number/DESC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('Cours Modèle relationnel et MCD', $premierTitre);
    }
    
    public function testFiltrePlaylists()
    {
        $client = $this->connexion();
        $client->request('GET', 'admin/playlists');
        $this->assertSelectorTextContains('th', 'playlist');
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Cours Informatique embarquée'
        ]);
        $this->assertCount(2, $crawler->filter('h5'));
        $this->assertSelectorTextContains('h5', 'Cours Informatique embarquée');
    }
    
    public function testFiltreCategories()
    {
        $client = $this->connexion();
        $crawler = $client->request('GET', '/admin/playlists');
        $form = $crawler->filter('form')->eq(1)->form(['recherche' => 2]);
        $crawler = $client->submit($form);
        $this->assertStringContainsString(
            'UML',
            $crawler->filter('h5')->first()->text()
        );
        $this->assertCount(3, $crawler->filter('tbody tr'));
    }
    
    public function testClicEditer()
    {
        $client = $this->connexion();
        $client->request('GET', '/admin/playlists');
        $client->clickLink('Editer');
        $this->assertEquals('http://localhost/admin/playlist/edit/1', $client->getRequest()->getUri());
        $this->assertSelectorTextContains('h2', 'Modifier une playlist :');
    }
    
    public function testClicSuppr()
    {
        $client = $this->connexion();
        $client->request('GET', '/admin/playlists');
        $client->clickLink('Supprimer');
        $this->assertEquals('http://localhost/admin/playlist/suppr/1', $client->getRequest()->getUri());
    }
    
    public function testClicAjouter()
    {
        $client = $this->connexion();
        $client->request('GET', '/admin/playlists');
        $client->clickLink('Ajouter');
        $this->assertEquals('http://localhost/admin/playlist/ajout', $client->getRequest()->getUri());
        $this->assertSelectorTextContains('h2', 'Ajouter une playlist :');
    }
}
