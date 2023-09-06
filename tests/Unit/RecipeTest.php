<?php

namespace App\Tests\Unit;

use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeTest extends KernelTestCase
{

    public function getEntity(): Recipe
    {
        return (new Recipe)
            ->setName('Recipe #1')
            ->setDescription('Description #1')
            ->setIsFavorite(true)
            ->setCreatedAt(new  \DateTimeImmutable())
            ->setUpdatedAt(new  \DateTimeImmutable());
    }
    public function testEntityIsValid(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $recipe = $this->getEntity();
        $errors = $container->get('validator')->validate($recipe);

        $this->assertCount(0, $errors);

        // $routerService = static::getContainer()->get('router');
        // $myCustomService = static::getContainer()->get(CustomService::class);
    }

    public function testInvalidName()
    {
        self::bootKernel();
        $container = static::getContainer();

        $recipe = $this->getEntity()
            ->setName('');
        $errors = $container->get('validator')->validate($recipe);
        $this->assertCount(2, $errors);
    }
}
