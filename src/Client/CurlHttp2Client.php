<?php

namespace LogStream\Client;

use LogStream\Client;
use LogStream\Log;
use LogStream\Client\Normalizer\LogNormalizer;

class CurlHttp2Client implements Client
{
    /**
     * @var LogNormalizer
     */
    private $logNormalizer;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var bool
     */
    private $strictSsl;

    /**
     * @var resource|null
     */
    private $curlHandler;

    /**
     * @param LogNormalizer $logNormalizer
     * @param string        $baseUrl
     * @param bool          $strictSsl
     */
    public function __construct(LogNormalizer $logNormalizer, $baseUrl, $strictSsl = true)
    {
        $this->baseUrl = $baseUrl;
        $this->logNormalizer = $logNormalizer;
        $this->strictSsl = $strictSsl;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Log $log)
    {
        $normalized = $this->logNormalizer->normalize($log);
        $response = $this->request('POST', $this->baseUrl.'/v1/logs', $normalized);

        return $this->logNormalizer->denormalize($response);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus(Log $log, $status)
    {
        return $this->patch($log, [
            'status' => $status,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function patch(Log $log, array $patch)
    {
        $url = sprintf('%s/v1/logs/%s', $this->baseUrl, $log->getId());
        $response = $this->request('PATCH', $url, $patch);

        return $this->logNormalizer->denormalize($response);
    }

    /**
     * {@inheritdoc}
     */
    public function archive(Log $log)
    {
        $url = sprintf('%s/v1/archive/%s', $this->baseUrl, $log->getId());
        $response = $this->request('POST', $url);

        return $this->logNormalizer->denormalize($response);
    }

    /**
     * Run the given request.
     *
     * @param string $method
     * @param string $path
     * @param array  $body
     *
     * @throws ClientException
     *
     * @return string
     */
    private function request($method, $path, array $body = [])
    {
        $data_string = json_encode($body);

        $ch = curl_init($path);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (false === $this->strictSsl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: '.strlen($data_string),
        ]);

        $contents = $this->executeMultiplexedRequest($ch);
        $info = curl_getinfo($ch);

        if ($info['http_code'] != 200) {
            curl_close($ch);

            throw new ClientException(sprintf('Found status %d', $info['http_code']));
        }

        curl_close($ch);

        if (null === ($json = json_decode($contents, true))) {
            throw new ClientException('The response is not a valid JSON object');
        } elseif (!array_key_exists('_id', $json)) {
            throw new ClientException('No `_id` found in response');
        }

        return $json;
    }

    /**
     * Execute the given cURL handle in a multiplexed handler.
     *
     * @param resource $handle
     *
     * @return string
     */
    private function executeMultiplexedRequest($handle)
    {
        if (null === $this->curlHandler) {
            $this->curlHandler = curl_multi_init();
        }

        // Add the handle to be processed.
        curl_multi_add_handle($this->curlHandler, $handle);

        // Do all the processing.
        $active = null;
        do {
            $ret = curl_multi_exec($this->curlHandler, $active);
        } while ($ret == CURLM_CALL_MULTI_PERFORM);

        while ($active && $ret == CURLM_OK) {
            if (curl_multi_select($this->curlHandler) == -1) {
                usleep(1);
            }

            do {
                $mrc = curl_multi_exec($this->curlHandler, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }

        // Get the contents for this handle
        $contents = curl_multi_getcontent($handle);

        // Remove the handle from the multi processor.
        curl_multi_remove_handle($this->curlHandler, $handle);

        return $contents;
    }

    /**
     * Close the multiplex handler that the same time than the class
     * is destroyed.
     */
    public function __destroy()
    {
        if (null !== $this->curlHandler) {
            curl_multi_close($this->curlHandler);
        }
    }
}
