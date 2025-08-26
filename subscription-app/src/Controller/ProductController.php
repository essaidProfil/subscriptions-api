<?php
namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\ProductService;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Product read operations.
 */
class ProductController extends AbstractController
{
    /**
     * List all products with their options.
     */
    public function list(
        ProductRepository   $productRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $products = $productRepository->findAll();

        $context = SerializationContext::create()->setGroups(['details']);
        $json = $serializer->serialize($products, 'json', $context);

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * Add a products with its options
     */
    #[IsGranted('ROLE_ADMIN')]
    public function create(
        Request $request,
        ProductService $productService,
        SerializerInterface $serializer
    ): JsonResponse {
        $payload = $request->toArray();

        if (empty($payload['name'])) {
            return $this->json(['error' => 'Product name is required'], 400);
        }

        $options = $payload['options'] ?? [];

        $product = $productService->createProductWithOptions($payload['name'], $options);

        $context = SerializationContext::create()->setGroups(['details']);
        $json = $serializer->serialize($product, 'json', $context);

        return new JsonResponse($json, 201, [], true);
    }
}
