<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use LDAP\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\ItemInterface;

class RecipeController extends AbstractController
{
    /**
     * This controller display all recipes of the  current user
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {

        $recipes = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }


    /**
     * This controller display al recipes shares with community
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recette/communaute', name: 'recipe.community', methods: ['GET'])]
    public function indexCommunity(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        //Add cache system to get public recipes 
        $cache = new FilesystemAdapter();
        //who expired after 15 sec  with key controller_object (indexCommunity_recipes)
        $data = $cache->get('indexCommunity_recipes', function (ItemInterface $item) use ($repository) {
            $item->expiresAfter(15);
            //get Public recipes without specify how many
            return $repository->findPublicRecipe();
        });

        $recipes = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/community.html.twig', [
            'recipes' => $recipes,
        ]);
    }


    /**
     * This controller display a recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/recette/voir/{id}', name: 'recipe.show', methods: ['GET', 'POST'])]
    public function show(Recipe $recipe, Request $request, MarkRepository $markRepository, EntityManagerInterface $manager): Response
    {
        if ($recipe->getIsPublic()  || $recipe->getUser() === $this->getUser()) {
            $mark = new Mark();
            $form = $this->createForm(MarkType::class, $mark);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $mark->setUser($this->getUser())
                    ->setRecipe($recipe);

                $existingMark = $markRepository->findOneBy([
                    'user' => $this->getUser(),
                    'recipe' => $recipe,
                ]);

                if (!$existingMark)
                    $manager->persist($mark);
                else
                    $existingMark->setMark($form->getData()->getMark());

                $manager->flush();
                $this->addFlash(
                    'success',
                    "Votre note a bien été  attribué avec succès"
                );
                return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
            }


            return $this->render('pages/recipe/show.html.twig', [
                'recipe'   => $recipe,
                'form'     => $form->createView()
            ]);
        } else
            return $this->redirectToRoute('recipe.index');
    }


    /**
     * This controller display form which create new recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/recette/creation', name: 'recipe.create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe, ['route' => 'create']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $recipe->setUser($this->getUser());
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre recette a été  créé avec succès"
            );
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller display form which update existent recipe of user
     *
     * @param Recipe $recipe
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/modification/{id}', name: 'recipe.update', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function update(Recipe $recipe, Request $request, EntityManagerInterface $manager): Response
    {
        if ($recipe->getUser() != $this->getUser())
            return $this->redirectToRoute('recipe.index');
        $form = $this->createForm(RecipeType::class, $recipe, ['route' => 'update']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre recette a été  modifié avec succès."
            );
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller remove existent recipe of user
     *
     * @param Recipe $recipe
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/suppression/{id}', name: 'recipe.delete', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function delete(EntityManagerInterface $manager, Recipe $recipe): Response
    {
        if ($recipe->getUser() != $this->getUser())
            return $this->redirectToRoute('recipe.index');
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash(
            'success',
            "Votre recette a été  supprimé avec succès."
        );
        return $this->redirectToRoute('recipe.index');
    }
}
