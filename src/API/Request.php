<?php
namespace Sharnw\AvoRedMerchantWarrior\API;

use Sharnw\AvoRedMerchantWarrior\API\Exceptions\ApiException;
use Sharnw\AvoRedMerchantWarrior\API\Exceptions\InvalidConfigException;
use Sharnw\AvoRedMerchantWarrior\API\Exceptions\GenericApiException;
use Sharnw\AvoRedMerchantWarrior\API\Exceptions\BankErrorException;
use Sharnw\AvoRedMerchantWarrior\API\Exceptions\BankDeclinedException;
use Log;

class Request
{

    /**
     * @var string
     */
    private $apiEndpoint;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiSecret;

    /**
     * @var string
     */
    private $merchantUUID;

    /**
     * ApiRequest constructor.
     *
     * @param Array $config
     */
    public function __construct(Array $config)
    {
        if (empty($config['api_endpoint']) || empty($config['api_key'])
            || empty($config['api_secret'])  || empty($config['merchant_uuid'])) {
            throw new InvalidConfigException();
        }

    	$this->apiEndpoint = $config['api_endpoint'];
        $this->apiKey = $config['api_key'];
        $this->apiSecret = $config['api_secret'];
        $this->merchantUUID = $config['merchant_uuid'];
    }

    /**
     * Attempts a post request to merchant warrior API.
     *
     * Parses XML response into a simple result array.
     *
     * @param array $postData Transaction data to be processed.
     *
     * @return Array The result array.
     *
     * @throws CurlException
     */
    public function postRequest($postData = [])
    {
        // Setup CURL defaults
        $curl = curl_init();

        // Setup CURL params for this request
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $this->apiEndpoint);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData, '', '&'));

        // Run CURL
        $response = curl_exec($curl);
        $error = curl_error($curl);

        // Check for CURL errors
        if (isset($error) && strlen($error)) {
            Log::error([
                "Error posting Merchant Warrior request",
                "CURL Error: {$error}",
            ]);

            throw new GenericApiException();
        }

        return new Response($response, $postData, true);
    }

    /**
     * Attempt a 'processCard' request.
     *
     * @param string $totalAmount  2-digit decimal formatted transaction amount string.
     * @param string $currencyCode 3-digit ISO 4217 currency code
     * @param Array  $customer     Array of customer information fields.
     * @param Array  $card         Array of card details fields.
     *
     * @return Response $response
     */
    public function processCard($totalAmount, $currencyCode, Array $customer, Array $card) {
        $hash = md5($this->apiSecret) . $this->merchantUUID . $totalAmount . $currencyCode;
        $hash = md5(strtolower($hash));

        $postData = array_merge([
          'method' => 'processCard',
          'merchantUUID' => $this->merchantUUID,
          'apiKey' => $this->apiKey,
          'transactionAmount' => $totalAmount,
          'transactionCurrency' => $currencyCode,
          'transactionProduct' => 'Cart Checkout',
          'hash' => $hash
        ], $customer, $card);

        $response = $this->postRequest($postData);

        if ($response->getSuccess()) {
            return $response;
        } else {
            Log::error([
                "Error processing Merchant Warrior card payment",
                $response->getMessage(),
            ]);

            switch ($response->getCode()) {
                case 2:
                case 4:
                case 5:
                    throw new ApiException($response->getMessage());
                case -1:
                    throw new InvalidConfigException();
                case 6:
                case 7:
                    throw new BankErrorException();
                case 2:
                case 8:
                case 9:
                    throw new BankDeclinedException();
                case -2:
                case -3:
                case -4:
                case 3:
                default:
                    throw new GenericApiException();
            }
        }
    }

    public function queryCard($transactionID, $transactionRef) {
    }

}
    