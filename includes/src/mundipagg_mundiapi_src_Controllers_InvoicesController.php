<?php
/*
 * MundiAPILib
 *
 * This file was automatically generated by APIMATIC v2.0 ( https://apimatic.io ).
 */

namespace MundiAPILib\Controllers;

use MundiAPILib\APIException;
use MundiAPILib\APIHelper;
use MundiAPILib\Configuration;
use MundiAPILib\Models;
use MundiAPILib\Exceptions;
use MundiAPILib\Utils\DateTimeHelper;
use MundiAPILib\Http\HttpRequest;
use MundiAPILib\Http\HttpResponse;
use MundiAPILib\Http\HttpMethod;
use MundiAPILib\Http\HttpContext;
use Unirest\Request;

/**
 * @todo Add a general description for this controller.
 */
class InvoicesController extends BaseController
{
    /**
     * @var InvoicesController The reference to *Singleton* instance of this class
     */
    private static $instance;

    /**
     * Returns the *Singleton* instance of this class.
     * @return InvoicesController The *Singleton* instance.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        
        return static::$instance;
    }

    /**
     * Cancels an invoice
     *
     * @param string $invoiceId  Invoice id
     * @return mixed response from the API call
     * @throws APIException Thrown if API call fails
     */
    public function cancelInvoice(
        $invoiceId
    ) {

        //prepare query string for API call
        $_queryBuilder = '/invoices/{invoice_id}';

        //process optional query parameters
        $_queryBuilder = APIHelper::appendUrlWithTemplateParameters($_queryBuilder, array (
            'invoice_id' => $invoiceId,
            ));

        //validate and preprocess url
        $_queryUrl = APIHelper::cleanUrl(Configuration::$BASEURI . $_queryBuilder);

        //prepare headers
        $_headers = array (
            'user-agent'    => BaseController::USER_AGENT,
            'Accept'        => 'application/json'
        );

        //set HTTP basic auth parameters
        Request::auth(Configuration::$basicAuthUserName, Configuration::$basicAuthPassword);

        //call on-before Http callback
        $_httpRequest = new HttpRequest(HttpMethod::DELETE, $_headers, $_queryUrl);
        if ($this->getHttpCallBack() != null) {
            $this->getHttpCallBack()->callOnBeforeRequest($_httpRequest);
        }

        //and invoke the API call request to fetch the response
        $response = Request::delete($_queryUrl, $_headers);

        $_httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $_httpContext = new HttpContext($_httpRequest, $_httpResponse);

        //call on-after Http callback
        if ($this->getHttpCallBack() != null) {
            $this->getHttpCallBack()->callOnAfterRequest($_httpContext);
        }

        //handle errors defined at the API level
        $this->validateResponse($_httpResponse, $_httpContext);

        $mapper = $this->getJsonMapper();

        return $mapper->mapClass($response->body, 'MundiAPILib\\Models\\GetInvoiceResponse');
    }

    /**
     * Gets an invoice
     *
     * @param string $invoiceId  Invoice Id
     * @return mixed response from the API call
     * @throws APIException Thrown if API call fails
     */
    public function getInvoice(
        $invoiceId
    ) {

        //prepare query string for API call
        $_queryBuilder = '/invoices/{invoice_id}';

        //process optional query parameters
        $_queryBuilder = APIHelper::appendUrlWithTemplateParameters($_queryBuilder, array (
            'invoice_id' => $invoiceId,
            ));

        //validate and preprocess url
        $_queryUrl = APIHelper::cleanUrl(Configuration::$BASEURI . $_queryBuilder);

        //prepare headers
        $_headers = array (
            'user-agent'    => BaseController::USER_AGENT,
            'Accept'        => 'application/json'
        );

        //set HTTP basic auth parameters
        Request::auth(Configuration::$basicAuthUserName, Configuration::$basicAuthPassword);

        //call on-before Http callback
        $_httpRequest = new HttpRequest(HttpMethod::GET, $_headers, $_queryUrl);
        if ($this->getHttpCallBack() != null) {
            $this->getHttpCallBack()->callOnBeforeRequest($_httpRequest);
        }

        //and invoke the API call request to fetch the response
        $response = Request::get($_queryUrl, $_headers);

        $_httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $_httpContext = new HttpContext($_httpRequest, $_httpResponse);

        //call on-after Http callback
        if ($this->getHttpCallBack() != null) {
            $this->getHttpCallBack()->callOnAfterRequest($_httpContext);
        }

        //handle errors defined at the API level
        $this->validateResponse($_httpResponse, $_httpContext);

        $mapper = $this->getJsonMapper();

        return $mapper->mapClass($response->body, 'MundiAPILib\\Models\\GetInvoiceResponse');
    }

