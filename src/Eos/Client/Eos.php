<?php

namespace Eos\Client;

/**
 * Class Eos
 *
 * Static Facade for EOS
 *
 * @package Eos\Client
 */
class Eos
{
    /**
     * @var EosUdpClient
     */
    private static $client = null;

    public static function init($realm, $secret, $host, $port = 8087, array $tags = array())
    {
        $tags[] = gethostname();

        self::$client = new EosUdpClient($realm, $secret, $host, $port, $tags);
    }

    /**
     * Send content to Eos server
     *
     * @param array $content
     * @param array $tags
     */
    public static function send(array $content, array $tags = array())
    {
        if (self::$client !== null) {
            self::$client->send($content, $tags);
        }
    }

    /**
     * Send string to Eos server
     *
     * @param string $data
     * @param array $tags
     */
    public static function sendString($data, array $tags = array())
    {
        if (self::$client !== null) {
            self::$client->sendString($data, $tags);
        }
    }
}
