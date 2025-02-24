<?php

namespace App\Controller;

use App\Entity\Baskets;
use App\Repository\BasketsRepository;
use App\Repository\UsersRepository;
use DateMalformedStringException;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/baskets')]
final class BasketsController extends AbstractController
{
    /** get all baskets
     * @param int $idUser
     * @param BasketsRepository $basketsRepository
     * @param UsersRepository $usersRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/{idUser}', name: 'app_get_baskets', methods: ['GET'])]
    public function getAllBaskets(
        int                 $idUser,
        BasketsRepository   $basketsRepository,
        UsersRepository     $usersRepository,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $user = $usersRepository->find($idUser);
        $baskets = $basketsRepository->findBy(['user' => $user]);
        $jsonContent = $serializer->serialize($baskets, 'json',
            ['groups' => [
                'infos_baskets',
                'relation_basket_basketArticles',
                'infos_basketarticles',
                'relation_basketarticle_articles',
                'infos_articles'
            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    /**
     * Get basket by id
     * @param int $id
     * @param int $idUser
     * @param BasketsRepository $basketsRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/{idUser}/{id}', name: 'app_get_basket', methods: ['GET'])]
    public function getBasketById(
        int                 $id,
        int                 $idUser,
        BasketsRepository   $basketsRepository,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $basket = $basketsRepository->find($id);

        if (!$basket) {
            return new JsonResponse("Le panier n'existe pas", Response::HTTP_NOT_FOUND, [], true);
        }

        $jsonContent = $serializer->serialize($basket, 'json',
            ['groups' => [
                'infos_baskets',
                'relation_basket_basketArticles',
                'relation_basketarticle_articles',
                'infos_articles'
            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    /**
     * Create a new basket
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UsersRepository $usersRepository
     * @return JsonResponse
     */
    #[Route('', name: 'app_create_basket', methods: ['POST'])]
    public function createBasket(
        Request                $request,
        EntityManagerInterface $entityManager,
        SerializerInterface    $serializer,
        UsersRepository        $usersRepository,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['basket_number']) || !isset($data['dt_created']) || !isset($data['user'])) {
            return new JsonResponse(['error' => 'Missing data'], Response::HTTP_BAD_REQUEST);
        }

        $user = $usersRepository->find($data['user']['id']);
        $basket = $serializer->deserialize($request->getContent(), Baskets::class, 'json');

        $basket->setUser($user);

        $entityManager->persist($basket);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($basket, 'json',
            ['groups' => [
                'infos_baskets',
                'relation_basket_basketArticles',
                'relation_basketarticle_articles',
                'infos_articles'
            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_CREATED, [], true);
    }

    /**
     * Update basket by id
     * @param int $id
     * @param Request $request
     * @param BasketsRepository $basketsRepository
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * @throws DateMalformedStringException
     */
    #[Route('/{id}', name: 'app_update_basket', methods: ['PUT'])]
    public function updateBasket(
        int                    $id,
        Request                $request,
        BasketsRepository      $basketsRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface    $serializer,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $basket = $basketsRepository->find($id);

        if (!$basket) {
            return new JsonResponse("Le panier n'existe pas", Response::HTTP_NOT_FOUND, [], true);
        }

        if (!isset($data['basket_number']) || !isset($data['dt_created']) || !isset($data['user'])) {
            return new JsonResponse(['error' => 'Missing data'], Response::HTTP_BAD_REQUEST);
        }

        $basket->setBasketNumber($data['basket_number']);
        $basket->setDtCreated(new DateTimeImmutable($data['dt_created']));

        $entityManager->flush();

        $jsonContent = $serializer->serialize($basket, 'json',
            ['groups' => [
                'infos_baskets',
                'relation_basket_basketArticles',
                'relation_basketarticle_articles',
                'infos_articles'
            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_delete_basket', methods: ['DELETE'])]
    public function deleteBasket(int $id, BasketsRepository $basketsRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $basket = $basketsRepository->find($id);

        if (!$basket) {
            return new JsonResponse("Le panier n'existe pas", Response::HTTP_NOT_FOUND, [], true);
        }

        try {
            $entityManager->remove($basket);
            $entityManager->flush();
        } catch (ForeignKeyConstraintViolationException $e) {
            return new JsonResponse (null, Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse (null, Response::HTTP_NO_CONTENT, []);
    }
}