    /**
     * Create an Invoice
     *
     * @param string                      $subscriptionId  Subscription Id
     * @param string                      $cycleId         Cycle Id
     * @param Models\CreateInvoiceRequest $request         (optional) TODO: type description here
     * @return mixed response from the API call
     * @throws APIException Thrown if API call fails
     */
    public function createInvoice(
        $subscriptionId,
        $cycleId,
        $request = null
    ) {

        //prepare query string for API call
        $_queryBuilder = '/subscriptions/{subscription_id}/cycles/{cycle_id}/pay';

        //process optional query parameters
        $_queryBuilder = APIHelper::appendUrlWithTemplateParameters($_queryBuilder, array (
            'subscription_id' => $subscriptionId,
            'cycle_id'        => $cycleId,
            ));

        //validate and preprocess url
        $_queryUrl = APIHelper::cleanUrl(Configuration::$BASEURI . $_queryBuilder);

        //prepare headers
        $_headers = array (
            'user-agent'    => BaseController::USER_AGENT,
            'Accept'        => 'application/json',
            'content-type'  => 'application/json; charset=utf-8'
        );

        //json encode body
        $_bodyJson = Request\Body::Json($request);

        //set HTTP basic auth parameters
        Request::auth(Configuration::$basicAuthUserName, Configuration::$basicAuthPassword);

        //call on-before Http callback
        $_httpRequest = new HttpRequest(HttpMethod::POST, $_headers, $_queryUrl);
        if ($this->getHttpCallBack() != null) {
            $this->getHttpCallBack()->callOnBeforeRequest($_httpRequest);
        }

        //and invoke the API call request to fetch the response
        $response = Request::post($_queryUrl, $_headers, $_bodyJson);

        $_httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $_httpContext = new HttpContext($_httpRequest, $_httpResponse);

        //call on-after Http callback
        if ($this->getHttpCallBack() != null) {
            $this->getHttpCallBack()->callOnAfterRequest($_httpContext);
        }

        //handle errors defined at the API level
        $this->validateResponse($_httpResponse, $_httpContext);

        $mapper = $this->getJsonMapper();

        return $mapper->mapClass($response->body, 'MundiAPILib\\Models\\GetInvoiceResponse');
    }

    /**
     * Updates the status from an invoice
     *
     * @param string                            $invoiceId  Invoice Id
     * @param Models\UpdateInvoiceStatusRequest $request    Request for updating an invoice's status
     * @return mixed response from the API call
     * @throws APIException Thrown if API call fails
     */
    public function updateInvoiceStatus(
        $invoiceId,
        $request
    ) {

        //prepare query string for API call
        $_queryBuilder = '/invoices/{invoice_id}/status';

        //process optional query parameters
        $_queryBuilder = APIHelper::appendUrlWithTemplateParameters($_queryBuilder, array (
            'invoice_id' => $invoiceId,
            ));

        //validate and preprocess url
        $_queryUrl = APIHelper::cleanUrl(Configuration::$BASEURI . $_queryBuilder);

        //prepare headers
        $_headers = array (
            'user-agent'    => BaseController::USER_AGENT,
            'Accept'        => 'application/json',
            'content-type'  => 'application/json; charset=utf-8'
        );

        //json encode body
        $_bodyJson = Request\Body::Json($request);

        //set HTTP basic auth parameters
        Request::auth(Configuration::$basicAuthUserName, Configuration::$basicAuthPassword);

        //call on-before Http callback
        $_httpRequest = new HttpRequest(HttpMethod::PATCH, $_headers, $_queryUrl);
        if ($this->getHttpCallBack() != null) {
            $this->getHttpCallBack()->callOnBeforeRequest($_httpRequest);
        }

        //and invoke the API call request to fetch the response
        $response = Request::patch($_queryUrl, $_headers, $_bodyJson);

        $_httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $_httpContext = new HttpContext($_httpRequest, $_httpResponse);

        //call on-after Http callback
        if ($this->getHttpCallBack() != null) {
            $this->getHttpCallBack()->callOnAfterRequest($_httpContext);
        }

        //handle errors defined at the API level
        $this->validateResponse($_httpResponse, $_httpContext);

        $mapper = $this->getJsonMapper();

        return $mapper->mapClass($response->body, 'MundiAPILib\\Models\\GetInvoiceResponse');
    }

