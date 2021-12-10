<?php


namespace BitPaySDKLight\Util\RESTcli;


use BitPaySDKLight\Env;
use BitPaySDKLight\Exceptions\BitPayException;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response as Response;
use GuzzleHttp\RequestOptions as RequestOptions;

class RESTcli
{
    /**
     * @var GuzzleHttpClient
     */
    protected $_client;
    /**
     * @var string
     */
    protected $_baseUrl;
    
    public function __construct(string $environment)
    {
        $this->_baseUrl = $environment == Env::Test ? Env::TestUrl : Env::ProdUrl;
        $this->init();
    }

    public function init()
    {
        try {
            $this->_client = new GuzzleHttpClient(
                [
                    'base_url' => $this->_baseUrl,
                    'defaults' => [
                        'headers' => [
                            'x-accept-version'           => Env::BitpayApiVersion,
                            'x-bitpay-plugin-info'       => Env::BitpayPluginInfo,
                            'x-bitpay-api-frame'         => Env::BitpayApiFrame,
                            'x-bitpay-api-frame-version' => Env::BitpayApiFrameVersion,
                        ],
                    ],
                ]);
        } catch (Exception $e) {
            throw new BitPayException("RESTcli init failed : ".$e->getMessage());
        }
    }

    public function post($uri, array $formData = []): string
    {
        try {
            $fullURL = $this->_baseUrl.$uri;
            $headers = [
                'Content-Type' => 'application/json',
                'x-accept-version'           => Env::BitpayApiVersion,
                'x-bitpay-plugin-info'       => Env::BitpayPluginInfo,
                'x-bitpay-api-frame'         => Env::BitpayApiFrame,
                'x-bitpay-api-frame-version' => Env::BitpayApiFrameVersion,
            ];

            /**
             * @var Response
             */
            $response = $this->_client->requestAsync(
                'POST', $fullURL, [
                $options[RequestOptions::SYNCHRONOUS] = false,
                'headers'            => $headers,
                RequestOptions::JSON => $formData,
            ])->wait();
            $responseJson = $this->responseToJsonString($response);
            return $responseJson;
        } catch (BadResponseException $e) {
            $errorJson = $this->responseToJsonString($e->getResponse());
            throw new BitPayException("POST failed : Guzzle/BadResponseException : ".$errorJson['message'], $errorJson['code']);
        } catch (Exception $e) {
            throw new BitPayException("POST failed : ".$e->getMessage());
        }
    }

    public function get($uri, array $parameters = null): string
    {
        try {
            $fullURL = $this->_baseUrl.$uri;
            $headers = [
                'Content-Type' => 'application/json',
                'x-accept-version'           => Env::BitpayApiVersion,
                'x-bitpay-plugin-info'       => Env::BitpayPluginInfo,
                'x-bitpay-api-frame'         => Env::BitpayApiFrame,
                'x-bitpay-api-frame-version' => Env::BitpayApiFrameVersion,
            ];

            if ($parameters) {
                $fullURL .= '?'.http_build_query($parameters);
            }

            /**
             * @var Response
             */
            $response = $this->_client->requestAsync(
                'GET', $fullURL, [
                $options[RequestOptions::SYNCHRONOUS] = false,
                'headers' => $headers,
                'query'   => $parameters,
            ])->wait();
            $responseJson = $this->responseToJsonString($response);
            return $responseJson;
        } catch (BadResponseException $e) {
            $errorJson = $this->responseToJsonString($e->getResponse());
            throw new BitPayException("GET failed : Guzzle/BadResponseException : ".$errorJson['message'], $errorJson['code']);
        } catch (Exception $e) {
            throw new BitPayException("GET failed : ".$e->getMessage());
        }
    }

    public function delete($uri, array $parameters = null): string
    {
        try {
            $fullURL = $this->_baseUrl.$uri;
            if ($parameters) {
                $fullURL .= '?'.http_build_query($parameters);
            }

            $headers = [
                'x-accept-version'           => Env::BitpayApiVersion,
                'x-bitpay-plugin-info'       => Env::BitpayPluginInfo,
                'x-bitpay-api-frame'         => Env::BitpayApiFrame,
                'x-bitpay-api-frame-version' => Env::BitpayApiFrameVersion,
                'Content-Type' => 'application/json',
            ];

            /**
             * @var Response
             */
            $response = $this->_client->requestAsync(
                'DELETE', $fullURL, [
                $options[RequestOptions::SYNCHRONOUS] = false,
                'headers' => $headers,
                'query'   => $parameters,
            ])->wait();

            $responseJson = $this->responseToJsonString($response);

            return $responseJson;
        } catch (BadResponseException $e) {
            $errorJson = $this->responseToJsonString($e->getResponse());
            throw new BitPayException("DELETE failed : Guzzle/BadResponseException : ".$errorJson['message'], $errorJson['code']);
        } catch (Exception $e) {
            throw new BitPayException("DELETE failed : ".$e->getMessage());
        }
    }

    public function update($uri, array $formData = []): string
    {
        try {
            $fullURL = $this->_baseUrl.$uri;
            $headers = [
                'x-accept-version'           => Env::BitpayApiVersion,
                'x-bitpay-plugin-info'       => Env::BitpayPluginInfo,
                'x-bitpay-api-frame'         => Env::BitpayApiFrame,
                'x-bitpay-api-frame-version' => Env::BitpayApiFrameVersion,
                'Content-Type' => 'application/json',
            ];

            /**
             * @var Response
             */
            $response = $this->_client->requestAsync(
                'PUT', $fullURL, [
                $options[RequestOptions::SYNCHRONOUS] = false,
                'headers'            => $headers,
                RequestOptions::JSON => $formData,
            ])->wait();

            $responseJson = $this->responseToJsonString($response);

            return $responseJson;
        } catch (BadResponseException $e) {
            $errorJson = $this->responseToJsonString($e->getResponse());
            throw new BitPayException("UPDATE failed : Guzzle/BadResponseException : ".$errorJson['message'], $errorJson['code']);
        } catch (Exception $e) {
            throw new BitPayException("UPDATE failed : ".$e->getMessage());
        }
    }

    public function responseToJsonString(Response $response): string
    {
        if ($response == null) {
            throw new Exception("Error: HTTP response is null");
        }

        try {
            $body = json_decode($response->getBody()->getContents(), true);

            if (!empty($body['status'])) {
                if ($body['status'] == 'error') {
                    throw new BitpayException($body['message'], null, null, $body['code']);
                }
            }

            $error_message = false;
            $error_message = (!empty($body['error'])) ? $body['error'] : $error_message;
            $error_message = (!empty($body['errors'])) ? $body['errors'] : $error_message;
            $error_message = (is_array($error_message)) ? implode("\n", $error_message) : $error_message;
            if (false !== $error_message) {
                throw new BitpayException($error_message);
            }
            if (!empty($body['success'])) {
                return json_encode($body);
            }

            return json_encode($body['data']);

        } catch (BitpayException $e) {
            throw new BitPayException("failed to retrieve HTTP response body : ".$e->getMessage(), null, null, $e->getApiCode());
        } catch (Exception $e) {
            throw new BitPayException("failed to retrieve HTTP response body : ".$e->getMessage());
        }
    }

}
