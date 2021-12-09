<?php

namespace BitPaySDKLight\Exceptions;


class InvoiceQueryException extends InvoiceException
{
    private $bitPayMessage = "Failed to retrieve invoice";
    private $bitPayCode    = "BITPAY-INVOICE-GET";
    protected $apiCode;

    /**
     * Construct the InvoiceQueryException.
     *
     * @param string $message [optional] The Exception message to throw.
     * @param int    $code    [optional] The Exception code to throw.
     * @param string $apiCode [optional] The API Exception code to throw.
     */
    public function __construct($message = "", $code = 103)
    {

        $message = $this->bitPayCode.": ".$this->bitPayMessage."-> ".$message;
        $this->apiCode = $apiCode;
        parent::__construct($message, $code);
    }

    /**
     * @return string Error code provided by the BitPay REST API
     */
    public function getApiCode()
    {
        return $this->apiCode;
    }
}