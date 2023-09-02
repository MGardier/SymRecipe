<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact.create')]
    public function create(Request $request, EntityManagerInterface $manager, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        if ($this->getUser()) {
            $user = $this->getUser();
            $contact->setFullName($user->getFullName());
            $contact->setEmail($user->getEmail());
        }
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        dump($contact);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            //$manager->persist($contact);
            //$manager->flush();

            //email
            $email = (new TemplatedEmail())
                ->from(new Address($contact->getEmail(), 'Mailtrap'))
                ->to('adminSymrecipe@gmail.com')
                ->subject($contact->getSubject())
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'contact' => $contact
                ]);
            $mailer->send($email);
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
