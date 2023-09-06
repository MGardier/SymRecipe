<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact.create')]
    public function create(Request $request, EntityManagerInterface $manager, MailService $mailService): Response
    {
        $contact = new Contact();
        if ($this->getUser()) {
            $user = $this->getUser();
            $contact->setFullName($user->getFullName());
            $contact->setEmail($user->getEmail());
        }
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            //$manager->persist($contact);
            //$manager->flush();

            //email

            $mailService->sendEmail(
                $contact->getEmail(),
                $contact->getSubject(),
                'pages/emails/contact.html.twig',
                ['contact' => $contact]
            );
            $this->addFlash(
                'success',
                "Votre message a été  transmis avec succès"
            );
            return $this->redirectToRoute('contact.create');
        }
        return $this->render('pages/contact/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
