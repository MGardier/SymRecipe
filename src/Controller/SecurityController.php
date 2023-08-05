<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'security.login', methods: ['POST', 'GET'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {


        return $this->render('pages/security/login.html.twig', [
            'controller_name' => 'SecurityController',
            'lastUsername' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/deconnexion', name: 'security.logout')]
    public function logout()
    {
        //nothing to do here
    }
}
