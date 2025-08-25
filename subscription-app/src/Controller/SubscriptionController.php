<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\PriceOptionRepository;
use App\Repository\SubscriptionRepository;
use App\Service\SubscriptionService;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Subscription endpoints (Get, create, cancel).
 */
class SubscriptionController extends AbstractController
{
    /**
     * Get current user's subscriptions.
     *
     * @param SubscriptionRepository    $subscriptionRepository
     * @param SerializerInterface       $serializer
     * @return JsonResponse
     */
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function getUserSubscription(
        SubscriptionRepository  $subscriptionRepository,
        SerializerInterface     $serializer
    ): JsonResponse
    {
        $subscriptions = $subscriptionRepository->findBy(['user' => $this->getUser()]);
        $json = $serializer->serialize($subscriptions, 'json', ['groups' => ['details']]);
        return new JsonResponse($json, 200, [], true);
    }

    /**
     * Create a subscription for the current user.
     *
     * @param Request               $request
     * @param PriceOptionRepository $priceOptionRepository
     * @param SubscriptionService   $subscriptionService
     * @return JsonResponse
     */
    #[IsGranted('ROLE_USER')]
    public function create(
        Request $request,
        PriceOptionRepository   $priceOptionRepository,
        SubscriptionService     $subscriptionService
    ): JsonResponse
    {
        $payload = $request->toArray();
        $priceOptionId = (int)($payload['priceOptionId'] ?? 0);
        $priceOption = $priceOptionRepository->find($priceOptionId);
        if (!$priceOption) {
            return $this->json(['error' => 'price option not found'], 404);
        }
        /** @var User $user */
        $user = $this->getUser();
        $subscription = $subscriptionService->subscribe($user, $priceOption);
        return $this->json(['id' => $subscription->getId()], 201);
    }

    /**
     * Cancel a subscription owned by the current user.
     *
     * @param int $id Subscription id.
     * @param SubscriptionRepository    $subscriptionRepository
     * @param SubscriptionService       $subscriptionService
     * @return JsonResponse
     */
    #[IsGranted('ROLE_USER')]
    public function cancel(
        int $id,
        SubscriptionRepository  $subscriptionRepository,
        SubscriptionService     $subscriptionService
    ): JsonResponse
    {
        $subscription = $subscriptionRepository->find($id);
        if (!$subscription) {
            return $this->json(['error' => 'subscription not found'], 404);
        }
        /** @var User $user */
        $user = $this->getUser();
        if ($subscription->getUser()->getId() !== $user->getId()) {
            return $this->json(['error' => 'forbidden'], 403);
        }
        $subscriptionService->cancel($subscription);
        return $this->json(['status' => 'cancelled']);
    }
}