    /**
     * Gets all invoices
     *
     * @param integer  $page            (optional) Page number
     * @param integer  $size            (optional) Page size
     * @param string   $code            (optional) Filter for Invoice's code
     * @param string   $customerId      (optional) Filter for Invoice's customer id
     * @param string   $subscriptionId  (optional) Filter for Invoice's subscription id
     * @param DateTime $createdSince    (optional) Filter for Invoice's creation date start range
     * @param DateTime $createdUntil    (optional) Filter for Invoices creation date end range
     * @param string   $status          (optional) Filter for Invoice's status
     * @param DateTime $dueSince        (optional) Filter for Invoice's due date start range
     * @param DateTime $dueUntil        (optional) Filter for Invoice's due date end range
     * @return mixed response from the API call
     * @throws APIException Thrown if API call fails
     */
    public function getInvoices(
        $page = null,
        $size = null,
        $code = null,
        $customerId = null,
        $subscriptionId = null,
        $createdSince = null,
        $createdUntil = null,
        $status = null,
        $dueSince = null,
        $dueUntil = null
    ) {

        //prepare query string for API call
        $_queryBuilder = '/invoices';

        //process optional query parameters
        APIHelper::appendUrlWithQueryParameters($_queryBuilder, array (
            'page'            => $page,
            'size'            => $size,
            'code'            => $code,
            'customer_id'     => $customerId,
            'subscription_id' => $subscriptionId,
            'created_since'   => DateTimeHelper::toRfc3339DateTime($createdSince),
            'created_until'   => DateTimeHelper::toRfc3339DateTime($createdUntil),
            'status'          => $status,
            'due_since'       => DateTimeHelper::toRfc3339DateTime($dueSince),
            'due_until'       => DateTimeHelper::toRfc3339DateTime($dueUntil),
        ));

        //validate and preprocess url
        $_queryUrl = APIHelper::cleanUrl(Configuration::$BASEURI . $_queryBuilder);

        //prepare headers
        $_headers = array (
            'user-agent'    => BaseController::USER_AGENT,
            'Accept'        => 'application/json'
        );

        //set HTTP basic auth parameters
        Request::auth(Configuration::$basicAuthUserName, Configuration::$basicAuthPassword);

        //call on-before Http callback
        $_httpRequest = new HttpRequest(HttpMethod::GET, $_headers, $_queryUrl);
        if ($this->getHttpCallBack() != null) {
            $this->getHttpCallBack()->callOnBeforeRequest($_httpRequest);
        }

        //and invoke the API call request to fetch the response
        $response = Request::get($_queryUrl, $_headers);

        $_httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $_httpContext = new HttpContext($_httpRequest, $_httpResponse);

        //call on-after Http callback
        if ($this->getHttpCallBack() != null) {
            $this->getHttpCallBack()->callOnAfterRequest($_httpContext);
        }

        //handle errors defined at the API level
        $this->validateResponse($_httpResponse, $_httpContext);

        $mapper = $this->getJsonMapper();

        return $mapper->mapClass($response->body, 'MundiAPILib\\Models\\ListInvoicesResponse');
    }

