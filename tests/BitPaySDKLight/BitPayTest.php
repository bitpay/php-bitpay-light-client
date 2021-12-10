<?php

namespace BitPaySDKLight\Test;


use BitPaySDKLight;
use BitPaySDKLight\Client;
use BitPaySDKLight\Env;
use BitPaySDKLight\Model\Bill\Bill;
use BitPaySDKLight\Model\Bill\BillStatus;
use BitPaySDKLight\Model\Bill\Item;
use BitPaySDKLight\Model\Currency;
use BitPaySDKLight\Model\Invoice\Buyer;
use BitPaySDKLight\Model\Invoice\Invoice as Invoice;
use PHPUnit\Framework\TestCase;

class BitPayTest extends TestCase
{
    /**
     * @var BitPaySDKLight\Client
     */
    protected $_client;
    protected $_clientMock;
    protected $_environment = Env::Test;
    protected $_token       = "CFJCZH3VitcEER9Uybx8LMvkPsSWzpSWvN4vhNEJp47b";

    protected function setUp(): void
    {
        /**
         * You need to generate new tokens first
         * */
        $this->_clientMock = $this->createMock(Client::class);
        try {
            $this->_client = new Client($this->_token, $this->_environment);
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertNotNull($this->_client);
    }

    public function testShouldGetInvoiceId()
    {
        $invoice = new Invoice(2.16, "eur");
        $invoice->setOrderId("98e572ea-910e-415d-b6de-65f5090680f6");
        $invoice->setFullNotifications(true);
        $invoice->setExtendedNotifications(true);
        $invoice->setTransactionSpeed("medium");
        $invoice->setNotificationURL("https://hookbin.com/lJnJg9WW7MtG9GZlPVdj");
        $invoice->setRedirectURL("https://hookbin.com/lJnJg9WW7MtG9GZlPVdj");
        $invoice->setPosData("98e572ea35hj356xft8y8cgh56h5090680f6");
        $invoice->setItemDesc("Ab tempora sed ut.");
        $invoice->setNotificationEmail("");

        $buyer = new Buyer();
        $buyer->setName("Bily Matthews");
        $buyer->setEmail("");
        $buyer->setAddress1("168 General Grove");
        $buyer->setAddress2("");
        $buyer->setCountry("AD");
        $buyer->setLocality("Port Horizon");
        $buyer->setNotify(true);
        $buyer->setPhone("+99477512690");
        $buyer->setPostalCode("KY7 1TH");
        $buyer->setRegion("New Port");

        $invoice->setBuyer($buyer);

        try {
            $basicInvoice = $this->_client->createInvoice($invoice);
            $retrievedInvoice = $this->_client->getInvoice($basicInvoice->getId());
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertNotNull($basicInvoice->getId());
        $this->assertNotNull($retrievedInvoice->getId());
        $this->assertEquals($basicInvoice->getId(), $retrievedInvoice->getId());
    }

    public function testShouldCreateInvoiceBtc()
    {
        try {
            $basicInvoice = $this->_client->createInvoice(new Invoice(0.1, Currency::BTC));
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertNotNull($basicInvoice->getId());

    }

    public function testShouldCreateInvoiceBch()
    {
        try {
            $basicInvoice = $this->_client->createInvoice(new Invoice(0.1, Currency::BCH));
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertNotNull($basicInvoice->getId());

    }

    public function testShouldCreateInvoiceEth()
    {
        try {
            $basicInvoice = $this->_client->createInvoice(new Invoice(0.1, Currency::ETH));
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertNotNull($basicInvoice->getId());

    }

    public function testShouldCreateBillUSD()
    {
        $items = [];

        $item = new Item();
        $item->setPrice(30.0);
        $item->setQuantity(9);
        $item->setDescription("product-a");
        array_push($items, $item);

        $item = new Item();
        $item->setPrice(14.0);
        $item->setQuantity(16);
        $item->setDescription("product-b");
        array_push($items, $item);

        $item = new Item();
        $item->setPrice(3.90);
        $item->setQuantity(42);
        $item->setDescription("product-c");
        array_push($items, $item);

        $item = new Item();
        $item->setPrice(6.99);
        $item->setQuantity(12);
        $item->setDescription("product-d");
        array_push($items, $item);

        $bill = new Bill("1001", Currency::USD, "", $items);
        $bill->setEmail("sandbox@bitpay.com");
        $basicBill = null;
        try {
            $basicBill = $this->_client->createBill($bill);
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertNotNull($basicBill->getId());
        $this->assertNotNull($basicBill->getItems()[0]->getId());
    }

    public function testShouldCreateBillEUR()
    {
        $items = [];

        $item = new Item();
        $item->setPrice(30.0);
        $item->setQuantity(9);
        $item->setDescription("product-a");
        array_push($items, $item);

        $item = new Item();
        $item->setPrice(14.0);
        $item->setQuantity(16);
        $item->setDescription("product-b");
        array_push($items, $item);

        $item = new Item();
        $item->setPrice(3.90);
        $item->setQuantity(42);
        $item->setDescription("product-c");
        array_push($items, $item);

        $item = new Item();
        $item->setPrice(6.99);
        $item->setQuantity(12);
        $item->setDescription("product-d");
        array_push($items, $item);

        $bill = new Bill("1002", Currency::EUR, "", $items);
        $bill->setEmail("sandbox@bitpay.com");
        $basicBill = null;
        try {
            $basicBill = $this->_client->createBill($bill);
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertNotNull($basicBill->getId());
        $this->assertNotNull($basicBill->getUrl());
        $this->assertEquals(BillStatus::Draft, $basicBill->getStatus());
        $this->assertNotNull($basicBill->getItems()[0]->getId());
    }

    public function testShouldGetBill()
    {
        $items = [];

        $item = new Item();
        $item->setPrice(30.0);
        $item->setQuantity(9);
        $item->setDescription("product-a");
        array_push($items, $item);

        $item = new Item();
        $item->setPrice(14.0);
        $item->setQuantity(16);
        $item->setDescription("product-b");
        array_push($items, $item);

        $item = new Item();
        $item->setPrice(3.90);
        $item->setQuantity(42);
        $item->setDescription("product-c");
        array_push($items, $item);

        $item = new Item();
        $item->setPrice(6.99);
        $item->setQuantity(12);
        $item->setDescription("product-d");
        array_push($items, $item);

        $bill = new Bill("1003", Currency::EUR, "", $items);
        $bill->setEmail("sandbox@bitpay.com");
        $basicBill = null;
        $retrievedBill = null;
        try {
            $basicBill = $this->_client->createBill($bill);
            $retrievedBill = $this->_client->getBill($basicBill->getId());
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertEquals($basicBill->getId(), $retrievedBill->getId());
        $this->assertEquals($basicBill->getItems(), $retrievedBill->getItems());
    }

    public function testShouldDeliverBill()
    {
        $items = [];

        $item = new Item();
        $item->setPrice(30.0);
        $item->setQuantity(9);
        $item->setDescription("product-a");
        array_push($items, $item);

        $item = new Item();
        $item->setPrice(14.0);
        $item->setQuantity(16);
        $item->setDescription("product-b");
        array_push($items, $item);

        $item = new Item();
        $item->setPrice(3.90);
        $item->setQuantity(42);
        $item->setDescription("product-c");
        array_push($items, $item);

        $item = new Item();
        $item->setPrice(6.99);
        $item->setQuantity(12);
        $item->setDescription("product-d");
        array_push($items, $item);

        $bill = new Bill("1005", Currency::EUR, "", $items);
        $bill->setEmail("sandbox@bitpay.com");
        $basicBill = null;
        $retrievedBill = null;
        $result = null;
        try {
            $basicBill = $this->_client->createBill($bill);
            $result = $this->_client->deliverBill($basicBill->getId(), $basicBill->getToken());
            $retrievedBill = $this->_client->getBill($basicBill->getId());
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertEquals($basicBill->getId(), $retrievedBill->getId());
        $this->assertEquals($basicBill->getItems(), $retrievedBill->getItems());
        $this->assertEquals("Success", $result);
        $this->assertNotEquals($basicBill->getStatus(), $retrievedBill->getStatus());
        $this->assertEquals($retrievedBill->getStatus(), BillStatus::Sent);
    }

    public function testShouldGetExchangeRates()
    {
        $ratesList = null;
        try {
            $rates = $this->_client->getRates();
            $ratesList = $rates->getRates();
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertNotNull($ratesList);
    }

    public function testShouldGetEURExchangeRate()
    {
        $rate = null;
        try {
            $rates = $this->_client->getRates();
            $rate = $rates->getRate(Currency::EUR);
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertNotEquals(0, $rate);
    }

    public function testShouldGetCNYExchangeRate()
    {
        $rate = null;
        try {
            $rates = $this->_client->getRates();
            $rate = $rates->getRate(Currency::CNY);
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertNotEquals(0, $rate);
    }

    public function testShouldUpdateExchangeRates()
    {
        $rates = null;
        $ratesList = null;
        try {
            $rates = $this->_client->getRates();
            $rates->update();
            $ratesList = $rates->getRates();
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertNotNull($ratesList);
    }

    public function testShouldGetETHExchangeRates()
    {
        $ratesList = null;
        try {
            $rates = $this->_client->getCurrencyRates(Currency::ETH);
            $ratesList = $rates->getRates();
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertNotNull($ratesList);
    }

    public function testShouldGetETHToUSDExchangeRate()
    {
        $rate = null;
        try {
            $rate = $this->_client->getCurrencyPairRate(Currency::ETH, Currency::USD);
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertNotNull($rate);
    }

    public function testShouldGetCurrencies()
    {
        $currencyList = null;
        try {
            $currencyList = $this->_client->getCurrencies();
        } catch (\Exception $e) {
            $e->getTraceAsString();
            self::fail($e->getMessage());
        }

        $this->assertNotNull($currencyList);
    }
}
