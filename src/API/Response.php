<?php
namespace Sharnw\AvoRedMerchantWarrior\API;

class Response
{

    /**
     * @var array
     */
    private $params;

    /**
     * @var string
     */
    private $xmlRaw;

    /**
     * @var array
     */
    private $xmlResult;

    public function __construct(string $rawResponse, Array $params = []) {
        // Parse XML & convert result from a SimpleXMLObject into an array
        $this->xmlResult = (array) simplexml_load_string($rawResponse);
        // keep raw XML
        $this->xmlRaw = $rawResponse;
        // keep posted params
        $this->params = $params;
    }

    /**
     * Get the params posted with request.
     *
     * @return array
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * Get the request response raw xml.
     *
     * @return string
     */
    public function getXml() {
        return $this->xmlRaw;
    }

    /**
     * Gets the request response code.
     *
     * @return string
     */
    public function getCode() {
        return $this->xmlResult['responseCode'];
    }

    /**
     * Gets request response result array
     *
     * @return array
     */
    public function getResult() {
        return $this->xmlResult;
    }

    /**
     * Gets request response success status.
     *
     * @return boolean
     */
    public function getSuccess() {
        return ((int) $this->getCode() === 0);
    }

    /**
     * Get the request response message.
     *
     * @return string
     */
    public function getMessage() {
        return $this->xmlResult['responseMessage'];
    }

    /**
     * Attempt to get request response transactionID.
     *
     * @return string|null
     */
    public function getTransactionID() {
        return (isset($this->xmlResult['transactionID']) ? $this->xmlResult['transactionID'] : null);
    }

    /**
     * Attempt to get request response transactionReferenceID.
     *
     * @return string|null
     */
    public function getTransactionRef() {
        return (isset($this->xmlResult['transactionReferenceID']) ? $this->xmlResult['transactionReferenceID'] : null);
    }

}
    