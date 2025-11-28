<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of FormationControllerTest
 *
 * @author 
 */
class FormationsControllerTest extends WebTestCase {
    
    public function testTriFormationDesc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/title/DESC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('UML : Diagramme de paquetages', $premierTitre);
    }
    
    public function testTriFormationAsc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/title/ASC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('Android Studio (complément n°1) : Navigation Drawer et Fragment', $premierTitre);
    }
    
    public function testTriPlaylistAsc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/name/ASC/playlist');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('test', $premierTitre);
    }
    
    public function testTriPlaylistDesc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/name/DESC/playlist');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('C# : ListBox en couleur', $premierTitre);
    }
    
    public function testTriDateAsc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/publishedAt/ASC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals("Cours UML (1 à 7 / 33) : introduction et cas d'utilisation", $premierTitre);
    }
    
    public function testTriDateDesc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/publishedAt/DESC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('testdate', $premierTitre);
    }
    
    public function testFiltreFormations()
    {
        $client = static::createClient();
        $client->request('GET', '/formations');
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'TP Android n°9 : base de données distante MySQL (1)'
        ]);
        $this->assertCount(1, $crawler->filter('h5'));
        $this->assertSelectorTextContains('h5', 'TP Android n°9 : base de données distante MySQL (1)');
    }
    
    public function testFiltrePlaylists()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations');
        $form = $crawler->filter('th:contains("playlist") form')->form([
            'recherche' => 'uml'
        ]);
        $crawler = $client->submit($form);
        $this->assertCount(10, $crawler->filter('h5'));
        $this->assertSelectorTextContains('h5', 'UML : Diagramme de paquetages');
    }
    
    public function testFiltreCategories()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations');
        $form = $crawler->filter('form')->eq(2)->form(['recherche' => 2]);
        $crawler = $client->submit($form);
        $this->assertStringContainsString(
            'UML',
            $crawler->filter('h5')->first()->text()
        );
        $this->assertCount(14, $crawler->filter('h5'));
    }
    
    public function testClicFormation()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations');
        $link = $crawler->filter('tbody tr')->first()
                ->filter('td')->eq(4)
                ->filter('a')->link();
        $crawler = $client->click($link);
        $this->assertEquals('http://localhost/formations/formation/2', $link->getURI());
        $this->assertSelectorTextContains('h4', 'Eclipse n°7 : Tests unitaire');
    }
}
