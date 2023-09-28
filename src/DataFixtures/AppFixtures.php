<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\Ingredient;
use App\Entity\Mark;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/**
 * Allow to store fake data in database
 */
class AppFixtures extends Fixture
{
    /**
     *
     * @var Generator
     */
    private Generator $faker;


    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {

        //array of Users
        $users = [];
        //array of Ingredients
        $ingredients = [];

        $admin = new User();
        $admin->setFullName('Administrateur')
            ->setEmail('admin@symrecipe.fr')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setPlainPassword('password');
        $ingredientAdmin = new Ingredient();
        $ingredientAdmin->setName($this->faker->word())
            ->setPrice(mt_rand(0, 100))
            ->setUser($admin);


        $users[] = $admin;
        $ingredients[] = $ingredientAdmin;
        $manager->persist($admin);
        $manager->persist($ingredientAdmin);

        for ($k = 0; $k < 50; $k++) {

            $user = new User();
            $user
                ->setFullName($this->faker->name())
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_User'])
                ->setPlainPassword('password');
            if ($k % 2 == 0) {
                $user
                    ->setPseudo($this->faker->firstName());
            }
            $users[] = $user;
            $manager->persist($user);
        }



        $recipes = [];
        // Ingredient
        for ($i = 0; $i < 50; $i++) {


            $ingredient = new Ingredient();
            $ingredient
                ->setName($this->faker->word())
                ->setPrice(mt_rand(0, 100))
                ->setUser($users[mt_rand(0, count($users) - 1)]);
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }
        //Recipe
        for ($j = 0; $j < 25; $j++) {

            $recipe = new Recipe();
            $recipes[] = $recipe;
            $recipe
                ->setName($this->faker->word())
                ->setIsFavorite(mt_rand(0, 1))
                ->setIsPublic(mt_rand(0, 1))
                ->setUpdatedAt(new  \DateTimeImmutable())
                ->setDescription($this->faker->paragraph())
                ->setUser($users[mt_rand(0, count($users) - 1)]);

            if ($j % 2 == 0) {
                $recipe->setTime(mt_rand(1, 1440))
                    ->setNumberPeople(mt_rand(1, 50))
                    ->setDifficulty(mt_rand(1, 5))
                    ->setPrice(mt_rand(0, 1000));
            }

            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }
            $manager->persist($recipe);
        }

        //Mark
        foreach ($recipes as $recipe) {
            for ($i = 0; $i < mt_rand(0, 4); $i++) {
                $mark = new Mark();
                $mark->setMark(mt_rand(1, 5))
                    ->setUser($users[mt_rand(0, count($users) - 1)])
                    ->setRecipe($recipe);
                $manager->persist($mark);
                $recipe->setIsPublic(1);
            }
        }

        //Contact

        for ($i = 0; $i < 5; $i++) {
            $contact = new Contact();
            $contact->setFullName($this->faker->name())
                ->setEmail($this->faker->email())
                ->setSubject('Demande n°' . ($i + 1))
                ->setMessage($this->faker->text());
            $manager->persist($contact);
        }
        $manager->flush();
    }
}
