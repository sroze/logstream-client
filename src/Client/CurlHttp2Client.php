<?php

namespace LogStream\Client;

use GuzzleHttp\Exception\AdapterException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\ResponseInterface;
use LogStream\Client;
use LogStream\Log;
use GuzzleHttp\Client as GuzzleClient;
use LogStream\LogNode;
use LogStream\WrappedLog;

class CurlHttp2Client implements Client
{
    /**
     * @var \LogStream\Client\LogNormalizer
     */
    private $logNormalizer;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var resource
     */
    private $curlHandler;

    /**
     * @param \LogStream\Client\LogNormalizer $logNormalizer
     * @param string             $baseUrl
     */
    public function __construct(Client\LogNormalizer $logNormalizer, $baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->logNormalizer = $logNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function create(LogNode $log, Log $parent = null)
    {
        $normalized = $this->logNormalizer->normalize($log, $parent);

        try {
            $response = $this->request('POST', $this->baseUrl.'/v1/logs', $normalized);
        } catch (AdapterException $e) {
            throw new ClientException('Unable to create log', $e->getCode(), $e);
        } catch (RequestException $e) {
            throw new ClientException('Unable to create log', $e->getCode(), $e);
        }

        return $this->getWrappedResponseLog($response, $log);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus(Log $log, $status)
    {
        try {
            $response = $this->request('PUT', $this->baseUrl.'/v1/logs/'.$log->getId(), [
                'status' => $status,
            ]);
        } catch (AdapterException $e) {
            throw new ClientException('Unable to update log', $e->getCode(), $e);
        } catch (RequestException $e) {
            throw new ClientException('Unable to update log', $e->getCode(), $e);
        }

        return $this->getWrappedResponseLog($response, $log);
    }

    /**
     * @param string $response
     * @param LogNode           $logNode
     *
     * @return WrappedLog
     *
     * @throws ClientException
     */
    private function getWrappedResponseLog($response, LogNode $logNode)
    {
        $data = json_decode($response, true);

        return new WrappedLog($data['_id'], $logNode, array_key_exists('status', $data) ? $data['status'] : null);
    }

    private function request($method, $path, array $body = [])
    {
        $data_string = json_encode($body);

        $ch = curl_init($path);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
        ]);

        $result = $this->curlExecWithMulti($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != 200) {
            throw new ClientException(sprintf('Found status %d', $info['http_code']));
        }

        return $result;
    }

    private function curlExecWithMulti($handle)
    {
        if (null === $this->curlHandler) {
            $this->curlHandler = curl_multi_init();
        }

        // Add the handle to be processed.
        curl_multi_add_handle($this->curlHandler, $handle);

        // Do all the processing.
        $active = NULL;
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

    public function __destroy()
    {
        if (null !== $this->curlHandler) {
            curl_multi_close($this->curlHandler);
        }
    }

}
