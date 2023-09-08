<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Entity\Ingredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class IngredientTest extends WebTestCase
{
    public function testIfCreateIngredientIsSuccessfull(): void
    {
        $client = static::createClient();

        //get urlGenerator
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        //Recup entity manager
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');


        //get Admin User
        $user = $entityManager->find(User::class, 1);

        //login user
        $client->loginUser($user);

        //go to ingredient create page
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.create'));

        //get Form and set data
        //Name is unique so we use str_shuffle to get random name
        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => "" . str_shuffle("ABCDEFGabcdefg"),
            'ingredient[price]' => floatval(mt_rand(1, 150))
        ]);

        //Submit form 
        $client->submit($form);

        //Check http response
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        //Check if redirection is good
        $this->assertRouteSame('ingredient.index');

        //Check alert success message
        $this->assertSelectorTextContains('div.alert.alert-success.mt-4', 'Votre ingrédient a été créé avec succès !');
    }


    public function testIfListIngredientIsSuccessfull(): void
    {
        $client = static::createClient();

        //Recup entity manager
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        //get admin User
        /** @var User $user */
        $user = $entityManager->find(User::class, 1);

        //login user
        $client->loginUser($user);

        //Go to route
        $crawler = $client->request('GET', '/ingredient');

        //Check if route is good
        $this->assertRouteSame('ingredient.index');

        //Check if title is here
        $this->assertSelectorTextContains('h1', 'Mes ingrédients');

        //Check if table exist
        $table = $crawler->filter('table.table.table-hover');
        $this->assertEquals(1, count($table));

        //Check if headers are correct
        $this->assertSelectorTextContains('th.th-id', 'Numéro');
        $this->assertSelectorTextContains('th.th-name', 'Nom');
        $this->assertSelectorTextContains('th.th-price', 'Prix');
        $this->assertSelectorTextContains('th.th-date', 'Date');
        $this->assertSelectorTextContains('th.th-update', 'Modifier');
        $this->assertSelectorTextContains('th.th-delete', 'Supprimer');


        //Check if link add ingredient is here
        $link = $crawler->selectLink("Ajouter un nouvel ingrédient");
        $this->assertEquals(1, count($link));

        //click on link
        $crawler = $client->click($link->link());

        //Check if redirection is good
        $this->assertRouteSame('ingredient.create');
    }

    public function testIfUpdateIngredientIsSuccessfull(): void
    {

        $client = static::createClient();

        //get urlGenerator
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        //Recup entity manager
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');


        //get Admin User
        $user = $entityManager->find(User::class, 1);

        //login user
        $client->loginUser($user);

        //get one  Admin ingredient
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);
        //Go to update ingredient route
        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('ingredient.update', ['id' => $ingredient->getId()])
        );

        $this->assertResponseIsSuccessful();

        //get Form and set data
        //Name is unique so we use str_shuffle to get random name
        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => "" . str_shuffle("ABCDEFGabcdefg"),
            'ingredient[price]' => floatval(mt_rand(1, 150))
        ]);

        //Submit form
        $client->submit($form);

        //Check http response
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        //Follow redirection
        $client->followRedirect();

        //Check if redirection is good
        $this->assertRouteSame('ingredient.index');

        //Check alert success message
        $this->assertSelectorTextContains('div.alert.alert-success.mt-4', 'Votre ingrédient a été modifié avec succès !');
    }

    public function testIfDeleteIngredientIsSuccessfull(): void
    {

        $client = static::createClient();

        //get urlGenerator
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        //Get entity manager
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');


        //get Admin User
        $user = $entityManager->find(User::class, 1);

        //login user
        $client->loginUser($user);

        //get one  Admin ingredient
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        //Go to update ingredient route
        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('ingredient.delete', ['id' => $ingredient->getId()])
        );

        //Check http response
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        //Follow redirection
        $client->followRedirect();

        //Check if redirection is good
        $this->assertRouteSame('ingredient.index');

        //Check alert success message
        $this->assertSelectorTextContains('div.alert.alert-success.mt-4', 'Votre ingrédient a été supprimé avec succès !');
    }
}
