<?php

namespace Ypa\Api\Connectors;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AbstractConnector
{
    /**
     * @var array
     */
    protected $config = [
        'http_errors' => false,
        'Content-Type' => 'application/json'
    ];

    public function get(string $pUri, array $pOptions = []): array
    {
        try {
            $query = $this->client()->get($pUri, $pOptions);
        } catch (BadRequestException $e) {
            $data = [
                'body' => $query->getBody()->getContents,
                'trace' => $e->getTraceAsString(),
                'stack' => $e->getTrace(),
            ];
            return $this->failedResponse($e->getMessage(), $e->getCode(), $data);
        }

        return $this->response($query);
    }

    public function put(string $pUri, array $pOptions = []): array
    {
        try {
            $query = $this->client()->put($pUri, $pOptions);
        } catch (BadRequestException $e) {
            $data = [
                'body' => $query->getBody()->getContents,
                'trace' => $e->getTraceAsString(),
                'stack' => $e->getTrace(),
            ];
            return $this->failedResponse($e->getMessage(), $e->getCode(), $data);
        }

        return $this->response($query);
    }

    public function post(string $pUri, array $pOptions = []): array
    {
        try {
            $query = $this->client()->post($pUri, $pOptions);
        } catch (BadRequestException $e) {
            $data = [
                'body' => $query->getBody()->getContents,
                'trace' => $e->getTraceAsString(),
                'stack' => $e->getTrace(),
            ];
            return $this->failedResponse($e->getMessage(), $e->getCode(), $data);
        }

        return $this->response($query);
    }

    public function setConfig(array $pConfig)
    {
        $this->config = $pConfig;
    }

    public function addConfig(array $pConfig)
    {
        $this->setConfig(array_merge($this->config, $pConfig));
        $this->client()->addConfig($pConfig);
    }

    /**
     * Response wrapper
     * @param ResponseInterface $pResponse
     * @return array
     */
    protected function response(ResponseInterface $pResponse): array
    {
        try {
            if ($pResponse->getStatusCode() === 200 || $pResponse->getStatusCode() === 201) {
                $body = [];
                $body['body'] = json_decode($pResponse->getBody()->getContents(), true) ?? [];
                $body['headers'] = $pResponse->getHeaders() ?? [];

                return $this->successResponse($body);
            } else {
                $body = json_decode($pResponse->getBody()->getContents(), true) ?? [];
                $message = $body['message'] ?? '';

                return $this->failedResponse($message, $pResponse->getStatusCode(), $body);
            }
        } catch (\Exception $e) {
            $data = [
                'body' => $pResponse->getBody()->getContents() ?? [],
                'headers' => $pResponse->getHeaders() ?? [],
                'trace' => $e->getTraceAsString(),
                'stack' => $e->getTrace(),
            ];
            return $this->failedResponse($e->getMessage(), $e->getCode(), $data);
        }
    }

    /**
     * return success response pattern
     * @param array $pData
     * @return array
     */
    protected function successResponse(array $pData): array
    {
        return [
            'success' => true,
            'status' => 200,
            'data' => $pData['body'],
            'headers' => $pData['headers']
        ];
    }

    /**
     * return failed response pattern
     * @param string $pMessage
     * @param int $pStatus
     * @param array $pData
     * @return array
     */
    protected function failedResponse(string $pMessage, int $pStatus = 200, array $pData = []): array
    {
        return [
            'success' => false,
            'message' => $pMessage,
            'status' => $pStatus,
            'data' => $pData
        ];
    }

    protected function client(): Client
    {
        return $this->client ?? new Client($this->config);
    }
}
