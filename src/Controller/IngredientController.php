<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;



class IngredientController extends AbstractController
{
    /**
     * This controller display all ingredients of the  current user
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
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
    #[Route('/ingredient/creation', name: 'ingredient.create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient, ['route' => 'create']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $ingredient->setUser($this->getUser());
            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre ingrédient a été  créé avec succès"
            );
            return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/create.html.twig', [
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
    #[Route('/ingredient/modification/{id}', name: 'ingredient.update', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function update(Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {
        if ($ingredient->getUser() != $this->getUser())
            return $this->redirectToRoute('ingredient.index');
        $form = $this->createForm(IngredientType::class, $ingredient, ['route' => 'update']);
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
        return $this->render('pages/ingredient/update.html.twig', [
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
    #[IsGranted('ROLE_USER')]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient): Response
    {
        if ($ingredient->getUser() != $this->getUser())
            return $this->redirectToRoute('ingredient.index');
        $manager->remove($ingredient);
        $manager->flush();
        $this->addFlash(
            'success',
            "Votre ingrédient a été  supprimé avec succès."
        );
        return $this->redirectToRoute('ingredient.index');
    }
}
