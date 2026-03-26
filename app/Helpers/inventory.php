<?php

// if function doesn't exist
if(! function_exists('deductVariantStock')) {

    // () -> handle variant stock deduction..
    function deductVariantStock($variant, $qty) {

        // log the action
        logger()->info("[app\Helpers\inventory.php@deductVariantStock] Initiating Variant Stock Deduction!");

        // check if variant stock is valid!
        if($variant->stock < $qty) {
            // log the alert
            logger()->alert("Insufficient stock", [
                'variant_id' => $variant->id,
                'stock' => $variant->stock,
                'requested' => $qty
            ]);

            // throw the error.
            throw new Exception("Insufficient Variant {$variant->id}'s Stock | Deduction is not possible.");
        }

        // reduce the stock according to qty
        // for that variant
        $variant->decrement('stock', $qty);

        // log the status
        logger()->info("Deducted Variant {$variant->id}'s stock by {$qty}");

        // log the end.
        logger()->info("Variant Stock Deduction ended!");
    }
}
