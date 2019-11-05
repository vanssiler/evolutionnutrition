<?php
/*
 * MundiAPILib
 *
 * This file was automatically generated by APIMATIC v2.0 ( https://apimatic.io ).
 */

namespace MundiAPILib\Models;

use JsonSerializable;

/**
 *The settings for creating a voucher payment
 */
class CreateVoucherPaymentRequest implements JsonSerializable
{
    /**
     * The text that will be shown on the voucher's statement
     * @required
     * @maps statement_descriptor
     * @var string $statementDescriptor public property
     */
    public $statementDescriptor;

    /**
     * Card id
     * @required
     * @maps card_id
     * @var string $cardId public property
     */
    public $cardId;

    /**
     * Card token
     * @required
     * @maps card_token
     * @var string $cardToken public property
     */
    public $cardToken;

    /**
     * Card info
     * @required
     * @maps Card
     * @var \MundiAPILib\Models\CreateCardRequest $card public property
     */
    public $card;

    /**
     * Constructor to set initial or default values of member properties
     * @param string            $statementDescriptor Initialization value for $this->statementDescriptor
     * @param string            $cardId              Initialization value for $this->cardId
     * @param string            $cardToken           Initialization value for $this->cardToken
     * @param CreateCardRequest $card                Initialization value for $this->card
     */
    public function __construct()
    {
        if (4 == func_num_args()) {
            $this->statementDescriptor = func_get_arg(0);
            $this->cardId              = func_get_arg(1);
            $this->cardToken           = func_get_arg(2);
            $this->card                = func_get_arg(3);
        }
    }


    /**
     * Encode this object to JSON
     */
    public function jsonSerialize()
    {
        $json = array();
        $json['statement_descriptor'] = $this->statementDescriptor;
        $json['card_id']              = $this->cardId;
        $json['card_token']           = $this->cardToken;
        $json['Card']                 = $this->card;

        return $json;
    }
}
