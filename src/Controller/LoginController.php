<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request, SerializerInterface $serializer, UsersRepository $usersRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), Users::class, 'json');

        $result = $usersRepository->findBy(['email' => $user->getEmail(), 'password' => $user->getPassword()]);

        if ($result) {
            $resultjson = $serializer->serialize($result, 'json',
                ['groups' => [
                    'infos_users',
                ]]);
            return new JsonResponse ($resultjson, Response::HTTP_OK, ["Content-Type" => "application/json"], true);
        }
        return new JsonResponse ("L'utilisateur n'existe pas, ou le mot de passe n'est pas correct", Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): JsonResponse
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
        $user->setPassword($data['password']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, 201); // Code HTTP 201 pour la création
    }
}
