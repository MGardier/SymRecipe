<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

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
        $ingredients = [];
        // Ingredient
        for ($i = 0; $i < 50; $i++) {


            $ingredient = new Ingredient();
            $ingredient
                ->setName($this->faker->word())
                ->setPrice(mt_rand(0, 100));
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }
        //Recipe
        for ($j = 0; $j < 25; $j++) {

            $recipe = new Recipe();
            $recipe
                ->setName($this->faker->word())
                ->setIsFavorite(mt_rand(0, 1))
                ->setUpdatedAt(new  \DateTimeImmutable())
                ->setDescription($this->faker->paragraph());

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


        //User
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

            $manager->persist($user);
        }
        $manager->flush();
    }
}
