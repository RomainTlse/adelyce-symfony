<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/articles')]
final class ArticlesController extends AbstractController
{
    /** Get all articles
     * @param ArticlesRepository $articlesRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('', name: 'app_get_articles', methods: ['GET'])]
    public function getAllArticles(
        ArticlesRepository  $articlesRepository,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $articles = $articlesRepository->findAll();
        $jsonContent = $serializer->serialize($articles, 'json', ['groups' => 'infos_articles']);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    /**
     * Get article by id
     * @param int $id
     * @param ArticlesRepository $articlesRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'app_get_article', methods: ['GET'])]
    public function getArticleById(
        int                 $id,
        ArticlesRepository  $articlesRepository,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $article = $articlesRepository->find($id);

        if (!$article) {
            return new JsonResponse("L'article n'existe pas", Response::HTTP_NOT_FOUND, [], true);
        }

        $jsonContent = $serializer->serialize($article, 'json', ['groups' => 'infos_articles']);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    /**
     * Create a new article
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('', name: 'app_create_article', methods: ['POST'])]
    public function createArticle(
        Request                $request,
        EntityManagerInterface $entityManager,
        SerializerInterface    $serializer,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['quantity'])) {
            return new JsonResponse(['error' => 'Missing data'], 400);
        }

        $article = new Articles();
        $article->setName($data['name']);
        $article->setQuantity($data['quantity']);

        $entityManager->persist($article);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($article, 'json', ['groups' => 'infos_articles']);

        return new JsonResponse($jsonContent, Response::HTTP_CREATED, [], true);
    }

    /**
     * Update article by id
     * @param int $id
     * @param Request $request
     * @param ArticlesRepository $articlesRepository
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'app_update_article', methods: ['PUT'])]
    public function updateArticle(
        int                    $id,
        Request                $request,
        ArticlesRepository     $articlesRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface    $serializer,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $article = $articlesRepository->find($id);

        if (!$article) {
            return new JsonResponse("L'article n'existe pas", Response::HTTP_NOT_FOUND, [], true);
        }

        if (isset($data['name'])) {
            $article->setName($data['name']);
        }
        if (isset($data['quantity'])) {
            $article->setQuantity($data['quantity']);
        }

        $entityManager->flush();

        $jsonContent = $serializer->serialize($article, 'json', ['groups' => 'infos_articles']);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    /**
     * Delete article by id
     * @param int $id
     * @param ArticlesRepository $articlesRepository
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'app_delete_article', methods: ['DELETE'])]
    public function deleteArticle(int $id, ArticlesRepository $articlesRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $article = $articlesRepository->find($id);

        if (!$article) {
            return new JsonResponse("L'article n'existe pas", Response::HTTP_NOT_FOUND, [], true);
        }

        try {
            $entityManager->remove($article);
            $entityManager->flush();
        } catch (ForeignKeyConstraintViolationException $e) {
            return new JsonResponse (null, Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse (null, Response::HTTP_NO_CONTENT, []);
    }
}
