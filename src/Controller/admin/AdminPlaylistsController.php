<?php

namespace App\Controller\admin;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller pour la page de gestion des Playlists ('admin/playlists'), ainsi que pour les pages d'ajout ou de
 * modification de playlist
 */
class AdminPlaylistsController extends AbstractController
{
    
    /**
     * Contient l'instance du repository de Formation
     * @var formationRepository
     */
    private $formationRepository;
    
    /**
     * Contient l'instance du repository de Categorie
     * @var categoryRepository
     */
    private $categorieRepository;
    
    /**
     * Contient l'instance du repository de Playlist
     * @var playlistRepository
     */
    private $playlistRepository;
    
    /**
     * Chemin d'accès de la liste des playlists
     */
    private const ADMINPLAYLIST = "admin/admin.playlists.html.twig";
    
     /**
     * Constructeur. Initialise les trois repository
     * @param PlaylistRepository $playlistRepository
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(PlaylistRepository $playlistRepository, FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->playlistRepository = $playlistRepository;
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * Retourne /admin/playlists, contenant la liste des playlists ('playlists'),celle des catégories ('categories'),
     * ainsi qu'un array contenant le nombre de Formations pour chaque playlist ('nbFormations')
     * @return Response
     */
    #[Route('/admin/playlists', name: 'admin.playlists')]
    public function index() : Response
    {
        $playlists = $this->playlistRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        $nbFormations = $this->numberFormation($playlists);
        
        return $this->render(self::ADMINPLAYLIST, [
            'playlists' => $playlists,
            'nbFormations' => $nbFormations,
            'categories' => $categories
        ]);
    }
    
    /**
     * Retourne la liste des Playlists, mais triée en fonction des éléments sélectionnés.
     * "champ" est vide par défaut. Sans paramètre, la fonction tri par ordre alphabétique du nom des Playlists
     * Si on souhaite le tri en fonction du nombre de formations par playlist, il faut alors choisir insérer
     * 'number' en paramètre en tant que $champ.
     * Le code appelle ensuite la fonction PHP usort pour trier celles-ci selon l'ordre indiqué.
     * @param type $ordre
     * @param type $champ
     * @return Response
     */
    #[Route('/admin/playlists/tri/{champ}/{ordre}', name: 'admin.playlists.sort')]
    public function sort($ordre, $champ=""): Response
    {
        $categories = $this->categorieRepository->findAll();
        $playlists = $this->playlistRepository->findAllOrderByName($ordre);
        $nbFormations = $this->numberFormation($playlists);
        
        if ($champ == "number") {
            usort($playlists, function($a, $b) use ($nbFormations, $ordre) {
                $countA = $nbFormations[$a->getId()] ?? 0;
                $countB = $nbFormations[$b->getId()] ?? 0;
                if ($ordre == 'DESC') {
                    return $countA <=> $countB;
                } else {
                    return $countB <=> $countA;
                }
            });
        }
        
        return $this->render(self::ADMINPLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories,
            'nbFormations' => $nbFormations
        ]);
    }

    /**
     * Retourne la liste des Playlists, mais filtrées en fonction de l'élément indiquée dans le form "recherche"
     * "$table" est vide par défaut.
     * En plus des éléments habituels, ajoute au rendu 'valeur' et 'table'
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    #[Route('/admin/playlists/recherche/{champ}/{table}', name: 'admin.playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        $nbFormations = $this->numberFormation($playlists);
        
        return $this->render(self::ADMINPLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table,
            'nbFormations' => $nbFormations
        ]);
    }
    
    /**
     * Vérifie si la playlist demandée est vide. Si oui, la supprime. Si non, renvoie une erreur en message Flash
     * Dans tous les cas, recharge la page.
     * @param int $id
     * @return Response
     */
    #[Route('/admin/playlist/suppr/{id}', name: 'admin.playlist.suppr')]
    public function suppr(int $id): Response {
        $formations = $this->formationRepository->findAllForOnePlaylist($id);
        
        if (count($formations) > 0) {
            $this->addFlash('error', 'Erreur ! On ne peut pas supprimer une playlist qui contient des formations.');
        } else {
            $playlist = $this->playlistRepository->find($id);
            $this->playlistRepository->remove($playlist);
        }
        return $this->redirectToRoute('admin.playlists');
    }
    
    /**
     * Redirige l'utilisateur vers un formulaire de modification de la Playlist dont l'id est passé en paramètre.
     * Ajoute au rendu la liste des Formations de la playlist, qui s'affiche sur la page.
     * @param int $id
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/playlist/edit/{id}', name: 'admin.playlist.edit')]
    public function edit(int $id, Request $request): Response {
        $playlist = $this->playlistRepository->find($id);
        $formations = $this->formationRepository->findAllForOnePlaylist($id);
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        
        $formPlaylist->handleRequest($request);
        if($formPlaylist->isSubmitted() && $formPlaylist->isValid()) {
            $this->playlistRepository->add($playlist);
            return $this->redirectToRoute('admin.playlists');
        }
        
        return $this->render("admin/admin.playlist.edit.html.twig", [
            'playlist' => $playlist,
            'playlistformations' => $formations,
            'formplaylist' => $formPlaylist->createView(),
            'nbFormations' => count($formations)
        ]);
    }
    
    /**
     * Redirige l'utilisateur vers un formulaire d'ajout d'une nouvelle Playlist
     * La variable $formations est null et doit être ignorée. Elle est nécessaire pour le formulaire.
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/playlist/ajout', name: 'admin.playlist.ajout')]
    public function ajout(Request $request): Response {
        $playlist = new Playlist();
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        $formations = null;
        
        $formPlaylist->handleRequest($request);
        if($formPlaylist->isSubmitted() && $formPlaylist->isValid()) {
            $this->playlistRepository->add($playlist);
            return $this->redirectToRoute('admin.playlists');
        }
        
        return $this->render("admin/admin.playlist.ajout.html.twig", [
            'playlist' => $playlist,
            'playlistformations' => $formations,
            'formplaylist' => $formPlaylist->createView()
        ]);
    }
    
    /**
     * Reçoit un array de Playlistd, et renvoi un array associant l'id d'une Playlist au nombre de Formation contenue
     * @param type $playlists
     * @return array
     */
    private function numberFormation($playlists) : array {
        $nbFormations = [];
        foreach ($playlists as $playlist) {
            $id = $playlist->getId();
            $listeFormation = $this->formationRepository->findAllForOnePlaylist($id);
            $nbFormations[$id] = count($listeFormation);
        }
        return $nbFormations;
    }
    
}
