<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller pour la page des Playlists ('/playlists'), ainsi que pour les pages de consultation d'une Playlist
 * @author emds
 */
class PlaylistsController extends AbstractController
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
    private const PAGEPLAYLISTS = "pages/playlists.html.twig";
    
    /**
     * Constructeur. Initialise les trois repository
     * @param PlaylistRepository $playlistRepository
     * @param CategorieRepository $categorieRepository
     * @param FormationRepository $formationRespository
     */
    public function __construct(
            PlaylistRepository $playlistRepository,
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * Retourne /playlists, contenant la liste des playlists ('playlists'),celle des catégories ('categories'),
     * ainsi qu'un array contenant le nombre de Formations pour chaque playlist ('nbFormations')
     * @return Response
     */
    #[Route('/playlists', name: 'playlists')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        $nbFormations = $this->numberFormation($playlists);
        
        return $this->render(self::PAGEPLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,
            'nbFormations' => $nbFormations
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
    #[Route('/playlists/tri/{champ}/{ordre}', name: 'playlists.sort')]
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
        
        return $this->render(self::PAGEPLAYLISTS, [
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
    #[Route('/playlists/recherche/{champ}/{table}', name: 'playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        if (!$this->isCsrfTokenValid('filtre_'.$champ, $request->get('_token'))) {
           throw $this->createAccessDeniedException("Erreur de sécurité : veuillez réessayer"); 
        }
        
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        $nbFormations = $this->numberFormation($playlists);
        
        return $this->render(self::PAGEPLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table,
            'nbFormations' => $nbFormations
        ]);
    }

    /**
     * Renvoie la page d'affichage de la Playlist dont l'id est passé en paramètre.
     * Ajoute au rendu les formations de cette playlists, ainsi que toutes les catégories de ces formations, et le
     * nombre total de formation. La page affiche une liste des formations de la playlists.
     * @param type $id
     * @return Response
     */
    #[Route('/playlists/playlist/{id}', name: 'playlists.showone')]
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("pages/playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations,
            'nbFormations' => count($playlistFormations)
        ]);
    }
    
    
    /**
     * Reçoit un array de Playlist, et renvoi un array associant l'id d'une Playlist au nombre de Formation contenue
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
