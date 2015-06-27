<?php

namespace Eos\Client;

interface IdProviderInterface
{
    /**
     * Returns current EosId, used to group incoming messages
     *
     * @return string
     */
    public function getEosId();
}
