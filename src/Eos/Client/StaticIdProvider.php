<?php

namespace Eos\Client;

class StaticIdProvider implements IdProviderInterface
{
    /**
     * @var string
     */
    private static $id = null;

    public function __construct()
    {
        if (self::$id === null) {
            $this->refresh();
        }
    }

    /**
     * Refreshes current static ID value
     */
    public static function refresh()
    {
        self::$id = uniqid() . mt_rand(100000, 999999);
    }

    /**
     * Returns current EosId
     *
     * @return string
     */
    public function getEosId()
    {
        return self::$id;
    }
}
