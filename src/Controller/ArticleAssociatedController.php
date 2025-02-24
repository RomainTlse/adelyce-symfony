<?php

namespace App\Controller;

use App\Repository\BasketArticleRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class ArticleAssociatedController extends AbstractController
{
    #[Route('/api/associated/{idUser}', name: 'app_get_article_associated', methods: ['GET'])]
    public function getArticleAssociatedIdUser(
        int                     $idUser,
        BasketArticleRepository $basketArticleRepository,
        UsersRepository         $usersRepository,
        SerializerInterface     $serializer,
    ): JsonResponse
    {
        $user = $usersRepository->find($idUser);
        $basketArticle = $basketArticleRepository->findBy(['associated_user' => $user]);

        $jsonContent = $serializer->serialize($basketArticle, 'json',
            ['groups' => [
                'infos_basketarticles',
                'relation_basketarticle_baskets',
                'infos_baskets',
                'relation_basketarticle_articles',
                'infos_articles',
                'relation_basket_user',
                'infos_users'

            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }
}
