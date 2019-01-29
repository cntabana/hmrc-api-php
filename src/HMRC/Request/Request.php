<?php


namespace HMRC\Request;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use HMRC\Response\Response as HMRCResponse;

abstract class Request
{
    /** @var string URL of sandbox environment */
    const URL_SANDBOX = 'https://test-api.service.hmrc.gov.uk';

    /** @var string URL of live environment */
    const URL_LIVE = 'https://api.service.hmrc.gov.uk';

    /** @var string constant for method GET */
    const METHOD_GET = 'GET';

    /** @var string constant for method POST */
    const METHOD_POST = 'POST';

    /** @var string accept header for request */
    const HEADER_ACCEPT = 'Accept';

    /** @var string authorization header for request */
    const HEADER_AUTHORIZATION = 'Authorization';

    /** @var string content type for request */
    const HEADER_CONTENT_TYPE = 'Content-Type';

    /** @var string header value application/json */
    const HEADER_VALUE_APPLICATION_JSON = 'application/json';

    /** @var Client Guzzle client */
    protected $client;

    /** @var string API base URL */
    protected $apiBaseUrl;

    /** @var string Service version of HMRC API */
    protected $serviceVersion = '1.0';

    /** @var string Content type of the request */
    protected $contentType = 'json';

    public function __construct()
    {
        $this->client = new Client;
        $this->apiBaseUrl = static::URL_SANDBOX;
    }

    public function useLiveEnv()
    {
        $this->apiBaseUrl = static::URL_LIVE;

        return $this;
    }

    public function useSandboxEnv()
    {
        $this->apiBaseUrl = static::URL_SANDBOX;

        return $this;
    }

    /**
     * @return HMRCResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fire()
    {
        /** @var Response $response */
        $response = $this->client->request($this->getMethod(), $this->getURI(), $this->getHTTPClientOptions());

        return new HMRCResponse($response);
    }

    /**
     * Get options to call via HTTP client
     *
     * @return array
     */
    protected function getHTTPClientOptions(): array
    {
        return [
            'headers' => $this->getHeaders(),
        ];
    }

    protected function getAcceptHeader(): string
    {
        return "application/vnd.hmrc.{$this->serviceVersion}+{$this->contentType}";
    }

    protected function getAuthorizationHeader(string $token): string
    {
        return "Bearer {$token}";
    }

    protected function getHeaders(): array
    {
        return [
            static::HEADER_ACCEPT => $this->getAcceptHeader(),
        ];
    }

    protected function getURI(): string
    {
        return "{$this->apiBaseUrl}{$this->getApiPath()}";
    }

    /**
     * @return string
     */
    public function getApiBaseUrl(): string
    {
        return $this->apiBaseUrl;
    }

    /**
     * @param string $apiBaseUrl
     *
     * @return Request
     */
    public function setApiBaseUrl(string $apiBaseUrl): Request
    {
        $this->apiBaseUrl = $apiBaseUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getServiceVersion(): string
    {
        return $this->serviceVersion;
    }

    /**
     * @param string $serviceVersion
     *
     * @return Request
     */
    public function setServiceVersion(string $serviceVersion): Request
    {
        $this->serviceVersion = $serviceVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     *
     * @return Request
     */
    public function setContentType(string $contentType): Request
    {
        $this->contentType = $contentType;

        return $this;
    }

    abstract protected function getMethod(): string;
    abstract protected function getApiPath(): string;
}
