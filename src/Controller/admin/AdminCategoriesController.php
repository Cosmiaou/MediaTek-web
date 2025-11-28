<?php

namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller pour la page admin/categories
 *
 */
class AdminCategoriesController extends AbstractController
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
     * Chemin d'accès de la liste des Categories
     */
    private const ADMINCATEGORIES = "admin/admin.categories.html.twig";

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
     * Retourne /admin/categories, contenant la liste des formations ('formations')
     * ainsi que la liste des catégories ('categories')
     * @return Response
     */
    #[Route('/admin/categories', name: 'admin.categories')]
    public function index() : Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ADMINCATEGORIES, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
     /**
     * Vérifie si la Catégorie demandée est vide. Si oui, la supprime. Si non, renvoi une erreur.
     * Dans tous les cas, redirige vers admin/categories
     * @param int $id
     * @return Response
     */
    #[Route('/admin/categorie/suppr/{id}', name: 'admin.categorie.suppr')]
    public function suppr(int $id): Response
    {
        $formations = $this->formationRepository->findAllForOneCategory($id);
        
        if (count($formations) > 0) {
            $this->addFlash('error', 'Erreur ! On ne peut pas supprimer une catégorie si elle est rattachée à une ou plusieurs formations.');
        } else {
            $id = $this->categorieRepository->find($id);
            $this->categorieRepository->remove($id);
        }
        return $this->redirectToRoute('admin.categories');
    }
    
    /**
     * Crée une nouvelle catégorie en fonction du nom indiqué par l'utilisateur.
     * Dans tous les cas, renvoie vers admin/categories
     * Si aucun nom n'est indiqué, renvoie un message Flash et refuse la création.
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/categorie/ajout', name: 'admin.categorie.ajout')]
    public function ajout(Request $request): Response
    {
        $nomCategorie = $request->get("nom");
        
        if ($nomCategorie != null && strlen($nomCategorie) <= 50) {
            $categorie = new Categorie();
            $categorie->setName($nomCategorie);
            $this->categorieRepository->add($categorie);
        } else {
            $this->addFlash('error', 'Erreur ! Un nom de moins de 50 caractères doit être indiqué pour la catégorie.');
        }
        
        return $this->redirectToRoute('admin.categories');
    }
}
