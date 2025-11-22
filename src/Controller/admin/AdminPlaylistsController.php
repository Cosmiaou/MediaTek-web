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
 * Description of AdminPlaylistsController
 */
class AdminPlaylistsController extends AbstractController {
    /**
     *
     * @var formationRepository
     */
    private $formationRepository;
    
    private $categorieRepository;
    
    private $playlistRepository;
    
    /**
     * Chemin d'accès de la liste des playlists
     */
    private const ADMINPLAYLIST = "admin/admin.playlists.html.twig";
    
    public function __construct(PlaylistRepository $playlistRepository, FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->playlistRepository = $playlistRepository;
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    #[Route('/admin/playlists', name: 'admin.playlists')]
    public function index() : Response {
        $playlists = $this->playlistRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        $nbFormations = $this->numberFormation($playlists);
        
        return $this->render(self::ADMINPLAYLIST, [
            'playlists' => $playlists,
            'nbFormations' => $nbFormations,
            'categories' => $categories
        ]);
    }
    
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
     * Vérifie si la playlist demandée est vide. Si oui, la supprime. Si non, renvoi une erreur.
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
     * Permet la modification d'une Playlist existante.
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
     * Permet l'ajout d'une Playlist supplémentaire.
     * La variable $formations est null et doit être ignorée. Elle est nécessaire pour le Formulaire.
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
     * Reçoit un array de liste de Playlist, et renvoi un array associant l'id d'une Playlist
     * au nombre de Formation contenue
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