    /**
     * Updates the metadata from an invoice
     *
     * @param string                       $invoiceId  The invoice id
     * @param Models\UpdateMetadataRequest $request    Request for updating the invoice metadata
     * @return mixed response from the API call
     * @throws APIException Thrown if API call fails
     */
    public function updateInvoiceMetadata(
        $invoiceId,
        $request
    ) {

        //prepare query string for API call
        $_queryBuilder = '/invoices/{invoice_id}/metadata';

        //process optional query parameters
        $_queryBuilder = APIHelper::appendUrlWithTemplateParameters($_queryBuilder, array (
            'invoice_id' => $invoiceId,
            ));

        //validate and preprocess url
        $_queryUrl = APIHelper::cleanUrl(Configuration::$BASEURI . $_queryBuilder);

        //prepare headers
        $_headers = array (
            'user-agent'    => BaseController::USER_AGENT,
            'Accept'        => 'application/json',
            'content-type'  => 'application/json; charset=utf-8'
        );

        //json encode body
        $_bodyJson = Request\Body::Json($request);

        //set HTTP basic auth parameters
        Request::auth(Configuration::$basicAuthUserName, Configuration::$basicAuthPassword);

        //call on-before Http callback
        $_httpRequest = new HttpRequest(HttpMethod::PATCH, $_headers, $_queryUrl);
        if ($this->getHttpCallBack() != null) {
            $this->getHttpCallBack()->callOnBeforeRequest($_httpRequest);
        }

        //and invoke the API call request to fetch the response
        $response = Request::patch($_queryUrl, $_headers, $_bodyJson);

        $_httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $_httpContext = new HttpContext($_httpRequest, $_httpResponse);

        //call on-after Http callback
        if ($this->getHttpCallBack() != null) {
            $this->getHttpCallBack()->callOnAfterRequest($_httpContext);
        }

        //handle errors defined at the API level
        $this->validateResponse($_httpResponse, $_httpContext);

        $mapper = $this->getJsonMapper();

        return $mapper->mapClass($response->body, 'MundiAPILib\\Models\\GetInvoiceResponse');
    }

    /**
     * @todo Add general description for this endpoint
     *
     * @param string $subscriptionId  Subscription Id
     * @return mixed response from the API call
     * @throws APIException Thrown if API call fails
     */
    public function getPartialInvoice(
        $subscriptionId
    ) {

        //prepare query string for API call
        $_queryBuilder = '/subscriptions/{subscription_id}/partial-invoice';

        //process optional query parameters
        $_queryBuilder = APIHelper::appendUrlWithTemplateParameters($_queryBuilder, array (
            'subscription_id' => $subscriptionId,
            ));

        //validate and preprocess url
        $_queryUrl = APIHelper::cleanUrl(Configuration::$BASEURI . $_queryBuilder);

        //prepare headers
        $_headers = array (
            'user-agent'    => BaseController::USER_AGENT,
            'Accept'        => 'application/json'
        );

        //set HTTP basic auth parameters
        Request::auth(Configuration::$basicAuthUserName, Configuration::$basicAuthPassword);

        //call on-before Http callback
        $_httpRequest = new HttpRequest(HttpMethod::GET, $_headers, $_queryUrl);
        if ($this->getHttpCallBack() != null) {
            $this->getHttpCallBack()->callOnBeforeRequest($_httpRequest);
        }

        //and invoke the API call request to fetch the response
        $response = Request::get($_queryUrl, $_headers);

        $_httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $_httpContext = new HttpContext($_httpRequest, $_httpResponse);

        //call on-after Http callback
        if ($this->getHttpCallBack() != null) {
            $this->getHttpCallBack()->callOnAfterRequest($_httpContext);
        }

        //handle errors defined at the API level
        $this->validateResponse($_httpResponse, $_httpContext);

        $mapper = $this->getJsonMapper();

        return $mapper->mapClass($response->body, 'MundiAPILib\\Models\\GetInvoiceResponse');
    }
}