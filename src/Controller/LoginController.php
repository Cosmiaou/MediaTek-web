<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller pour la page de connexion, ainsi que la deconnexion
 */
class LoginController extends AbstractController
{
    
    /**
     * Fonction de connexion pour le back-office. Si la connexion fonctionne, renvoi à /admin
     * Si non, recharge la page de connexion en ajoutant une erreur, ainsi que le dernier username testé
     * @param AuthenticationUtils $authentificationUtils
     * @return Response
     */
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authentificationUtils): Response
    {
        $error = $authentificationUtils->getLastAuthenticationError();
        $lastUsername = $authentificationUtils->getLastUsername();
        
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
    
    /**
     * Fonction de deconnexion. Laissée vide car gêrée automatiquement par Symfony. Sert uniquement à gêrer la route.
     */
    #[Route('/logout', name: 'logout')]
    public function logout()
    {
    }
}
