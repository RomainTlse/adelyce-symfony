<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
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
    #[Route('', name: 'app_get_users', methods: ['GET'])]
    public function getUsers(
        UsersRepository     $usersRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $users = $usersRepository->findAll();

        $jsonContent = $serializer->serialize($users, 'json', ['groups' => 'infos_users']);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_get_user', methods: ['GET'])]
    public function getUserById(int $id, UsersRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse("Le user n'existe pas", Response::HTTP_NOT_FOUND, [], true);
        }

        $jsonContent = $serializer->serialize($user, 'json', ['groups' => 'infos_users']);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'app_create_user', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Missing data'], 400);
        }

        $user = new Users();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, 201); // Code HTTP 201 pour la création
    }

    #[Route('/{id}', name: 'app_update_user', methods: ['PUT'])]
    public function updateUser(int $id, Request $request, UsersRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
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

        return $this->json($user);
    }

    #[Route('/{id}', name: 'app_delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id, UsersRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse("Le user n'existe pas", Response::HTTP_NOT_FOUND, [], true);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(null, 204);
    }
}
