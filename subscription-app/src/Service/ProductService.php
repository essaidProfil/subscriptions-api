<?php
namespace App\Service;

use App\Entity\Product;
use App\Entity\PriceOption;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Product and pricing management.
 */
class ProductService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    /**
     * Create a product and attach price options.
     *
     * @param string $name Product display name.
     * @param array<int, array{code:string, amountCents:int, currency:string}> $options Array of options (code, amountCents, currency).
     * @return Product The persisted product.
     */
    public function createProductWithOptions(string $name, array $options): Product
    {
        $product = new Product();
        $product->setName($name);
        $this->entityManager->persist($product);

        foreach ($options as $option) {
            $priceOption = new PriceOption();
            $priceOption->setProduct($product)
                        ->setCode($option['code'])
                        ->setAmountCents($option['amountCents'])
                        ->setCurrency($option['currency']);
            $this->entityManager->persist($priceOption);
        }

        $this->entityManager->flush();
        return $product;
    }
}
