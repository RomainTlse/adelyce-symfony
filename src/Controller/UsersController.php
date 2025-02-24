<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/users')]
final class UsersController extends AbstractController
{
    /** Get all users
     * @param UsersRepository $usersRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('', name: 'app_get_users', methods: ['GET'])]
    public function getUsers(
        UsersRepository     $usersRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $users = $usersRepository->findAll();

        $jsonContent = $serializer->serialize($users, 'json',
            ['groups' => [
                'infos_users',
                'relation_user_baskets',
                'infos_baskets',
                'relation_basket_basketArticles',
                'relation_basketarticle_articles',
                'infos_articles'
            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    /**
     * Get user by id
     * @param int $id
     * @param UsersRepository $userRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'app_get_user', methods: ['GET'])]
    public function getUserById(
        int                 $id,
        UsersRepository     $userRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse("Le user n'existe pas", Response::HTTP_NOT_FOUND, [], true);
        }

        $jsonContent = $serializer->serialize($user, 'json',
            ['groups' => [
                'infos_users',
                'relation_user_baskets',
                'infos_baskets',
                'relation_basket_basketArticles',
                'relation_basketarticle_articles',
                'infos_articles'
            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    /**
     * Create a new user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('', name: 'app_create_user', methods: ['POST'])]
    public function createUser(
        Request                $request,
        EntityManagerInterface $entityManager,
        SerializerInterface    $serializer
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['username']) || !isset($data['email'])) {
            return new JsonResponse(['error' => 'Missing data'], 400);
        }

        $user = new Users();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);

        $entityManager->persist($user);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($user, 'json',
            ['groups' => [
                'infos_users',
                'relation_user_baskets',
                'infos_baskets',
                'relation_basket_basketArticles',
                'relation_basketarticle_articles',
                'infos_articles'
            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_CREATED, [], true);
    }

    /**
     * Update user by id
     * @param int $id
     * @param Request $request
     * @param UsersRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'app_update_user', methods: ['PUT'])]
    public function updateUser(
        int                    $id,
        Request                $request,
        UsersRepository        $userRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface    $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse("Le user n'existe pas", Response::HTTP_NOT_FOUND, [], true);
        }

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['firstname'])) {
            $user->setFirstname($data['firstname']);
        }
        if (isset($data['lastname'])) {
            $user->setLastname($data['lastname']);
        }

        $entityManager->flush();

        $jsonContent = $serializer->serialize($user, 'json',
            ['groups' => [
                'infos_users',
                'relation_user_baskets',
                'infos_baskets',
                'relation_basket_basketArticles',
                'relation_basketarticle_articles',
                'infos_articles'
            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id, UsersRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse("Le user n'existe pas", Response::HTTP_NOT_FOUND, [], true);
        }

        try {
            $entityManager->remove($user);
            $entityManager->flush();
        } catch (ForeignKeyConstraintViolationException $e) {
            return new JsonResponse (null, Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse(null, 204);
    }
}
