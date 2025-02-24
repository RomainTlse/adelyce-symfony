<?php

namespace App\Controller;

use App\Entity\Notifications;
use App\Repository\NotificationsRepository;
use DateMalformedStringException;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/notifications')]
final class NotificationsController extends AbstractController
{
    /**
     * Get all notifications
     * @param NotificationsRepository $notificationsRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('', name: 'app_get_notifications')]
    public function getNotifications(
        NotificationsRepository $notificationsRepository,
        SerializerInterface     $serializer
    ): JsonResponse
    {
        $notifications = $notificationsRepository->findAll();

        $jsonContent = $serializer->serialize($notifications, 'json',
            ['groups' => [
                'infos_notificationss',
            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);

    }

    /**
     * Add new notification
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * @throws DateMalformedStringException
     */
    #[Route('', name: 'app_create_notification', methods: ['POST'])]
    public function createNotification(
        Request                $request,
        EntityManagerInterface $entityManager,
        SerializerInterface    $serializer
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);


        $notification = new Notifications();
        $notification->setTitle($data['title']);
        $notification->setDescription($data['description']);
        $notification->setDtCreated(new DateTime($data['dtCreated']));
        $notification->setIsOpen(false);

        $entityManager->persist($notification);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($notification, 'json',
            ['groups' => [
                'infos_notifications',
            ]]);

        return new JsonResponse($jsonContent, Response::HTTP_CREATED, [], true);
    }
}
