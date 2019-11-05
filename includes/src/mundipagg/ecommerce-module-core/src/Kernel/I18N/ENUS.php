<?php

namespace Mundipagg\Core\Kernel\I18N;

use Mundipagg\Core\Kernel\Abstractions\AbstractI18NTable;

class ENUS extends AbstractI18NTable
{
    protected function getTable()
    {
        return [
            'Invoice created: #%s.' => null,
            'Invoice canceled: #%s.' => null,
            'Webhook received: %s.%s' => null,
            'Order paid.' => null,
            'Order created at Mundipagg. Id: %s' => null,
            'Order waiting for online retries at Mundipagg.' => null,
            'Order canceled.' => null,
            'Payment received: %.2f' => null,
            'Canceled amount: %.2f' => null,
            'Refunded amount: %.2f' => null,
            'Partial Payment' => null,
            'Charge canceled.' => null,
            'Charge not found' => null,
            'Creditmemo created: #%s.' => null,
            'until now' => null,
            'Extra amount paid: %.2f' => null,
            "Order '%s' canceled at Mundipagg" => null,
            'Remaining amount: %.2f' => null,
            "Some charges couldn't be canceled at Mundipagg. Reasons:" => null,
            "without interest" => null,
            "with %.2f%% of interest" => null,
            "%dx of %s %s (Total: %s)" => null,
            "Order payment failed" => null,
            "The order will be canceled" => null,
            "An error occurred when trying to create the order. Please try again. Error Reference: %s." => null,
            "Can't cancel current order. Please cancel it by Mundipagg panel" => null,
            "Charge canceled with success" => null,
            'Invalid address. Please fill the street lines and try again.' => null,
            "The informed card couldn't be deleted." => null,
            "The card '%s' was deleted." => null,
            "The card '%s' couldn't be deleted." => null,
            "Different paid amount for this invoice. Paid value: %.2f" => null,
            "The %s should not be empty!" => null,
            "street" => null,
            "number" => null,
            "neighborhood" => null,
            "city" => null,
            "country" => null,
            "state" => null,
            "document" => null,
            "Can't create order." => null,
            'Invalid address configuration. Please fill the address configuration on admin panel.' => null
        ];
    }
}