<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of AdminFormationControllerTest
 *
 * @author 
 */
class AdminFormationsControllerTest extends WebTestCase
{
    
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

    public function testTriFormationDesc()
    {
        $client = $this->connexion();
        $crawler = $client->request('GET', '/admin/formations/tri/title/DESC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('UML : Diagramme de paquetages', $premierTitre);
    }
    
    public function testTriFormationAsc()
    {
        $client = $this->connexion();
        $crawler = $client->request('GET', '/admin/formations/tri/title/ASC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('Android Studio (complément n°1) : Navigation Drawer et Fragment', $premierTitre);
    }
    
    public function testTriPlaylistAsc()
    {
        $client = $this->connexion();
        $crawler = $client->request('GET', '/admin/formations/tri/name/ASC/playlist');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('test', $premierTitre);
    }
    
    public function testTriPlaylistDesc()
    {
        $client = $this->connexion();
        $crawler = $client->request('GET', '/admin/formations/tri/name/DESC/playlist');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('C# : ListBox en couleur', $premierTitre);
    }
    
    public function testTriDateAsc()
    {
        $client = $this->connexion();
        $crawler = $client->request('GET', '/admin/formations/tri/publishedAt/ASC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals("Cours UML (1 à 7 / 33) : introduction et cas d'utilisation", $premierTitre);
    }
    
    public function testTriDateDesc()
    {
        $client = $this->connexion();
        $crawler = $client->request('GET', '/admin/formations/tri/publishedAt/DESC');
        $premierTitre = $crawler->filter('h5')->first()->text();
        $this->assertEquals('testdate', $premierTitre);
    }
    
    public function testFiltreFormations()
    {
        $client = $this->connexion();
        $client->request('GET', '/admin');
        $this->assertSelectorTextContains('th', 'formation');
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'TP Android n°9 : base de données distante MySQL (1)'
        ]);
        $this->assertCount(1, $crawler->filter('h5'));
        $this->assertSelectorTextContains('h5', 'TP Android n°9 : base de données distante MySQL (1)');
    }
    
    public function testFiltrePlaylists()
    {
        $client = $this->connexion();
        $crawler = $client->request('GET', '/admin');
        $form = $crawler->filter('th:contains("playlist") form')->form([
            'recherche' => 'uml'
        ]);
        $crawler = $client->submit($form);
        $this->assertCount(10, $crawler->filter('h5'));
        $this->assertSelectorTextContains('h5', 'UML : Diagramme de paquetages');
    }
    
    public function testFiltreCategories()
    {
        $client = $this->connexion();
        $crawler = $client->request('GET', '/admin');
        $form = $crawler->filter('form')->eq(2)->form(['recherche' => 2]);
        $crawler = $client->submit($form);
        $this->assertStringContainsString(
            'UML',
            $crawler->filter('h5')->first()->text()
        );
        $this->assertCount(15, $crawler->filter('tbody tr'));
    }
    
    public function testClicEditer()
    {
        $client = $this->connexion();
        $client->request('GET', '/admin');
        $client->clickLink('Editer');
        $this->assertEquals('http://localhost/admin/formation/edit/2', $client->getRequest()->getUri());
        $this->assertSelectorTextContains('h2', 'Modifier une formation :');
    }
    
    public function testClicSuppr()
    {
        $client = $this->connexion();
        $client->request('GET', '/admin');
        $client->clickLink('Supprimer');
        $this->assertEquals('http://localhost/admin/formation/suppr/2', $client->getRequest()->getUri());
    }
    
    public function testClicAjouter()
    {
        $client = $this->connexion();
        $client->request('GET', '/admin');
        $client->clickLink('Ajouter');
        $this->assertEquals('http://localhost/admin/formation/ajout', $client->getRequest()->getUri());
        $this->assertSelectorTextContains('h2', 'Ajouter une formation :');
    }
}
