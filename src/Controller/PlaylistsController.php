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
 * Description of PlaylistsController
 *
 * @author emds
 */
class PlaylistsController extends AbstractController
{
    
    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
    /**
     *
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    /**
     * Chemin d'accès de la liste des playlists
     */
    private const PAGEPLAYLISTS = "pages/playlists.html.twig";
    
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
     * @Route("/playlists", name="playlists")
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

    #[Route('/playlists/recherche/{champ}/{table}', name: 'playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
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
