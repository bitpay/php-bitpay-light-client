<?php


namespace BitPaySDKLight;


use BitPaySDKLight\Exceptions\BillCreationException;
use BitPaySDKLight\Exceptions\BillDeliveryException;
use BitPaySDKLight\Exceptions\BillQueryException;
use BitPaySDKLight\Exceptions\BitPayException;
use BitPaySDKLight\Exceptions\CurrencyQueryException;
use BitPaySDKLight\Exceptions\InvoiceCreationException;
use BitPaySDKLight\Exceptions\InvoiceQueryException;
use BitPaySDKLight\Exceptions\RateQueryException;
use BitPaySDKLight\Model\Bill\Bill;
use BitPaySDKLight\Model\Invoice\Invoice;
use BitPaySDKLight\Model\Rate\Rate;
use BitPaySDKLight\Model\Rate\Rates;
use BitPaySDKLight\Util\JsonMapper\JsonMapper;
use BitPaySDKLight\Util\RESTcli\RESTcli;
use Exception;

/**
 * Class Client
 * @package Bitpay light
 * @author  Antonio Buedo
 * @version 2.1.2112
 * See bitpay.com/api for more information.
 * date 10.12.2021
 */
class Client
{
    protected $_env;
    protected $_token;

    /**
     * @var RESTcli
     */
    protected $_RESTcli = null;

    /**
     * Constructor for the BitPay SDK to use with the POS facade.
     *
     * @param $token       string The token generated on the BitPay account.
     * @param $environment string The target environment [Default: Production].
     *
     * @throws BitPayException BitPayException class
     */
    public function __construct(string $token, string $environment = null)
    {
        try {
            $this->_token = $token;
            $this->_env = strtolower($environment) == "test" ? Env::Test : Env::Prod;
            $this->init();
        } catch (Exception $e) {
            throw new BitPayException("failed to initialize BitPay Light Client (Config) : ".$e->getMessage());
        }

    }

    /**
     * Initialize this object with the selected environment.
     *
     * @throws BitPayException BitPayException class
     */
    private function init()
    {
        try {
            $this->_RESTcli = new RESTcli($this->_env);
        } catch (Exception $e) {
            throw new BitPayException("failed to build configuration : ".$e->getMessage());
        }
    }

    /**
     * Returns a GUID for use in REST calls.
     *
     * @see http://en.wikipedia.org/wiki/Globally_unique_identifier
     *
     * @return string
     */
    private function guid()
    {
        return sprintf(
            '%s-%s-%s-%s-%s',
            bin2hex(openssl_random_pseudo_bytes(4)),
            bin2hex(openssl_random_pseudo_bytes(2)),
            bin2hex(openssl_random_pseudo_bytes(2)),
            bin2hex(openssl_random_pseudo_bytes(2)),
            bin2hex(openssl_random_pseudo_bytes(6))
        );
    }

    /**
     * Create a BitPay invoice.
     *
     * @param $invoice Invoice An Invoice object with request parameters defined.
     *
     * @return Invoice $invoice Invoice A BitPay generated Invoice object.
     * @throws InvoiceCreationException InvoiceCreationException class
     * @throws BitPayException BitPayException class
     */
    public function createInvoice(
        Invoice $invoice
    ): Invoice {
        try {
            $invoice->setToken($this->_token);
            $invoice->setGuid($this->guid());

            $responseJson = $this->_RESTcli->post("invoices", $invoice->toArray());
        } catch (BitPayException $e) {
            throw new InvoiceCreationException("failed to serialize Invoice object : ".$e->getMessage(), null, null, $e->getApiCode());
        } catch (Exception $e) {
            throw new InvoiceCreationException("failed to serialize Invoice object : ".$e->getMessage());
        }

        try {
            $mapper = new JsonMapper();
            $invoice = $mapper->map(
                json_decode($responseJson),
                new Invoice()
            );

        } catch (Exception $e) {
            throw new InvoiceCreationException(
                "failed to deserialize BitPay server response (Invoice) : ".$e->getMessage());
        }

        return $invoice;
    }

