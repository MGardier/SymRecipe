<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use LDAP\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class IngredientController extends AbstractController
{
    /**
     * This controller display all ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/ingredient/index.html.twig', [
            'controller_name' => 'IngredientController',
            'ingredients' => $ingredients,
        ]);
    }


    /**
     * This controller show form which create new ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau', name: 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre ingrédient a été  créé avec succès"
            );
            return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller show form which update existent ingredient
     *
     * @param Ingredient $ingredient
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/edition/{id}', name: 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre ingrédient a été  modifié avec succès."
            );
            return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller delete existent ingredient
     *
     * @param Ingredient $ingredient
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/suppression/{id}', name: 'ingredient.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient): Response
    {

        $manager->remove($ingredient);
        $manager->flush();
        $this->addFlash(
            'success',
            "Votre ingrédient a été  supprimé avec succès."
        );
        return $this->redirectToRoute('ingredient.index');
    }
}
