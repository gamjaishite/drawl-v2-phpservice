<?php

require_once __DIR__ . '/../Common/CustomResponse.php';

require_once __DIR__ . '/../Utils/SOAPResponse.php';

class SOAPRequest
{
    public string $url;
    private string $endpoint;
    private array $headers;
    private string $operationName;
    private array $soapHeaders;
    private array $soapBody;
    private string $serviceName;

    public function __construct($endpoint, $operationName, $headers, $soapHeaders, $soapBody, $serviceName = "http://Services.soapService.org/")
    {
        $this->url = (getenv("SOAP_SERVICE_BASE_URL") ?? "http://host.docker.internal:8083") . "/" . $endpoint;
        $this->endpoint = $endpoint;
        $this->operationName = $operationName;
        $this->headers = $headers;
        $this->soapHeaders = $soapHeaders;
        $this->soapBody = $soapBody;
        $this->serviceName = $serviceName;
    }

    public function post(): CustomResponse
    {
        $curl = curl_init($this->url);

        $httpHeaders = array_merge(
            array(
                "Content-Type: text/xml",
//                "token:" . getenv('OUTBOUND_SOAP_API_KEY')
            ),
        );
        $body = <<<BODY
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="{$this->serviceName}">
                {$this->parseHeader()}
                {$this->parseBody()}
            </soapenv:Envelope>
        BODY;

        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeaders);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        $streamVerboseHandle = fopen('php://temp', 'w+');
        curl_setopt($curl, CURLOPT_STDERR, $streamVerboseHandle);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (!$response) {
            $finalResponse = new CustomResponse();
            http_response_code(500);
            $finalResponse->status = 500;
            $finalResponse->message = "Something went wrong, please try again later";
            curl_close($curl);
            return $finalResponse;
        }

//        rewind($streamVerboseHandle);
//        $verboseLog = stream_get_contents($streamVerboseHandle);
//
//        echo "cUrl verbose information:\n",
//        "<pre>", htmlspecialchars($verboseLog), "</pre>\n";

        curl_close($curl);
        if ($httpCode == "500") {
            return SOAPResponse::parseFault($response);
        } else {
            return SOAPResponse::parseSuccess($response);
        }
    }

    private function parseHeader(): string
    {
        $xml = new DOMDocument();
        $root = $xml->appendChild($xml->createElement("soapenv:Header"));

        foreach ($this->soapHeaders as $key => $value) {
            $root->appendChild($xml->createElement($key, $value));
        }

        return $xml->saveXML($xml->documentElement);
    }

    private function parseBody(): string
    {
        $xml = new DOMDocument();
        $root = $xml->appendChild($xml->createElement("soapenv:Body"));
        $operation = $root->appendChild($xml->createElement("ser:{$this->operationName}"));

        foreach ($this->soapBody as $key => $value) {
            $operation->appendChild($xml->createElement($key, $value));
        }

        return $xml->saveXML($xml->documentElement);
    }
}