    /**
     * Retrieve a BitPay invoice.
     *
     * @param $invoiceId string The id of the invoice to retrieve.
     *
     * @return Invoice A BitPay Invoice object.
     * @throws InvoiceQueryException InvoiceQueryException class
     * @throws BitPayException BitPayException class
     */
    public function getInvoice(
        string $invoiceId
    ): Invoice {
        try {
            $params = [];
            $params["token"] = $this->_token;

            $responseJson = $this->_RESTcli->get("invoices/".$invoiceId, $params);
        } catch (BitPayException $e) {
            throw new InvoiceQueryException("failed to serialize Invoice object : ".$e->getMessage(), null, null, $e->getApiCode());
        } catch (Exception $e) {
            throw new InvoiceQueryException("failed to serialize Invoice object : ".$e->getMessage());
        }

        try {
            $mapper = new JsonMapper();
            $invoice = $mapper->map(
                json_decode($responseJson),
                new Invoice()
            );

        } catch (Exception $e) {
            throw new InvoiceQueryException(
                "failed to deserialize BitPay server response (Invoice) : ".$e->getMessage());
        }

        return $invoice;
    }

    /**
     * Create a BitPay Bill.
     *
     * @param Bill $bill string A Bill object with request parameters defined.
     *
     * @return Bill A BitPay generated Bill object.
     * @throws BillCreationException BillCreationException class
     * @throws BitPayException BitPayException class
     */
    public function createBill(Bill $bill): Bill
    {
        try {
            $bill->setToken($this->_token);

            $responseJson = $this->_RESTcli->post("bills", $bill->toArray());
        } catch (BitPayException $e) {
            throw new BillCreationException("failed to serialize Bill object : ".$e->getMessage(), null, null, $e->getApiCode());
        } catch (Exception $e) {
            throw new BillCreationException("failed to serialize Bill object : ".$e->getMessage());
        }

        try {
            $mapper = new JsonMapper();
            $bill = $mapper->map(
                json_decode($responseJson),
                new Bill()
            );

        } catch (Exception $e) {
            throw new BillCreationException(
                "failed to deserialize BitPay server response (Bill) : ".$e->getMessage());
        }

        return $bill;
    }

    /**
     * Retrieve a BitPay bill by bill id.
     *
     * @param $billId string The id of the bill to retrieve.
     * @return Bill A BitPay Bill object.
     * @throws BillQueryException BillQueryException class
     * @throws BitPayException BitPayException class
     */
    public function getBill(string $billId): Bill
    {

        try {
            $params = [];
            $params["token"] = $this->_token;

            $responseJson = $this->_RESTcli->get("bills/".$billId, $params);
        } catch (BitPayException $e) {
            throw new BillQueryException("failed to serialize Bill object : ".$e->getMessage(), null, null, $e->getApiCode());
        } catch (Exception $e) {
            throw new BillQueryException("failed to serialize Bill object : ".$e->getMessage());
        }

        try {
            $mapper = new JsonMapper();
            $bill = $mapper->map(
                json_decode($responseJson),
                new Bill()
            );

        } catch (Exception $e) {
            throw new BillQueryException(
                "failed to deserialize BitPay server response (Bill) : ".$e->getMessage());
        }

        return $bill;
    }

    /**
     * Deliver a BitPay Bill.
     *
     * @param $billId      string The id of the requested bill.
     * @param $billToken   string The token of the requested bill.
     *
     * @return string A response status returned from the API.
     * @throws BillDeliveryException BillDeliveryException class
     * @throws BitPayException BitPayException class
     */
    public function deliverBill(string $billId, string $billToken): string
    {
        try {
            $responseJson = $this->_RESTcli->post(
                "bills/".$billId."/deliveries", ['token' => $billToken]);
        } catch (BitPayException $e) {
            throw new BillDeliveryException("failed to serialize Bill object : ".$e->getMessage(), null, null, $e->getApiCode());
        } catch (Exception $e) {
            throw new BillDeliveryException("failed to serialize Bill object : ".$e->getMessage());
        }

        try {
            $result = str_replace("\"", "", $responseJson);
        } catch (Exception $e) {
            throw new BillDeliveryException("failed to deserialize BitPay server response (Bill) : ".$e->getMessage());
        }

        return $result;
    }

