<?php
/*
 * MundiAPILib
 *
 * This file was automatically generated by APIMATIC v2.0 ( https://apimatic.io ).
 */

namespace MundiAPILib\Models;

use JsonSerializable;

/**
 *The settings for creating a debit card payment
 */
class CreateDebitCardPaymentRequest implements JsonSerializable
{
    /**
     * The text that will be shown on the debit card's statement
     * @maps statement_descriptor
     * @var string|null $statementDescriptor public property
     */
    public $statementDescriptor;

    /**
     * Debit card data
     * @var \MundiAPILib\Models\CreateCardRequest|null $card public property
     */
    public $card;

    /**
     * The debit card id
     * @maps card_id
     * @var string|null $cardId public property
     */
    public $cardId;

    /**
     * The debit card token
     * @maps card_token
     * @var string|null $cardToken public property
     */
    public $cardToken;

    /**
     * Indicates a recurrence
     * @var bool|null $recurrence public property
     */
    public $recurrence;

    /**
     * The payment authentication request
     * @var \MundiAPILib\Models\CreatePaymentAuthenticationRequest|null $authentication public property
     */
    public $authentication;

    /**
     * The Debit card payment token request
     * @var \MundiAPILib\Models\CreateCardPaymentTokenRequest|null $token public property
     */
    public $token;

    /**
     * Constructor to set initial or default values of member properties
     * @param string                             $statementDescriptor Initialization value for $this-
     *                                                                  >statementDescriptor
     * @param CreateCardRequest                  $card                Initialization value for $this->card
     * @param string                             $cardId              Initialization value for $this->cardId
     * @param string                             $cardToken           Initialization value for $this->cardToken
     * @param bool                               $recurrence          Initialization value for $this->recurrence
     * @param CreatePaymentAuthenticationRequest $authentication      Initialization value for $this->authentication
     * @param CreateCardPaymentTokenRequest      $token               Initialization value for $this->token
     */
    public function __construct()
    {
        if (7 == func_num_args()) {
            $this->statementDescriptor = func_get_arg(0);
            $this->card                = func_get_arg(1);
            $this->cardId              = func_get_arg(2);
            $this->cardToken           = func_get_arg(3);
            $this->recurrence          = func_get_arg(4);
            $this->authentication      = func_get_arg(5);
            $this->token               = func_get_arg(6);
        }
    }


    /**
     * Encode this object to JSON
     */
    public function jsonSerialize()
    {
        $json = array();
        $json['statement_descriptor'] = $this->statementDescriptor;
        $json['card']                 = $this->card;
        $json['card_id']              = $this->cardId;
        $json['card_token']           = $this->cardToken;
        $json['recurrence']           = $this->recurrence;
        $json['authentication']       = $this->authentication;
        $json['token']                = $this->token;

        return $json;
    }
}
