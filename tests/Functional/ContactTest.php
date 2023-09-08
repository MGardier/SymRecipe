<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Formulaire de contact');

        $submitButton = $crawler->selectButton('Envoyer');

        //Get form
        $form = $submitButton->form();

        //Set form data
        $form["contact[fullName]"] = "Jean Dupont";
        $form["contact[email]"] = "jd@symrecipe.com";
        $form["contact[subject]"] = "Test";
        $form["contact[message]"] = "Test";

        $client->submit($form);

        //Check status HTTP is 302
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        //Check email 
        $this->assertEmailCount(1);

        $client->followRedirect();

        //Check success message
        $this->assertSelectorTextContains(
            'div.alert.alert-success.mt-4',
            'Votre demande a été envoyé avec succès !'
        );
    }

    public function testCrudIsHere(): void
    {

        $client = static::createClient();

        //Get entity manager
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        //get Admin User
        $user = $entityManager->find(User::class, 1);

        //login user
        $client->loginUser($user);

        //Check if admin have access to admin
        $crawler = $client->request('GET', '/admin');

        //Check if admin route is good
        $this->assertRouteSame('admin');

        //Go to contact requests in admin section
        $crawler = $client->clickLink('Demandes de contact');

        //Check if link for create new contact request work
        $crawler = $client->click($crawler->filter('.action-new')->link());
        $this->assertResponseIsSuccessful();

        //Go back to contact requests in admin section
        $client->request('GET', '/admin');
        $crawler = $client->clickLink('Demandes de contact');

        //Check if link for  upate contact request work
        $crawler = $client->click($crawler->filter('.action-edit')->link());
        $this->assertResponseIsSuccessful();

        //Go back to contact requests in admin section
        $client->request('GET', '/admin');
        $crawler = $client->clickLink('Demandes de contact');

        //Check if link for  delete contact request work 
        //A modifier pour valider la popup 
        // $crawler = $client->click($crawler->filter('.action-delete')->link());
        //$this->assertResponseIsSuccessful();
    }
}
