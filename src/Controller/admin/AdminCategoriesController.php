<?php

namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Description of AdminCategoriesController
 *
 * @author 
 */
class AdminCategoriesController extends AbstractController {
        /**
     *
     * @var formationRepository
     */
    private $formationRepository;
    
    private $categorieRepository;
    
    /**
     * Chemin d'accès de la liste des formations
     */
    private const ADMINCATEGORIES = "admin/admin.categories.html.twig";

    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    #[Route('/admin/categories', name: 'admin.categories')]
    public function index() : Response {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ADMINCATEGORIES, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
        /**
     * Vérifie si la Catégorie demandée est vide. Si oui, la supprime. Si non, renvoi une erreur.
     * @param int $id
     * @return Response
     */
    #[Route('/admin/categorie/suppr/{id}', name: 'admin.categorie.suppr')]
    public function suppr(int $id): Response {
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
     * Crée une nouvelle catégorie en fonction du nom indiqué par l'utilisateur
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/categorie/ajout', name: 'admin.categorie.ajout')]
    public function ajout(Request $request): Response {
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
