<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends WebTestCase
{
    public function testIfLoginIsSuccessfull(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        //Generate url
        $crawler = $client->request('GET', $urlGenerator->generate('security.login'));

        //Get form and set data
        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "admin@symrecipe.fr",
            "_password" => "password",
        ]);

        //submit form
        $client->submit($form);

        //Check status HTTP is 302
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        //Check redirection
        $client->followRedirect();

        //Check redirection route
        $this->assertRouteSame('home.index');
    }

    public function testIfLoginFailedWhenPasswordIsWrong()
    {


        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");
        $crawler = $client->request('GET', $urlGenerator->generate('security.login'));

        //Get form and set data
        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "admin@symrecipe.fr",
            "_password" => "paassword",
        ]);

        $client->submit($form);

        //Check status HTTP is 302
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        //Check route still login
        $this->assertRouteSame('security.login');

        $this->assertSelectorTextContains("div.alert-danger", "Invalid credentials");
    }
}
