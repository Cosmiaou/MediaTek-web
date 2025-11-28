<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of PlaylistsControllerTest
 *
 * @author 
 */
class PlaylistsControllerTest extends WebTestCase {
    
    public function testTriPlaylistDesc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/name/DESC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('Visual Studio 2019 et C#', $premierTitre);
    }
    
    public function testTriPlaylistAsc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/name/ASC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('3e test', $premierTitre);
    }
    
    public function testTriNombreAsc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/number/ASC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('Bases de la programmation (C#)', $premierTitre);
    }
    
    public function testTriNombreDesc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/number/DESC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('Cours Modèle relationnel et MCD', $premierTitre);
    }
    
    public function testFiltrePlaylists()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists');
        $this->assertSelectorTextContains('th', 'playlist');
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Cours Informatique embarquée'
        ]);
        $this->assertCount(2, $crawler->filter('h5'));
        $this->assertSelectorTextContains('h5', 'Cours Informatique embarquée');
    }
    
    public function testFiltreCategories()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists');
        $form = $crawler->filter('form')->eq(1)->form(['recherche' => 2]);
        $crawler = $client->submit($form);
        $this->assertStringContainsString(
            'UML',
            $crawler->filter('h5')->first()->text()
        );
        $this->assertCount(3, $crawler->filter('h5'));
    }
    
    public function testClicPlaylist()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists');
        $link = $crawler->filter('tbody tr')->first()
                ->filter('td')->eq(3)
                ->filter('a')->link();
        $crawler = $client->click($link);
        $this->assertEquals('http://localhost/playlists/playlist/32', $link->getURI());
        $this->assertSelectorTextContains('h4', '3e test');
    }
}