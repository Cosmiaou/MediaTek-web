<?php

namespace App\Controller\admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller pour la page de gestion des Formations ('admin/'), ainsi que pour les pages d'ajout ou de
 * modification de formation
 */
class AdminFormationsController extends AbstractController
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
    private const ADMINFORMATION = "admin/admin.formations.html.twig";

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
     * Retourne /admin, contenant la liste des formations ('formations')
     * ainsi que la liste des catégories ('categories')
     * @return Response
     */
    #[Route('/admin', name: 'admin.formations')]
    public function index() : Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ADMINFORMATION, [
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
    #[Route('/admin/formations/tri/{champ}/{ordre}/{table}', name: 'admin.formations.sort')]
    public function sort($champ, $ordre, $table=""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ADMINFORMATION, [
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
    #[Route('admin/formations/recherche/{champ}/{table}', name: 'admin.formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ADMINFORMATION, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * Supprime l'élément indiqué en fonction de son id, puis recharge la page
     * @param int $id
     * @return Response
     */
    #[Route('/admin/formation/suppr/{id}', name: 'admin.formation.suppr')]
    public function suppr(int $id): Response {
        $formation = $this->formationRepository->find($id);
        $this->formationRepository->remove($formation);
        return $this->redirectToRoute('admin.formations');
    }
    
    /**
     * Redirige l'utilisateur vers un formulaire de modification de la Formation dont l'id est passé en paramètre.
     * @param int $id
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/formation/edit/{id}', name: 'admin.formation.edit')]
    public function edit(int $id, Request $request): Response {
        $formation = $this->formationRepository->find($id);
        $formFormation = $this->createForm(FormationType::class, $formation);
        
        $formFormation->handleRequest($request);
        if($formFormation->isSubmitted() && $formFormation->isValid()) {
            $this->formationRepository->add($formation);
            return $this->redirectToRoute('admin.formations');
        }
        
        return $this->render("admin/admin.formation.edit.html.twig", [
            'formation' => $formation,
            'formformation' => $formFormation->createView()
        ]);
    }
    
    /**
     * Redirige l'utilisateur vers un formulaire de création d'une nouvelle formation
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/formation/ajout', name: 'admin.formation.ajout')]
    public function ajout(Request $request): Response {
        $formation = new Formation();
        $formFormation = $this->createForm(FormationType::class, $formation);
        
        $formFormation->handleRequest($request);
        if($formFormation->isSubmitted() && $formFormation->isValid()) {
            $this->formationRepository->add($formation);
            return $this->redirectToRoute('admin.formations');
        }
        
        return $this->render("admin/admin.formation.ajout.html.twig", [
            'formation' => $formation,
            'formformation' => $formFormation->createView()
        ]);
    }
}
