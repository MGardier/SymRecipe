<?php

namespace App\Tests\Functional;

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
}
