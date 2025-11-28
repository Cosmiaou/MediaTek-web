<?php
namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller pour la page d'accueil, ainsi que pour la page des CGU
 *
 * @author emds
 */
class AccueilController extends AbstractController
{
    
    /**
     * Contient l'instance du repository de Formation
     * @var FormationRepository
     */
    private $repository;
    
    /**
     * Constructeur. Initialise le repository des Formations
     * @param FormationRepository $repository
     */
    public function __construct(FormationRepository $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * Renvoie la page d'accueil, avec les deux Formations les plus récentes ('formations')
     * @return Response
     */
    #[Route('/', name: 'accueil')]
    public function index(): Response
    {
        $formations = $this->repository->findAllLasted(2);
        return $this->render("pages/accueil.html.twig", [
            'formations' => $formations
        ]);
    }
    
    /**
     * 
     * @return Response
     */
    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render("pages/cgu.html.twig");
    }
}
