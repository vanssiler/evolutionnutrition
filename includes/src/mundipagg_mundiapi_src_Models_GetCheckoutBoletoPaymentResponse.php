<?php
/*
 * MundiAPILib
 *
 * This file was automatically generated by APIMATIC v2.0 ( https://apimatic.io ).
 */

namespace MundiAPILib\Models;

use JsonSerializable;
use MundiAPILib\Utils\DateTimeHelper;

/**
 * @todo Write general description for this model
 */
class GetCheckoutBoletoPaymentResponse implements JsonSerializable
{
    /**
     * Data de vencimento do boleto
     * @required
     * @maps due_at
     * @factory \MundiAPILib\Utils\DateTimeHelper::fromRfc3339DateTime
     * @var \DateTime $dueAt public property
     */
    public $dueAt;

    /**
     * Instruções do boleto
     * @required
     * @var string $instructions public property
     */
    public $instructions;

    /**
     * Constructor to set initial or default values of member properties
     * @param \DateTime $dueAt        Initialization value for $this->dueAt
     * @param string    $instructions Initialization value for $this->instructions
     */
    public function __construct()
    {
        if (2 == func_num_args()) {
            $this->dueAt        = func_get_arg(0);
            $this->instructions = func_get_arg(1);
        }
    }


    /**
     * Encode this object to JSON
     */
    public function jsonSerialize()
    {
        $json = array();
        $json['due_at']       = DateTimeHelper::toRfc3339DateTime($this->dueAt);
        $json['instructions'] = $this->instructions;

        return $json;
    }
}
