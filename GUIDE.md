# BitPay PHP light client

This SDK provides a convenient abstraction of BitPay's [cryptographically-secure API](https://bitpay.com/api) and allows payment gateway developers to focus on payment flow/e-commerce integration rather than on the specific details of client-server interaction using the API.  This SDK optionally provides the flexibility for developers to have control over important details, including the handling of private tokens needed for client-server communication.

- [Dependencies](GUIDE.md#dependencies)
- [Usage](GUIDE.md#usage)
  - [Getting your client token](GUIDE.md#getting-your-client-token)
  - [Installation](GUIDE.md#installation)
  - - [Composer](GUIDE.md#composer)
  - - - [Install composer](GUIDE.md#install-composer)
  - - - [Install via composer by hand](GUIDE.md#install-via-composer-by-hand)
  - - - [Install using composer](GUIDE.md#install-using-composer)
  - [Getting Started](GUIDE.md#getting-started)
  - - [Initializing your BitPay light client](GUIDE.md#initializing-your-bitPay-light-client)
  - - [Create an invoice](GUIDE.md#create-an-invoice)
  - - [Retrieve an invoice](GUIDE.md#retrieve-an-invoice)
  - - [Create bill](GUIDE.md#create-bill)
  - - [Get bill](GUIDE.md#get-bill)
  - - [Deliver bill](GUIDE.md#deliver-bill)
  - - [Get exchange rates](GUIDE.md#get-exchange-rates)
  - - [Get Currencies](GUIDE.md#get-currencies)
- [Copyright](GUIDE.md#copyright)

# Dependencies

You must have a BitPay merchant account to use this SDK.  It's free to [sign-up for a BitPay merchant account](https://bitpay.com/start).

If you need a test account, please visit https://test.bitpay.com/dashboard/signup and register for a BitPay merchant test account. Please fill in all questions, so you get a fully working test account.
If you are looking for a testnet bitcoin wallet to test with, please visit https://bitpay.com/wallet and
create a new wallet.
If you need testnet bitcoin please visit a testnet faucet, e.g. https://testnet.coinfaucet.eu/en/ or http://tpfaucet.appspot.com/

For more information about testing, please see https://bitpay.com/docs/testing

# Usage

This library was built and tested using the PhpStorm IDE; the source code tree is directly compatible with Other PHP IDEs.
Library dependencies can be downloaded by executing the following command at the root of the library:
```bash
php composer.phar install
```

## Getting your client token

First of all, you need to generate a new POS token on your BitPay's account which will be required to securely connect to the BitPay's API.  
For testing purposes use:  
https://test.bitpay.com/dashboard/merchant/api-tokens

For production use:  
https://bitpay.com/dashboard/merchant/api-tokens

Click on 'Add New Token', give a name on the Token Label input, leave the 'Require Authentication' checkbox unchecked and click on 'Add Token'.
The new token will appear and ready to use.

## Installation

### Composer

### Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
```

### Install via composer by hand

Add to your composer.json file by hand.

```bash
{
    ...
    "require": {
        ...
        "bitpay/sdk-light": "^2.0"
    }
    ...
}
```

Once you have added this, just run:

```bash
php composer.phar update bitpay/sdk-light
```

### Install using composer

```bash
php composer.phar require bitpay/sdk-light:^2.0
```

## Getting Started

### Initializing your BitPay light client

Once you have the environment file (JSON or YML previously generated) you can initialize the client on two different ways:

```php
// Provide the full path to the env file which you have previously stored securely.

$bitpay = BitPaySDKLight\Client::create()->withFile([FULL_PATH_TO_THE_CONFIG_FILE]);
```

```php
$bitpay = new BitPaySDKLight\Client("CFJCZH3VitcEER9Uybx8LMvkPsSWzpSWvN4vhNEJp47b", BitPaySDKLight\Env::Test);
```

### Create an invoice

Invoices are time-sensitive payment requests addressed to specific buyers. An invoice has a fixed price, typically denominated in fiat currency. It also has an equivalent price in the supported cryptocurrencies, calculated by BitPay, at a locked exchange rate with an expiration time of 15 minutes.

`POST /invoices`

Facade `POS`

#### HTTP Request

Headers

| Fields | Description | Presence |
| --- | --- | :---: |
| `X-Accept-Version` | Must be set to `2.0.0` for requests to the BitPay API. | Mandatory |
| `Content-Type` | must be set to `application/json` for requests to the BitPay API. | Mandatory |

Body

| Name | Description | Type | Presence |
| --- | --- | :---: | :---: |
| `token` | The API token can be retrieved from the dashboard (limited to `pos` facade) or using the Tokens resource to get access to the `merchant` facade. This is described in the section [Request an API token](GUIDE.md#getting-your-client-token)) | `string` | Mandatory |
| `price` | Fixed price amount for the checkout, in the "currency" of the invoice object | `string` | Mandatory |
| `currency` | ISO 4217 3-character currency code. This is the currency associated with the price field, supported currencies are available via the Currencies resource | `string` | Mandatory |
| `orderId` | Can be used by the merchant to assign their own internal Id to an invoice. If used, there should be a direct match between an orderId and an invoice id | `string` | Optional |
| `itemDesc` | Invoice description - will be added as a line item on the BitPay checkout page, under the merchant name | `string` | Optional |
| `itemCode` | "bitcoindonation" for donations, otherwise do not include the field in the request. | `string` | Optional |
| `notificationEmail` | Merchant email address for notification of invoice status change. It is also possible to configure this email via the account setting on the BitPay dashboard or disable the email notification | `string` | Optional |
| `notificationURL` | URL to which BitPay sends webhook notifications. HTTPS is mandatory. | `string` | Optional |
| `redirectURL` | The shopper will be redirected to this URL when clicking on the Return button after a successful payment or when clicking on the Close button if a separate `closeURL` is not specified. Be sure to include "http://" or "https://" in the url. | `string` | Optional |
| `closeURL` | URL to redirect if the shopper does not pay the invoice and click on the Close button instead. Be sure to include "http://" or "https://" in the url.
 | `string` | Optional |
| `autoRedirect` | Set to `false` by default, merchant can setup automatic redirect to their website by setting this parameter to `true`. This will applied to the following scenarios: When the invoice is paid, it automatically redirects the shopper to the `redirectURL` indicated When the invoice expires, it automatically redirects the shopper to the `closeURL` if specified and to the `redirectURL` otherwise Note: If automatic redirect is enabled, `redirectURL` becomes a mandatory invoice parameters. | `boolean` | Optional |
| `posData` | A passthru variable provided by the merchant during invoice creation and designed to be used by the merchant to correlate the invoice with an order or other object in their system. This passthru variable can be a serialized object, e.g.: `"posData": "\"{ \"ref\" : 711454, \"item\" : \"test_item\" }\""`. | `string` | Optional |
| `transactionSpeed` | This is a risk mitigation parameter for the merchant to configure how they want to fulfill orders depending on the number of block confirmations for the transaction made by the consumer on the selected cryptocurrency. high: The invoice is marked as "confirmed" by BitPay as soon as full payment is received but not yet validated on the corresponding blockchain. The invoice will go from a status of "new" to "confirmed", bypassing the "paid" status. If you want an immediate notification for a payment, you can use the high speed setting. However, it makes you more susceptible to receiving fraudulent payments, so it is not recommended. medium: (Recommended for most merchants) The invoice is marked as "confirmed" after the transaction has received basic confirmation on the corresponding blockchain. For invoices paid in Bitcoin (BTC), this means 1 confirmation on the blockchain which takes on average 10 minutes. The invoice will go from a status of "new" to "paid" followed by "confirmed" and then "complete" low: The invoice is marked as "confirmed" once BitPay has credited the funds to the merchant account. The invoice will go from a status of "new" to "paid" followed by "complete", thus bypassing the "confirmed" status. For invoices paid in Bitcoin (BTC), this means 6 confirmations on the blockchain which takes on average an hour If not set on the invoice, transactionSpeed will default to the account-level Order Settings. Note : orders are only credited to your BitPay Account Summary for settlement after the invoice reaches the status "complete" (regardless of this setting). | `string` | Optional |
| `fullNotifications` | This parameter is set to true by default, meaning all standard notifications are being sent for a payment made to an invoice. If you decide to set it to `false` instead, only 1 webhook will be sent for each invoice paid by the consumer. This webhook will be for the "confirmed" or "complete" invoice status, depending on the `transactionSpeed` selected. | `boolean` | Optional |
| `extendedNotifications` | Allows merchants to get access to additional webhooks. For instance when an invoice expires without receiving a payment or when it is refunded. If set to `true`, then `fullNotifications` is automatically set to `true`. When using the `extendedNotifications` parameter, the webhook also have a payload slightly different from the standard webhooks. | `boolean` | Optional |
| `physical` | Indicates whether items are physical goods. Alternatives include digital goods and services. | `boolean` | Optional |
| `buyer` | Allows merchant to pass buyer related information in the invoice object | `object` | Optional |
| &rarr; `name` | Buyer's name | `string` | Optional |
| &rarr; `address1` | Buyer's address | `string` | Optional |
| &rarr; `address2` | Buyer's appartment or suite number | `string` | Optional |
| &rarr; `locality` | Buyer's city or locality | `string` | Optional |
| &rarr; `region` | Buyer's state or province | `string` | Optional |
| &rarr; `postalCode` | Buyer's Zip or Postal Code | `string` | Optional |
| &rarr; `country` | Buyer's Country code. Format ISO 3166-1 alpha-2 | `string` | Optional |
| &rarr; `email` | Buyer's email address. If provided during invoice creation, this will bypass the email prompt for the consumer when opening the invoice. | `string` | Optional |
| &rarr; `phone` | Buyer's phone number | `string` | Optional |
| &rarr; `notify` | Indicates whether a BitPay email confirmation should be sent to the buyer once he has paid the invoice | `boolean` | Optional |
| `paymentCurrencies` | Allow the merchant to select the cryptocurrencies available as payment option on the BitPay invoice. Possible values are currently `"BTC"`, `"BCH"`, `"ETH"`, `"GUSD"`, `"PAX"`, `"BUSD"`, `"USDC"`, `"XRP"`, `"DOGE"`, `"DAI"` and `"WBTC"`. For instance `"paymentCurrencies": ["BTC"]` will create an invoice with only XRP available as transaction currency, thus bypassing the currency selection step on the invoice. | `array` | Optional |
| `jsonPayProRequired` | If set to `true`, this means that the invoice will only accept payments from wallets which have implemented the [BitPay JSON Payment Protocol](https://bitpay.com/docs/payment-protocol) | `boolean` | Optional |

An example code of the create invoice

```php
$invoice = new Invoice(50.0, "USD");
$invoice->setToken($bitpay->_token);
$invoice->setOrderId("65f5090680f6");
$invoice->setFullNotifications(true);
$invoice->setExtendedNotifications(true);
$invoice->setNotificationURL("https://hookbin.com/lJnJg9WW7MtG9GZlPVdj");
$invoice->setRedirectURL("https://hookbin.com/lJnJg9WW7MtG9GZlPVdj");
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
$buyer->setPhone("+990123456789");
$buyer->setPostalCode("KY7 1TH");
$buyer->setRegion("New Port");

$invoice->setBuyer($buyer);

$basicInvoice = $bitpay->createInvoice($invoice);
```

HTTP Response

```json
{
    "facade": "pos/invoice",
    "data": {
        "url": "https://bitpay.com/invoice?id=KSnNNfoMDsbRzd1U9ypmVH",
        "status": "new",
        "price": 10,
        "currency": "USD",
        "orderId": "20210511_abcde",
        "invoiceTime": 1620734545366,
        "expirationTime": 1620735445366,
        "currentTime": 1620734545415,
        "id": "KSnNNfoMDsbRzd1U9ypmVH",
        "lowFeeDetected": false,
        "amountPaid": 0,
        "displayAmountPaid": "0",
        "exceptionStatus": false,
        "redirectURL": "https://merchantwebsite.com/shop/return",
        "refundAddressRequestPending": false,
        "buyerProvidedInfo": {},
        "paymentSubtotals": {
            "BTC": 18200,
            "BCH": 744500,
            "ETH": 2535000000000000,
            "GUSD": 1000,
            "PAX": 10000000000000000000,
            "BUSD": 10000000000000000000,
            "USDC": 10000000,
            "XRP": 7084249,
            "DOGE": 2068707100,
            "DAI": 9990000000000000000,
            "WBTC": 18100
        },
        "paymentTotals": {
            "BTC": 29800,
            "BCH": 744500,
            "ETH": 2535000000000000,
            "GUSD": 1000,
            "PAX": 10000000000000000000,
            "BUSD": 10000000000000000000,
            "USDC": 10000000,
            "XRP": 7084249,
            "DOGE": 2068707100,
            "DAI": 9990000000000000000,
            "WBTC": 18100
        },
        "paymentDisplayTotals": {
            "BTC": "0.000298",
            "BCH": "0.007445",
            "ETH": "0.002535",
            "GUSD": "10.00",
            "PAX": "10.00",
            "BUSD": "10.00",
            "USDC": "10.00",
            "XRP": "7.084249",
            "DOGE": "20.687071",
            "DAI": "9.99",
            "WBTC": "0.000181"
        },
        "paymentDisplaySubTotals": {
            "BTC": "0.000182",
            "BCH": "0.007445",
            "ETH": "0.002535",
            "GUSD": "10.00",
            "PAX": "10.00",
            "BUSD": "10.00",
            "USDC": "10.00",
            "XRP": "7.084249",
            "DOGE": "20.687071",
            "DAI": "9.99",
            "WBTC": "0.000181"
        },
        "exchangeRates": {
            "BTC": {
            "USD": 55072.459995,
            "EUR": 45287.42496000001,
            "BCH": 40.884360403999914,
            "ETH": 13.953840617367156,
            "GUSD": 55072.459995,
            "PAX": 55072.459995,
            "BUSD": 55072.459995,
            "USDC": 55072.459995,
            "XRP": 38907.54307403195,
            "DOGE": 113694.39064944115,
            "DAI": 55018.486859390934,
            "WBTC": 0.9983514430763876
            },
            "BCH": {
            "USD": 1343.1537000000003,
            "EUR": 1104.481875,
            "BTC": 0.02437664632426631,
            "ETH": 0.34031805835672807,
            "GUSD": 1343.1537000000003,
            "PAX": 1343.1537000000003,
            "BUSD": 1343.1537000000003,
            "USDC": 1343.1537000000003,
            "XRP": 948.9100440136494,
            "DOGE": 2772.8748903518513,
            "DAI": 1341.8373575522414,
            "WBTC": 0.024348638771359274
            },
            "ETH": {
            "USD": 3944.6466899999996,
            "EUR": 3242.8077850000004,
            "BTC": 0.07159065804331831,
            "BCH": 2.9284029977060646,
            "GUSD": 3944.6466899999996,
            "PAX": 3944.6466899999996,
            "BUSD": 3944.6466899999996,
            "USDC": 3944.6466899999996,
            "XRP": 2786.8105223000134,
            "DOGE": 8143.529484384802,
            "DAI": 3940.7807840508463,
            "WBTC": 0.07150840394174397
            },
            ...
        },
        "supportedTransactionCurrencies": {
            "BTC": {
            "enabled": true
            },
            "BCH": {
            "enabled": true
            },
            "ETH": {
            "enabled": true
            },
            ...
        },
        "minerFees": {
            "BTC": {
            "satoshisPerByte": 79.152,
            "totalFee": 11600
            },
            "BCH": {
            "satoshisPerByte": 0,
            "totalFee": 0
            },
            "ETH": {
            "satoshisPerByte": 0,
            "totalFee": 0
            },
            ...
        },
        "jsonPayProRequired": false,
        "paymentCodes": {
            "BTC": {
            "BIP72b": "bitcoin:?r=https://bitpay.com/i/KSnNNfoMDsbRzd1U9ypmVH",
            "BIP73": "https://bitpay.com/i/KSnNNfoMDsbRzd1U9ypmVH"
            },
            "BCH": {
            "BIP72b": "bitcoincash:?r=https://bitpay.com/i/KSnNNfoMDsbRzd1U9ypmVH",
            "BIP73": "https://bitpay.com/i/KSnNNfoMDsbRzd1U9ypmVH"
            },
            "ETH": {
            "EIP681": "ethereum:?r=https://bitpay.com/i/KSnNNfoMDsbRzd1U9ypmVH"
            },
            ...
        },
        "token": "8nPJSGgi7omxcbGGZ4KsSgqdi6juypBe9pVpSURDeAwx4VDQx1XfWPy5qqknDKT9KQ"
    }
}
```

To get the generated invoice url and status

```php
$invoiceUrl = $basicInvoice->getURL();

$status = $basicInvoice->getStatus();
```

> **WARNING**: 
If you get the following error when initiating the client for first time:
"500 Internal Server Error` response: {"error":"Account not setup completely yet."}"
Please, go back to your BitPay account and complete the required steps.
More info [here](https://support.bitpay.com/hc/en-us/articles/203010446-How-do-I-apply-for-a-merchant-account-)

### Retrieve an invoice

`GET /invoices/:invoiceid`

### Facade `POS`

### HTTP Request

URL Parameters

| Parameter | Description | Type | Presence |
| --- | --- | :---: | :---: |
| `?token=` | When fetching an invoice the `pos` facade, pass the API token as a URL parameter - the same token used to create the invoice in the first place. | `string` | Mandatory |

Headers

| Fields | Description | Presence |
| --- | --- | :---: |
| `X-Accept-Version` | Must be set to `2.0.0` for requests to the BitPay API. | Mandatory |
| `Content-Type` | must be set to `application/json` for requests to the BitPay API. | Mandatory |


To get the generated invoice details, pass the Invoice Id with URL parameter

```php
$invoice = $bitpay->getInvoice($basicInvoice->getId());
```


### Create bill

Bills are payment requests addressed to specific buyers. Bill line items have fixed prices, typically denominated in fiat currency.

`POST /bills`

### Facade `POS`

### HTTP Request

Headers

| Fields | Description | Presence |
| --- | --- | :---: |
| `X-Accept-Version` | Must be set to `2.0.0` for requests to the BitPay API. | Mandatory |
| `Content-Type` | must be set to `application/json` for requests to the BitPay API. | Mandatory |

Body

| Name | Description | Type | Presence |
| --- | --- | :---: | :---: |
| `number` | Bill identifier, specified by merchant | `string` | Optional |
| `currency` | ISO 4217 3-character currency code. This is the currency associated with the price field | `string` | Mandatory |
| `name` | Bill recipient's name | `string` | Optional |
| `address1` | Bill recipient's address | `string` | Optional |
| `address2` | Bill recipient's address | `string` | Optional |
| `city` | Bill recipient's city | `string` | Optional |
| `state` | Bill recipient's state or province | `string` | Optional |
| `zip` | Bill recipient's ZIP code | `string` | Optional |
| `country` | Bill recipient's country | `string` | Optional |
| `email` | Bill recipient's email address | `string` | Mandatory |
| `cc` | Email addresses to which a copy of the bill must be sent | `array` | Optional |
| `phone` | Bill recipient's phone number | `string` | Optional |
| `dueDate` | Date and time at which a bill is due, ISO-8601 format yyyy-mm-ddThh:mm:ssZ. (UTC) | `string` | Optional |
| `passProcessingFee` | If set to `true`, BitPay's processing fee will be included in the amount charged on the invoice | `boolean` | Optional |
| `items` | List of line items | `string` | Mandatory |
| &rarr; `description` | Line item description | `string` | Mandatory |
| &rarr; `price` | Line item unit price for the corresponding `currency` | `number` | Mandatory |
| &rarr; `quantity` | Bill identifier, specified by merchant | `number` | Mandatory |
| `token` | The API token can be retrieved from the dashboard (limited to pos facade) | `string` | Mandatory |

```php
$items = [];

$item = new Item();
$item->setPrice(6.0);
$item->setQuantity(1);
$item->setDescription("Test Item 1");
array_push($items, $item);

$item = new Item();
$item->setPrice(4.0);
$item->setQuantity(1);
$item->setDescription("Test Item 2");
array_push($items, $item);

$bill = new Bill("bill1234-ABCD", Currency::USD, "", $items);
$bill->setEmail("john@doe.com");

$basicBill = $this->_client->createBill($bill);
```

### HTTP Response

```json
{
    "facade": "pos/bill",
    "data": {
        "status": "draft",
        "url": "https://bitpay.com/bill?id=X6KJbe9RxAGWNReCwd1xRw&resource=bills",
        "number": "bill1234-ABCD",
        "createdDate": "2021-05-21T09:48:02.373Z",
        "dueDate": "2021-05-31T00:00:00.000Z",
        "currency": "USD",
        "email": "john@doe.com",
        "cc": [
        "jane@doe.com"
        ],
        "passProcessingFee": true,
        "id": "X6KJbe9RxAGWNReCwd1xRw",
        "items": [
        {
            "id": "EL4vx41Nxc5RYhbqDthjE",
            "description": "Test Item 1",
            "price": 6,
            "quantity": 1
        },
        {
            "id": "6spPADZ2h6MfADvnhfsuBt",
            "description": "Test Item 2",
            "price": 4,
            "quantity": 1
        }
        ],
        "token": "qVVgRARN6fKtNZ7Tcq6qpoPBBE3NxdrmdMD883RyMK4Pf8EHENKVxCXhRwyynWveo"
    }
}
```


### Get bill

`GET /bills/:billid`

### Facade `POS`

### HTTP Request

URL Parameters

| Parameter | Description | Type | Presence |
| --- | --- | :---: | :---: |
| `?token=` | when fetching settlememts, pass a merchant facade token as a URL parameter. | `string` | Mandatory |

Headers

| Fields | Description | Presence |
| --- | --- | :---: |
| `X-Accept-Version` | Must be set to `2.0.0` for requests to the BitPay API. | Mandatory |
| `Content-Type` | must be set to `application/json` for requests to the BitPay API. | Mandatory |

```php
$retrievedBill = $bitpay->getBill($bill->getId());
```
### HTTP Response

```json
{
    "facade": "pos/bill",
    "data": {
        "status": "draft",
        "url": "https://bitpay.com/bill?id=X6KJbe9RxAGWNReCwd1xRw&resource=bills",
        "number": "bill1234-ABCD",
        "createdDate": "2021-05-21T09:48:02.373Z",
        "dueDate": "2021-05-31T00:00:00.000Z",
        "currency": "USD",
        "email": "john@doe.com",
        "cc": [
        "jane@doe.com"
        ],
        "passProcessingFee": true,
        "id": "X6KJbe9RxAGWNReCwd1xRw",
        "items": [
        {
            "id": "EL4vx41Nxc5RYhbqDthjE",
            "description": "Test Item 1",
            "price": 6,
            "quantity": 1
        },
        {
            "id": "6spPADZ2h6MfADvnhfsuBt",
            "description": "Test Item 2",
            "price": 4,
            "quantity": 1
        }
        ],
        "token": "qVVgRARN6fKtNZ7Tcq6qpoPBBE3NxdrmdMD883RyMK4Pf8EHENKVxCXhRwyynWveo"
    }
}
```


### Deliver bill

`GET /bills/:billid/deliveries`

### Facade `POS`

### HTTP Request

URL Parameters

| Parameter | Description | Type | Presence |
| --- | --- | :---: | :---: |
| `billId` | the id of the bill you want to deliver via email. | `string` | Mandatory |

Headers

| Fields | Description | Presence |
| --- | --- | :---: |
| `X-Accept-Version` | Must be set to `2.0.0` for requests to the BitPay API. | Mandatory |
| `Content-Type` | must be set to `application/json` for requests to the BitPay API. | Mandatory |

Body

| Name | Description | Type | Presence |
| --- | --- | :---: | :---: |
| `token` | The resource token for the billId you want to deliver via email. You need to retrieve this token from the bill object itself. | `string` | Mandatory |

```php
$deliveredBill = $bitpay->deliverBill($bill->getId());
```
HTTP Response

Body

| Name | Description | Type |
| --- | --- | :---: |
| `data` | set to `"Success"` once a bill is successfully sent via email. | `string` |

```json
{
    "data": "Success"
}
```

### Get exchange Rates

Rates are exchange rates, representing the number of fiat currency units equivalent to one BTC. You can retrieve BitPay's [BBB exchange rates](https://bitpay.com/exchange-rates).

`GET /rates/:basecurrency`

### Facade `PUBLIC`

### HTTP Request

URL Parameters

| Parameter | Description | Type | Presence |
| --- | --- | :---: | :---: |
| `baseCurrency` | the cryptocurrency for which you want to fetch the rates. Current supported values are BTC and BCH. | `string` | Mandatory |

Headers

| Fields | Description | Presence |
| --- | --- | :---: |
| `X-Accept-Version` | Must be set to `2.0.0` for requests to the BitPay API. | Mandatory |
| `Content-Type` | must be set to `application/json` for requests to the BitPay API. | Mandatory |

```php
$rates = $bitpay->getRates();

$rate = $rates->getRate(Currency::USD); //Always use the included Currency model to avoid typos

$rates->update();
```
HTTP Response

Body

| Name | Description | Type |
| --- | --- | :---: |
| `data` | array of currency rates for the requested `baseCurrency`. | `array` |
| &rarr; `code` | ISO 4217 3-character currency code. | `string` |
| &rarr; `name` | detailed currency name. | `string` |
| &rarr; `rate` | rate for the requested `baseCurrency` /`currency` pair. | `number` |

```json
{
    "data":[
        {
        "code":"BTC",
        "name":"Bitcoin",
        "rate":1
        },
        {
        "code":"BCH",
        "name":"Bitcoin Cash",
        "rate":50.77
        },
        {
        "code":"USD",
        "name":"US Dollar",
        "rate":41248.11
        },
        {
        "code":"EUR",
        "name":"Eurozone Euro",
        "rate":33823.04
        },
        {
        "code":"GBP",
        "name":"Pound Sterling",
        "rate":29011.49
        },
        {
        "code":"JPY",
        "name":"Japanese Yen",
        "rate":4482741
        },
        {
        "code":"CAD",
        "name":"Canadian Dollar",
        "rate":49670.85
        },
        {
        "code":"AUD",
        "name":"Australian Dollar",
        "rate":53031.99
        },
        {
        "code":"CNY",
        "name":"Chinese Yuan",
        "rate":265266.57
        },
        ...
    ]
}
```

You can retrieve all the rates for a given cryptocurrency

URL Parameters

| Parameter | Description | Type | Presence |
| --- | --- | :---: | :---: |
| `baseCurrency` | the cryptocurrency for which you want to fetch the rates. Current supported values are BTC and BCH. | `string` | Mandatory |
| `currency` | the fiat currency for which you want to fetch the `baseCurrency` rates. | `string` | Mandatory |

Headers

| Fields | Description | Presence |
| --- | --- | :---: |
| `X-Accept-Version` | Must be set to `2.0.0` for requests to the BitPay API. | Mandatory |
| `Content-Type` | must be set to `application/json` for requests to the BitPay API. | Mandatory |

```php
$rates = $bitpay->getCurrencyRates(Currency::ETH);

$rate = $rates->getRate(Currency::USD);
```

You can retrieve the rate for a cryptocurrency / fiat pair

```php
$rate = $bitpay->getCurrencyPairRate(Currency::BTC, Currency::USD);
```

HTTP Response

Body

| Name | Description | Type |
| --- | --- | :---: |
| `data` | rate data object. | `object` |
| &rarr; `code` | ISO 4217 3-character currency code. | `string` |
| &rarr; `name` | detailed currency name. | `string` |
| &rarr; `rate` | rate for the requested `baseCurrency` /`currency` pair. | `number` |

```json
{
    "data":
        {
            "code":"USD",
            "name":"US Dollar",
            "rate":41154.05
        }
}
```



See also the test package for more examples of API calls.

### Get Currencies

`GET /currencies`

### Facade `PUBLIC`

### HTTP Request

Headers

| Fields | Description | Presence |
| --- | --- | :---: |
| `X-Accept-Version` | Must be set to `2.0.0` for requests to the BitPay API. | Mandatory |
| `Content-Type` | must be set to `application/json` for requests to the BitPay API. | Mandatory |

You can retrieve all the currencies supported by BitPay.

```php
$currencies = $bitpay->getCurrencies();
```
HTTP Response

Body

| Name | Description | Type |
| --- | --- | :---: |
| `data` | Array of supported currencies | `array` |
| &rarr; `code` | ISO 4217 3-character currency code | `string` |
| &rarr; `symbol` | Display symbol | `string` |
| &rarr; `precision` | Number of decimal places | `number` |
| &rarr; `name` | English currency name | `string` |
| &rarr; `plural` | English plural form | `string` |
| &rarr; `alts` | Alternative currency name(s) | `string` |
| &rarr; `minimum` | Minimum supported value when creating an invoice, bill or payout for instance | `string` |
| &rarr; `sanctionned` | If the currency is linked to a sanctionned country | `boolean` |
| &rarr; `decimals` | decimal precision | `number` |
| &rarr; `chain` | For cryptocurrencies or tokens, the corresponding chain is also specified. For instance, with `USDC` (Circle USD Coin), the `chain` is `ETH`. | `string` |

```json
{
    "data": [
        {
        "code": "BTC",
        "symbol": "฿",
        "precision": 6,
        "name": "Bitcoin",
        "plural": "Bitcoin",
        "alts": "btc",
        "minimum": 0.000006,
        "sanctioned": false,
        "decimals": 8,
        "chain": "BTC"
        },
        ...
        ...
        {
        "code": "XRP",
        "symbol": "Ʀ",
        "precision": 6,
        "name": "Ripple",
        "plural": "Ripple",
        "alts": "xrp",
        "minimum": 0.000006,
        "sanctioned": false,
        "decimals": 6,
        "chain": "XRP"
        },
        ...
        ...
        {
        "code": "EUR",
        "symbol": "€",
        "precision": 2,
        "name": "Eurozone Euro",
        "plural": "Eurozone Euros",
        "alts": "eur",
        "minimum": 0.01,
        "sanctioned": false,
        "decimals": 2
        },
        ...
        ...
        {
        "code": "USD",
        "symbol": "$",
        "precision": 2,
        "name": "US Dollar",
        "plural": "US Dollars",
        "alts": "usd bucks",
        "minimum": 0.01,
        "sanctioned": false,
        "decimals": 2
        },
        {
        "code": "USDC",
        "symbol": "$",
        "precision": 2,
        "name": "Circle USD Coin",
        "plural": "Circle USD Coin",
        "alts": "",
        "minimum": 0.01,
        "sanctioned": false,
        "decimals": 6,
        "chain": "ETH"
        },
        ...
        ...
    ]
}
```


# Copyright

Copyright (c) 2019 BitPay