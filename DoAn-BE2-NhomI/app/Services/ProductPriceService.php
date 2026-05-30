<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;

class ProductPriceService
{
    /**
     * @param  ProductVariant|object|array<string, mixed>|null  $variant
     */
    public static function effectiveVariantPrice(mixed $variant, float $basePrice): float
    {
        if ($variant === null) {
            return $basePrice;
        }

        $salePrice = data_get($variant, 'sale_price');
        $price = data_get($variant, 'price');

        if ($salePrice !== null && (float) $salePrice > 0) {
            return (float) $salePrice;
        }

        if ($price !== null && (float) $price > 0) {
            return (float) $price;
        }

        return $basePrice;
    }

    public function displayPriceForProduct(Product $product): float
    {
        $basePrice = (float) $product->base_price;
        $variants = $product->variants->where('is_active', 1);

        if ($variants->isEmpty()) {
            return $basePrice;
        }

        return (float) $variants
            ->map(fn ($v) => self::effectiveVariantPrice($v, $basePrice))
            ->min();
    }

    public function bumpProduct(Product $product): void
    {
        $product->forceFill(['prices_updated_at' => now()])->save();
    }

    public function versionFor(Product $product): int
    {
        if ($product->prices_updated_at) {
            return $product->prices_updated_at->getTimestamp();
        }

        $variantFingerprint = $product->variants
            ->map(fn ($v) => implode(':', [
                data_get($v, 'variant_id'),
                data_get($v, 'price'),
                data_get($v, 'sale_price') ?? '',
            ]))
            ->sort()
            ->implode('|');

        return (int) crc32($product->product_id . ':' . $product->base_price . ':' . $variantFingerprint);
    }

    /**
     * @param  int[]  $productIds
     */
    public function buildSyncPayload(array $productIds): array
    {
        if (empty($productIds)) {
            return ['products' => [], 'synced_at' => now()->toIso8601String()];
        }

        $products = Product::query()
            ->with('variants')
            ->whereIn('product_id', $productIds)
            ->where('is_active', 1)
            ->get();

        $payload = [];

        foreach ($products as $product) {
            $variants = [];
            foreach ($product->variants as $variant) {
                $variants[(string) $variant->variant_id] = [
                    'price' => (float) $variant->price,
                    'sale_price' => $variant->sale_price !== null ? (float) $variant->sale_price : null,
                    'display_price' => self::effectiveVariantPrice($variant, (float) $product->base_price),
                ];
            }

            $payload[(string) $product->product_id] = [
                'version' => $this->versionFor($product),
                'base_price' => (float) $product->base_price,
                'display_price' => $this->displayPriceForProduct($product),
                'variants' => $variants,
            ];
        }

        return [
            'products' => $payload,
            'synced_at' => now()->toIso8601String(),
        ];
    }
}
