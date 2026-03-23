<?php

namespace App\Rules;

use App\Models\ProductVariant;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class InStock implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // log the action.
        logger()->info('[app/Rules/InStock@validate] Validating stock!');

        // get the product variant..
        $variant = ProductVariant::find($value);

        // check if incoming
        if ($variant->stock < 1) {

            // log the status
            logger()->alert($variant->id.'variant is Out Of Stock!');

            // redirect client back
            $fail('This item is out of stock');
        }
    }
}
