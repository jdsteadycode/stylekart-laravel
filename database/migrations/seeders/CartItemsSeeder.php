<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;

class CartItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // log the action
        logger()->info(
            "[database\seeders\CartItemsSeeder@run] Seeding for Cart-items initiated!",
        );

        // get the customer
        $jinal = User::where("role", "customer")->first();

        // check
        if (!$jinal) {
            logger()->alert(
                "OOPS! customer not found! CartItems Seeding terminated",
            );
            return;
        }

        // get the product
        // 1.
        $sportsJacketProduct = Product::where(
            "name",
            "Sports Jacket for Women | Girls",
        )->first();

        // check
        if (!$sportsJacketProduct) {
            logger()->alert(
                "OOPS! No product found! CartItems Seeding terminated",
            );
            return;
        }

        // get the variant of that product
        $variantM = ProductVariant::where(
            "product_id",
            $sportsJacketProduct->id,
        )
            ->where("size", "M")
            ->first();

        // check
        if (!$variantM) {
            logger()->alert(
                "OOPS! No variant found! of product: {$sportsJacketProduct->name} CartItems Seeding terminated",
            );
            return;
        }

        // check the quantity of variant m
        if ($variantM->stock < 1) {
            // log the status
            logger()->warning(
                "Couldn't add the variant because Stock is low! Seeding terminated",
            );
            return;
        }
        // add sports jacket product to cart
        $jacketToCart = $jinal->cartItems()->updateOrCreate(
            [
                "product_id" => $sportsJacketProduct->id,
                "variant_id" => $variantM->id,
            ],
            [
                "item_qty" => 2,
            ],
        );

        // log the status
        logger()->info(
            "Added {$sportsJacketProduct->name}'s variant of size:{$variantM->size} | color: {$variantM->color->name} to cart",
            [
                "status" => (bool) $jacketToCart,
            ],
        );

        // 2.
        $cottonTshirt = Product::where("name", "Cotton Levi's Tshirt")->first();

        // check
        if (!$cottonTshirt) {
            logger()->alert(
                "OOPS! No product found! CartItems Seeding terminated",
            );
            return;
        }

        // get the variant of that product
        $variantXS = ProductVariant::where("product_id", $cottonTshirt->id)
            ->where("size", "XS")
            ->first();

        // check
        if (!$variantXS) {
            logger()->alert(
                "OOPS! No variant found! of product: {$cottonTshirt->name} CartItems Seeding terminated",
            );
            return;
        }

        // check the quantity of variant m
        if ($variantXS->stock < 1) {
            // log the status
            logger()->warning(
                "Couldn't add the variant because Stock is low! Seeding terminated",
            );
            return;
        }
        // add sports jacket product to cart
        $jacketToCart = $jinal->cartItems()->updateOrCreate(
            [
                "product_id" => $cottonTshirt->id,
                "variant_id" => $variantXS->id,
            ],
            [
                "item_qty" => $variantXS->stock <= 2 ? 1 : 2,
            ],
        );

        // log the status
        logger()->info(
            "Added {$cottonTshirt->name}'s variant of size:{$variantXS->size} | color: {$variantXS->color->name} to cart",
            [
                "status" => (bool) $cottonTshirt,
            ],
        );

        // end the seeding
        logger()->info("CartItems Seeding complete");
    }
}