    /**
     * Retrieve the exchange rate table maintained by BitPay.  See https://bitpay.com/bitcoin-exchange-rates.
     *
     * @return Rates A Rates object populated with the BitPay exchange rate table.
     * @throws RateQueryException RateQueryException class
     * @throws BitPayException BitPayException class
     */
    public function getRates(): Rates
    {
        try {
            $responseJson = $this->_RESTcli->get("rates", null);
        } catch (BitPayException $e) {
            throw new RateQueryException("failed to serialize Rates object : ".$e->getMessage(), null, null, $e->getApiCode());
        } catch (Exception $e) {
            throw new RateQueryException("failed to serialize Rates object : ".$e->getMessage());
        }

        try {
            $mapper = new JsonMapper();
            $rates = $mapper->mapArray(
                json_decode($responseJson),
                [],
                'BitPaySDKLight\Model\Rate\Rate'
            );

        } catch (Exception $e) {
            throw new RateQueryException(
                "failed to deserialize BitPay server response (Rates) : ".$e->getMessage());
        }

        return new Rates($rates, $this);
    }

    /**
     * Retrieve all the rates for a given cryptocurrency
     *
     * @param string $baseCurrency The cryptocurrency for which you want to fetch the rates.
     *                             Current supported values are BTC, BCH, ETH, XRP, DOGE and LTC
     * @return Rates A Rates object populated with the currency rates for the requested baseCurrency.
     * @throws BitPayException BitPayException class
     */
    public function getCurrencyRates(string $baseCurrency): Rates
    {
        try {
            $responseJson = $this->_RESTcli->get("rates/".$baseCurrency, null, false);
        } catch (Exception $e) {
            throw new RateQueryException("failed to serialize Rates object : ".$e->getMessage());
        }

        try {
            $mapper = new JsonMapper();
            $rates = $mapper->mapArray(
                json_decode($responseJson),
                [],
                'BitPaySDKLight\Model\Rate\Rate'
            );

        } catch (Exception $e) {
            throw new RateQueryException(
                "failed to deserialize BitPay server response (Rates) : ".$e->getMessage());
        }

        return new Rates($rates, $this);
    }

    /**
     * Retrieve the rate for a cryptocurrency / fiat pair
     *
     * @param string $baseCurrency The cryptocurrency for which you want to fetch the fiat-equivalent rate.
     *                             Current supported values are BTC, BCH, ETH, XRP, DOGE and LTC
     * @param string $currency The fiat currency for which you want to fetch the baseCurrency rate
     * @return Rate A Rate object populated with the currency rate for the requested baseCurrency.
     * @throws BitPayException BitPayException class
     */
    public function getCurrencyPairRate(string $baseCurrency, string $currency): Rate
    {
        try {
            $responseJson = $this->_RESTcli->get("rates/".$baseCurrency."/".$currency, null, false);
        } catch (Exception $e) {
            throw new RateQueryException("failed to serialize Rate object : ".$e->getMessage());
        }

        try {
            $mapper = new JsonMapper();
            $rate = $mapper->map(
                json_decode($responseJson),
                new Rate()
            );

        } catch (Exception $e) {
            throw new RateQueryException(
                "failed to deserialize BitPay server response (Rate) : ".$e->getMessage());
        }

        return $rate;
    }

    /**
     * Fetch the supported currencies.
     *
     * @return array     A list of BitPay Invoice objects.
     * @throws CurrencyQueryException CurrencyQueryException class
     * @throws BitPayException BitPayException class
     */
    public function getCurrencies(): array
    {
        try {
            $responseJson = $this->_RESTcli->get("currencies", null);
        } catch (BitPayException $e) {
            throw new CurrencyQueryException("failed to serialize Currency object : ".$e->getMessage(), null, null, $e->getApiCode());
        } catch (Exception $e) {
            throw new CurrencyQueryException("failed to serialize Currency object : ".$e->getMessage());
        }

        try {
            $mapper = new JsonMapper();
            $currencies = $mapper->mapArray(
                json_decode($responseJson),
                [],
                'BitPaySDKLight\Model\Currency'
            );

        } catch (Exception $e) {
            throw new CurrencyQueryException(
                "failed to deserialize BitPay server response (Currency) : ".$e->getMessage());
        }

        return $currencies;
    }
}