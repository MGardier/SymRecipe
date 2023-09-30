<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{


    #[IsGranted('ROLE_USER')]
    #[Route('/utilisateur/profil/{id}', name: 'user.show', methods: ['GET', 'POST'])]
    public function show(User $user): Response
    {
        if ($user === $this->getUser())
            return $this->render('pages/user/show.html.twig', ['user' => $user]);
        else
            return $this->redirectToRoute('home.index');
    }

    /**
     * This controller allow user  to change his profile
     *
     * @param User $user
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/utilisateur/modification/{id}', name: 'user.update', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function update(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {

        if ($this->getUser() !== $user)
            return $this->redirectToRoute('home.index');
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())) {
                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Les informations de votre compte ont bien  été  modifiées."
                );
            } else {
                $this->addFlash(
                    'warning',
                    "Le mot de passe renseigné est incorrect."
                );
            }
        }

        return $this->render('pages/user/update.html.twig', [
            'form' => $form,
        ]);
    }


    /**
     * Allow to user to change his password
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route('/utilisateur/modification-mot-de-passe/{id}', name: 'user.update.password', methods: ['GET', 'POST'])]
    public function updatePassword(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        if ($this->getUser() !== $user)
            return $this->redirectToRoute('home.index');
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()['plainPassword'])) {
                $user->setPlainPassword($form->getData()['newPassword']);
                $user->setUpdatedAt(new \DateTimeImmutable());
                $manager->persist($user);
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Le mot de passe a  été  modifiées."
                );
            } else {
                $this->addFlash(
                    'warning',
                    "Le mot de passe renseigné est incorrect."
                );
            }
        }
        return $this->render('pages/user/update_password.html.twig', [
            'form' => $form,
        ]);
    }
}
