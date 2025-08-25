<?php
namespace App\Controller;

use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Product read operations.
 */
class ProductController extends AbstractController
{
    /**
     * List all products with their options.
     *
     * @param ProductRepository     $productRepository
     * @param SerializerInterface   $serializer
     * @return JsonResponse
     */
    public function list(
        ProductRepository   $productRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $products = $productRepository->findAll();
        $json = $serializer->serialize($products, 'json', ['groups' => ['details']]);
        return new JsonResponse($json, 200, [], true);
    }
}
