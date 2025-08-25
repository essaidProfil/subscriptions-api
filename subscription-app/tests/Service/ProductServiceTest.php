<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\PriceOption;
use App\Entity\Product;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for ProductService.
 *
 * @internal
 */
#[CoversClass(ProductService::class)]
#[Group('unit')]
final class ProductServiceTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    private ProductService $productService;

    protected function setUp(): void
    {
        /** @var EntityManagerInterface&MockObject $entityManager */
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->productService = new ProductService($this->entityManager);
    }

    /**
     * Ensure product and all price options are persisted.
     *
     * @return void
     */
    #[Test]
    public function test_create_product_with_options_persists_all(): void
    {
        /** @var array<int, array{code:string, amountCents:int, currency:string}> $options */
        $options = [
            ['code' => 'monthly', 'amountCents' => 9900,  'currency' => 'EUR'],
            ['code' => 'yearly',  'amountCents' => 99000, 'currency' => 'EUR'],
        ];

        // 1 persist for product + 2 for options = 3
        $this->entityManager->expects($this->exactly(3))
            ->method('persist')
            ->with($this->logicalOr(
                $this->isInstanceOf(Product::class),
                $this->isInstanceOf(PriceOption::class)
            ));
        $this->entityManager->expects($this->once())->method('flush');

        $product = $this->productService->createProductWithOptions('Pro Plan', $options);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame('Pro Plan', $product->getName());
    }
}
