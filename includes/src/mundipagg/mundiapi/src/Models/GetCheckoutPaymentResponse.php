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
 *Resposta das configurações de pagamento do checkout
 */
class GetCheckoutPaymentResponse implements JsonSerializable
{
    /**
     * @todo Write general description for this property
     * @required
     * @var string $id public property
     */
    public $id;

    /**
     * Valor em centavos
     * @var integer|null $amount public property
     */
    public $amount;

    /**
     * Meio de pagamento padrão no checkout
     * @required
     * @maps default_payment_method
     * @var string $defaultPaymentMethod public property
     */
    public $defaultPaymentMethod;

    /**
     * Url de redirecionamento de sucesso após o checkou
     * @required
     * @maps success_url
     * @var string $successUrl public property
     */
    public $successUrl;

    /**
     * Url para pagamento usando o checkout
     * @required
     * @maps payment_url
     * @var string $paymentUrl public property
     */
    public $paymentUrl;

    /**
     * Código da afiliação onde o pagamento será processado no gateway
     * @required
     * @maps gateway_affiliation_id
     * @var string $gatewayAffiliationId public property
     */
    public $gatewayAffiliationId;

    /**
     * Meios de pagamento aceitos no checkout
     * @required
     * @maps accepted_payment_methods
     * @var array $acceptedPaymentMethods public property
     */
    public $acceptedPaymentMethods;

    /**
     * Status do checkout
     * @required
     * @var string $status public property
     */
    public $status;

    /**
     * Pular tela de sucesso pós-pagamento?
     * @required
     * @maps skip_checkout_success_page
     * @var bool $skipCheckoutSuccessPage public property
     */
    public $skipCheckoutSuccessPage;

    /**
     * Data de criação
     * @required
     * @maps created_at
     * @factory \MundiAPILib\Utils\DateTimeHelper::fromRfc3339DateTime
     * @var \DateTime $createdAt public property
     */
    public $createdAt;

    /**
     * Data de atualização
     * @required
     * @maps updated_at
     * @factory \MundiAPILib\Utils\DateTimeHelper::fromRfc3339DateTime
     * @var \DateTime $updatedAt public property
     */
    public $updatedAt;

    /**
     * Data de cancelamento
     * @maps canceled_at
     * @factory \MundiAPILib\Utils\DateTimeHelper::fromRfc3339DateTime
     * @var \DateTime|null $canceledAt public property
     */
    public $canceledAt;

    /**
     * Torna o objeto customer editável
     * @required
     * @maps customer_editable
     * @var bool $customerEditable public property
     */
    public $customerEditable;

    /**
     * Dados do comprador
     * @required
     * @var \MundiAPILib\Models\GetCustomerResponse $customer public property
     */
    public $customer;

    /**
     * Dados do endereço de cobrança
     * @required
     * @var \MundiAPILib\Models\GetAddressResponse $billingaddress public property
     */
    public $billingaddress;

    /**
     * Configurações de cartão de crédito
     * @required
     * @maps credit_Card
     * @var \MundiAPILib\Models\GetCheckoutCreditCardPaymentResponse $creditCard public property
     */
    public $creditCard;

    /**
     * Configurações de boleto
     * @required
     * @var \MundiAPILib\Models\GetCheckoutBoletoPaymentResponse $boleto public property
     */
    public $boleto;

    /**
     * Indica se o billing address poderá ser editado
     * @required
     * @maps billing_address_editable
     * @var bool $billingAddressEditable public property
     */
    public $billingAddressEditable;

    /**
     * Configurações  de entrega
     * @required
     * @var \MundiAPILib\Models\GetShippingResponse $shipping public property
     */
    public $shipping;

    /**
     * Indica se possui entrega
     * @required
     * @var bool $shippable public property
     */
    public $shippable;

    /**
     * Data de fechamento
     * @maps closed_at
     * @factory \MundiAPILib\Utils\DateTimeHelper::fromRfc3339DateTime
     * @var \DateTime|null $closedAt public property
     */
    public $closedAt;

    /**
     * Data de expiração
     * @maps expires_at
     * @factory \MundiAPILib\Utils\DateTimeHelper::fromRfc3339DateTime
     * @var \DateTime|null $expiresAt public property
     */
    public $expiresAt;

    /**
     * Moeda
     * @required
     * @var string $currency public property
     */
    public $currency;

    /**
     * Configurações de cartão de débito
     * @maps debit_card
     * @var \MundiAPILib\Models\GetCheckoutDebitCardPaymentResponse|null $debitCard public property
     */
    public $debitCard;

