## Using the BitPay PHP light client

This SDK provides a convenient abstraction of BitPay's [cryptographically-secure API](https://bitpay.com/api) and allows payment gateway developers to focus on payment flow/e-commerce integration rather than on the specific details of client-server interaction using the API.  This SDK optionally provides the flexibility for developers to have control over important details, including the handling of private tokens needed for client-server communication.

### Dependencies

You must have a BitPay merchant account to use this SDK.  It's free to [sign-up for a BitPay merchant account](https://bitpay.com/start).

If you need a test account, please visit https://test.bitpay.com/dashboard/signup and register for a BitPay merchant test account. Please fill in all questions, so you get a fully working test account.
If you are looking for a testnet bitcoin wallet to test with, please visit https://bitpay.com/wallet and
create a new wallet.
If you need testnet bitcoin please visit a testnet faucet, e.g. https://testnet.coinfaucet.eu/en/ or http://tpfaucet.appspot.com/

For more information about testing, please see https://bitpay.com/docs/testing

### Usage

This library was built and tested using the PhpStorm IDE; the source code tree is directly compatible with Other PHP IDEs.
Library dependencies can be downloaded by executing the following command at the root of the library:
```bash
php composer.phar install
```

### Getting your client token

First of all, you need to generate a new POS token on your BitPay's account which will be required to securely connect to the BitPay's API.  
For testing purposes use:  
https://test.bitpay.com/dashboard/merchant/api-tokens

For production use:  
https://bitpay.com/dashboard/merchant/api-tokens

Click on 'Add New Token', give a name on the Token Label input, leave the 'Require Authentication' checkbox unchecked and click on 'Add Token'.
The new token will appear and ready to use.

# Installation

## Composer

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
        "bitpay/sdk-light": "~1.0"
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
php composer.phar require bitpay/sdk:~3.0
```

### Initializing your BitPay light client

Once you have the environment file (JSON or YML previously generated) you can initialize the client on two different ways:

```php
// Provide the full path to the env file which you have previously stored securely.

$bitpay = BitPaySDKLight\Client::create()->withFile([FULL_PATH_TO_THE_CONFIG_FILE]);
```

```php
$bitpay = new BitPaySDKLight\Client("CFJCZH3VitcEER9Uybx8LMvkPsSWzpSWvN4vhNEJp47b", BitPaySDKLight\Env::Test);
```
##
### Create an invoice

```php
$invoice = $bitpay->createInvoice(new Invoice(50.0, "USD"));

$invoiceUrl = $invoice->getURL();

$status = $invoice->getStatus();
```

> **WARNING**: 
If you get the following error when initiating the client for first time:
"500 Internal Server Error` response: {"error":"Account not setup completely yet."}"
Please, go back to your BitPay account and complete the required steps.
More info [here](https://support.bitpay.com/hc/en-us/articles/203010446-How-do-I-apply-for-a-merchant-account-)

### Retrieve an invoice

```php
$invoice = $bitpay->getInvoice($invoice->getId());
```

### Get exchange Rates

You can retrieve BitPay's [BBB exchange rates](https://bitpay.com/exchange-rates).

```php
$rates = $bitpay->getRates();

$rate = $rates->getRate(Currency::USD); //Always use the included Currency model to avoid typos

$rates->update();
```

See also the test package for more examples of API calls.
