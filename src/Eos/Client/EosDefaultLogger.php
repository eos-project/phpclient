<?php

namespace Eos\Client;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class EosDefaultLogger extends AbstractLogger
{
    private static $levels = [
        LogLevel::DEBUG => 1,
        LogLevel::INFO => 2,
        LogLevel::NOTICE => 3,
        LogLevel::WARNING => 4,
        LogLevel::ERROR => 5,
        LogLevel::CRITICAL => 6,
        LogLevel::ALERT => 7,
        LogLevel::EMERGENCY => 8
    ];

    /**
     * @var EosUdpClient
     */
    private $client;
    /**
     * @var int
     */
    private $threshold;

    /**
     * Constructor
     *
     * @param string   $realm     Realm (login to Eos server)
     * @param string   $secret    Secret password to Eos server
     * @param string   $host      Hostname or IP of Eos server
     * @param int      $port      Port of Eos UDP server listener (defaults to 8087)
     * @param string[] $tags      Default tags
     * @param string   $threshold Min level to send
     */
    public function __construct($realm, $secret, $host, $port = 8087, array $tags = array(), $threshold = null)
    {
        // Set hostname
        $tags[] = gethostname();

        // Build client
        $this->client = new EosUdpClient($realm, $secret, $host, $port, $tags);

        if ($threshold !== null && isset(self::$levels[$threshold])) {
            $this->threshold = self::$levels[$threshold];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = array())
    {
        // Check threshold
        if (isset(self::$levels[$level]) && self::$levels[$level] < $this->threshold) {
            return;
        }

        $packet = array_merge(
            $context,
            array(
                'message' => $message,
                'level'   => $level
            )
        );

        $this->client->send($packet, array($level));
    }
}
