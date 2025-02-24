<?php

namespace App\Controller;

use App\Entity\BasketArticle;
use App\Repository\ArticlesRepository;
use App\Repository\BasketArticleRepository;
use App\Repository\BasketsRepository;
use App\Repository\UsersRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/basketarticle')]
final class BasketArticleController extends AbstractController
{
    /**
     * Get all relation basket/article
     * @param BasketArticleRepository $basketArticleRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('', name: 'app_get_all_basket_article')]
    public function GetAllBasketArticle(
        BasketArticleRepository $basketArticleRepository,
        SerializerInterface     $serializer,
    ): JsonResponse
    {
        $datas = $basketArticleRepository->findAll();

        $jsonContent = $serializer->serialize($datas, 'json',
            ['groups' => [
                'infos_basketarticles',
                'relation_basketarticle_baskets',
                'infos_baskets',
                'relation_basket_user',
                'infos_users',
                'relation_basketarticle_articles',
                'infos_articles',
            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    /**
     * Create a new association basket/article
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param BasketsRepository $basketsRepository
     * @param ArticlesRepository $articlesRepository
     * @return JsonResponse
     */
    #[Route('/add', name: 'app_create_basket-article', methods: ['POST'])]
    public function createBasketArticle(
        Request                $request,
        EntityManagerInterface $entityManager,
        SerializerInterface    $serializer,
        BasketsRepository      $basketsRepository,
        ArticlesRepository     $articlesRepository,
        UsersRepository        $usersRepository
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $basket = $basketsRepository->find($data['basket']['id']);
        $article = $articlesRepository->find($data['article']['id']);


        $basketArticle = new BasketArticle();
        $basketArticle->setArticle($article);
        $basketArticle->setBasket($basket);
        $basketArticle->setQuantity($data['quantity']);
        if (!empty($data['associated_user'])) {
            $associatedUser = $usersRepository->find($data['associated_user']['id']);
            $basketArticle->setAssociatedUser($associatedUser);
        }


        $entityManager->persist($basketArticle);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($basketArticle, 'json',
            ['groups' => [
                'infos_basketarticles',
                'relation_basketarticle_baskets',
                'infos_baskets',
                'relation_basketarticle_articles',
                'infos_articles',
            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_CREATED, [], true);
    }

    /**
     * Get association basket/article by ids
     * @param int $idBasket
     * @param int $idArticle
     * @param BasketArticleRepository $basketArticleRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/{idBasket}/{idArticle}', name: 'app_get_basket_article', methods: ['GET'])]
    public function getBasketArticleBuIds(
        int                     $idBasket,
        int                     $idArticle,
        BasketArticleRepository $basketArticleRepository,
        SerializerInterface     $serializer,
        BasketsRepository       $basketsRepository,
        ArticlesRepository      $articlesRepository,
    ): JsonResponse
    {
        $basket = $basketsRepository->find($idBasket);
        $article = $articlesRepository->find($idArticle);
        $basketArticle = $basketArticleRepository->findOneBy(['basket' => $basket, 'article' => $article]);

        if (!$basketArticle) {
            return new JsonResponse("L'association n'existe pas", Response::HTTP_NOT_FOUND, [], true);
        }

        $jsonContent = $serializer->serialize($basketArticle, 'json',
            ['groups' => [
                'infos_basketarticles',
                'relation_basketarticle_baskets',
                'infos_baskets',
                'relation_basketarticle_articles',
                'infos_articles',
            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    /**
     * Update the association basket/article by ids
     * @param int $idBasket
     * @param int $idArticle
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param BasketArticleRepository $basketArticleRepository
     * @param BasketsRepository $basketsRepository
     * @param ArticlesRepository $articlesRepository
     * @return JsonResponse
     */
    #[Route('/{idBasket}/{idArticle}', name: 'app_update_basket_article', methods: ['PUT'])]
    public function updateBasketArticle(
        int                     $idBasket,
        int                     $idArticle,
        Request                 $request,
        SerializerInterface     $serializer,
        EntityManagerInterface  $entityManager,
        BasketArticleRepository $basketArticleRepository,
        BasketsRepository       $basketsRepository,
        ArticlesRepository      $articlesRepository,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $basket = $basketsRepository->find($data['basket']['id']);
        $article = $articlesRepository->find($data['article']['id']);
        $basketArticle = $basketArticleRepository->findOneBy(['basket' => $basket, 'article' => $article]);

        if (!$basketArticle) {
            return new JsonResponse("L'association n'existe pas", Response::HTTP_NOT_FOUND, [], true);
        }

        if (isset($data['quantity'])) {
            $basketArticle->setQuantity($data['quantity']);
        }

        $entityManager->flush();

        $jsonContent = $serializer->serialize($basketArticle, 'json',
            ['groups' => [
                'infos_basketarticles',
                'relation_basketarticle_baskets',
                'infos_baskets',
                'relation_basketarticle_articles',
                'infos_articles',
            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    /** Delete the association basket/article by ids
     * @param int $idBasket
     * @param int $idArticle
     * @param BasketArticleRepository $basketArticleRepository
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/{idBasket}/{idArticle}', name: 'app_delete_basket_article', methods: ['DELETE'])]
    public function deleteBasketArticle(
        int                     $idBasket,
        int                     $idArticle,
        BasketArticleRepository $basketArticleRepository,
        EntityManagerInterface  $entityManager,
        BasketsRepository       $basketsRepository,
        ArticlesRepository      $articlesRepository,
    ): JsonResponse
    {
        $basket = $basketsRepository->find($idBasket);
        $article = $articlesRepository->find($idArticle);
        $basketArticle = $basketArticleRepository->findOneBy(['basket' => $basket, 'article' => $article]);

        if (!$basketArticle) {
            return new JsonResponse("L'association n'existe pas", Response::HTTP_NOT_FOUND, [], true);
        }

        try {
            $entityManager->remove($basketArticle);
            $entityManager->flush();
        } catch (ForeignKeyConstraintViolationException $e) {
            return new JsonResponse (null, Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse (null, Response::HTTP_NO_CONTENT, []);
    }

}
