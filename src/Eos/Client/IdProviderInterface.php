<?php

namespace Eos\Client;

interface IdProviderInterface
{
    /**
     * Returns current EosId
     *
     * @return string
     */
    public function getEosId();
}
