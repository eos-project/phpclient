<?php

namespace Eos\Client;

/**
 * Class EosUdpClient
 *
 * Main Eos client for PHP
 *
 * @package Eos\Client
 */
class EosUdpClient
{
    /**
     * @var string
     */
    private $host;
    /**
     * @var int
     */
    private $port;
    /**
     * @var string
     */
    private $realm;
    /**
     * @var string
     */
    private $secret;
    /**
     * @var string[]
     */
    private $tags;
    /**
     * @var resource
     */
    private $socket;
    /**
     * @var IdProviderInterface
     */
    private $idProvider;

    /**
     * Constructor
     *
     * @param string              $realm      Realm (login to Eos server)
     * @param string              $secret     Secret password to Eos server
     * @param string              $host       Hostname or IP of Eos server
     * @param int                 $port       Port of Eos UDP server listener (defaults to 8087)
     * @param string[]            $tags       Default tags
     * @param IdProviderInterface $idProvider
     */
    public function __construct(
        $realm,
        $secret,
        $host,
        $port = 8087,
        array $tags = array(),
        IdProviderInterface $idProvider = null
    ) {
        if (!is_string($realm) || empty($realm)) {
            throw new \InvalidArgumentException("Realm expected to be not empty string");
        }
        if (!is_string($secret) || empty($secret)) {
            throw new \InvalidArgumentException("Secret expected to be not empty string");
        }
        if (!is_string($host) || empty($host)) {
            throw new \InvalidArgumentException("Host expected to be not empty string");
        }
        if ($port === null) {
            $port = 8087;
        }
        if (!is_int($port) || $port < 1) {
            throw new \InvalidArgumentException("Port expected to be valid integer port number");
        }

        $this->host = $host;
        $this->port = $port;
        $this->secret = $secret;
        $this->realm = $realm;
        $this->tags = $tags;
        $this->idProvider = $idProvider !== null ? $idProvider : new StaticIdProvider();

        // Building socket
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    }

    /**
     * Send string to Eos server
     *
     * @param string $data
     * @param array $tags
     */
    public function sendString($data, array $tags = array())
    {
        $this->send(array('message' => strval($data)), $tags);
    }

    /**
     * Send content to Eos server
     *
     * @param array $content
     * @param array $tags
     */
    public function send(array $content, array $tags = array())
    {
        if (count($content) === 0) {
            return;
        }

        // Extract tags from content body and merge with default
        if (isset($content['tags']) && is_array($content['tags'])) {
            $tags = array_merge($tags, $content['tags']);
            unset($content['tags']);
        }
        $tags = array_merge($tags, $this->tags);
        if (count($tags) === 0) {
            return;
        }

        // Add EosId
        $content['eos-id'] = $this->idProvider->getEosId();

        // Serialize payload
        $payload = json_encode($content);

        // Generate NONCE
        $nonce = microtime(true) . mt_rand();

        // Generate signature
        $signature = hash("sha256", $nonce . $payload . $this->secret);
        $packet = $nonce . "\n" . $signature . "\n" . $this->realm . "+log://" . implode(':', $tags) . "\n" . $payload;

        // Send
        socket_sendto($this->socket, $packet, strlen($packet), 0, $this->host, $this->port);
    }
}