    /**
     * Constructor to set initial or default values of member properties
     * @param string                                $id                      Initialization value for $this->id
     * @param integer                               $amount                  Initialization value for $this->amount
     * @param string                                $defaultPaymentMethod    Initialization value for $this-
     *                                                                         >defaultPaymentMethod
     * @param string                                $successUrl              Initialization value for $this-
     *                                                                         >successUrl
     * @param string                                $paymentUrl              Initialization value for $this-
     *                                                                         >paymentUrl
     * @param string                                $gatewayAffiliationId    Initialization value for $this-
     *                                                                         >gatewayAffiliationId
     * @param array                                 $acceptedPaymentMethods  Initialization value for $this-
     *                                                                         >acceptedPaymentMethods
     * @param string                                $status                  Initialization value for $this->status
     * @param bool                                  $skipCheckoutSuccessPage Initialization value for $this-
     *                                                                         >skipCheckoutSuccessPage
     * @param \DateTime                             $createdAt               Initialization value for $this-
     *                                                                         >createdAt
     * @param \DateTime                             $updatedAt               Initialization value for $this-
     *                                                                         >updatedAt
     * @param \DateTime                             $canceledAt              Initialization value for $this-
     *                                                                         >canceledAt
     * @param bool                                  $customerEditable        Initialization value for $this-
     *                                                                         >customerEditable
     * @param GetCustomerResponse                   $customer                Initialization value for $this->customer
     * @param GetAddressResponse                    $billingaddress          Initialization value for $this-
     *                                                                         >billingaddress
     * @param GetCheckoutCreditCardPaymentResponse  $creditCard              Initialization value for $this-
     *                                                                         >creditCard
     * @param GetCheckoutBoletoPaymentResponse      $boleto                  Initialization value for $this->boleto
     * @param bool                                  $billingAddressEditable  Initialization value for $this-
     *                                                                         >billingAddressEditable
     * @param GetShippingResponse                   $shipping                Initialization value for $this->shipping
     * @param bool                                  $shippable               Initialization value for $this-
     *                                                                         >shippable
     * @param \DateTime                             $closedAt                Initialization value for $this->closedAt
     * @param \DateTime                             $expiresAt               Initialization value for $this-
     *                                                                         >expiresAt
     * @param string                                $currency                Initialization value for $this->currency
     * @param GetCheckoutDebitCardPaymentResponse   $debitCard               Initialization value for $this-
     *                                                                         >debitCard
     */
    public function __construct()
    {
        if (24 == func_num_args()) {
            $this->id                      = func_get_arg(0);
            $this->amount                  = func_get_arg(1);
            $this->defaultPaymentMethod    = func_get_arg(2);
            $this->successUrl              = func_get_arg(3);
            $this->paymentUrl              = func_get_arg(4);
            $this->gatewayAffiliationId    = func_get_arg(5);
            $this->acceptedPaymentMethods  = func_get_arg(6);
            $this->status                  = func_get_arg(7);
            $this->skipCheckoutSuccessPage = func_get_arg(8);
            $this->createdAt               = func_get_arg(9);
            $this->updatedAt               = func_get_arg(10);
            $this->canceledAt              = func_get_arg(11);
            $this->customerEditable        = func_get_arg(12);
            $this->customer                = func_get_arg(13);
            $this->billingaddress          = func_get_arg(14);
            $this->creditCard              = func_get_arg(15);
            $this->boleto                  = func_get_arg(16);
            $this->billingAddressEditable  = func_get_arg(17);
            $this->shipping                = func_get_arg(18);
            $this->shippable               = func_get_arg(19);
            $this->closedAt                = func_get_arg(20);
            $this->expiresAt               = func_get_arg(21);
            $this->currency                = func_get_arg(22);
            $this->debitCard               = func_get_arg(23);
        }
    }


    /**
     * Encode this object to JSON
     */
    public function jsonSerialize()
    {
        $json = array();
        $json['id']                         = $this->id;
        $json['amount']                     = $this->amount;
        $json['default_payment_method']     = $this->defaultPaymentMethod;
        $json['success_url']                = $this->successUrl;
        $json['payment_url']                = $this->paymentUrl;
        $json['gateway_affiliation_id']     = $this->gatewayAffiliationId;
        $json['accepted_payment_methods']   = $this->acceptedPaymentMethods;
        $json['status']                     = $this->status;
        $json['skip_checkout_success_page'] = $this->skipCheckoutSuccessPage;
        $json['created_at']                 = DateTimeHelper::toRfc3339DateTime($this->createdAt);
        $json['updated_at']                 = DateTimeHelper::toRfc3339DateTime($this->updatedAt);
        $json['canceled_at']                = isset($this->canceledAt) ?
            DateTimeHelper::toRfc3339DateTime($this->canceledAt) : null;
        $json['customer_editable']          = $this->customerEditable;
        $json['customer']                   = $this->customer;
        $json['billingaddress']             = $this->billingaddress;
        $json['credit_Card']                = $this->creditCard;
        $json['boleto']                     = $this->boleto;
        $json['billing_address_editable']   = $this->billingAddressEditable;
        $json['shipping']                   = $this->shipping;
        $json['shippable']                  = $this->shippable;
        $json['closed_at']                  = isset($this->closedAt) ?
            DateTimeHelper::toRfc3339DateTime($this->closedAt) : null;
        $json['expires_at']                 = isset($this->expiresAt) ?
            DateTimeHelper::toRfc3339DateTime($this->expiresAt) : null;
        $json['currency']                   = $this->currency;
        $json['debit_card']                 = $this->debitCard;

        return $json;
    }
}
