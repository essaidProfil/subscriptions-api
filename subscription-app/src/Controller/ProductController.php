<?php
namespace App\Controller;

use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

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
}
