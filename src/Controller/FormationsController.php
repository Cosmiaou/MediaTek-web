<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller pour la page des Formations ('/formations'), ainsi que pour les pages de consultation d'une Formation
 * @author emds
 */
class FormationsController extends AbstractController
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
     * Chemin d'accès de la liste des formations
     */
    private const PAGEFORMATION = "pages/formations.html.twig";
    
    /**
     * Constructeur. Initialise les deux repository
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * Retourne /formations, contenant la liste des formations ('formations'),celle des catégories ('categories')
     * @return Response
     */
    #[Route('/formations', name: 'formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGEFORMATION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * Retourne la liste des Formations, mais triée en fonction des éléments sélectionnés.
     * "$table" est vide par défaut.
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    #[Route('/formations/tri/{champ}/{ordre}/{table}', name: 'formations.sort')]
    public function sort($champ, $ordre, $table=""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGEFORMATION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * Retourne la liste des Formations, mais filtrées en fonction de l'élément indiquée dans le form "recherche"
     * "$table" est vide par défaut.
     * En plus des éléments habituels, ajoute au rendu 'valeur' et 'table'
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    #[Route('/formations/recherche/{champ}/{table}', name: 'formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGEFORMATION, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * Renvoie la page d'affichage de la Formation dont l'id est passé en paramètre.
     * @param type $id
     * @return Response
     */
    #[Route('/formations/formation/{id}', name: 'formations.showone')]
    public function showOne($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
            'formation' => $formation
        ]);
    }
    
}
