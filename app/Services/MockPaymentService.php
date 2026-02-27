<?php

// folder path
namespace app\Services;

// path to class..

// UDC MockPaymentService
class MockPaymentService
{

    /**
     * simulate the payment state
     */
    public function charge($order, array $validated)
    {
        // log the action..
        logger()->info('[app\Services\MockPaymentService@charge] MockPaymentService started!');

        // initial success
        $success = 1;

        // for intentional failure
        if ($validated['card_number'] === '0000 0000 0000 0000') {

            // update the state..
            $success = 0;
        }

        // log the action..
        logger()->info('Payment Service Executed!', [
            'orderNumber' => $order->order_number,
            'status' => (bool) $success
        ]);

        // log the end..
        logger()->info('MockPaymentService ended.');

        // get the payment service response
        return $success;
    }
}
