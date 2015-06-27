
PHP Eos client
==============

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eos-project/phpclient/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/eos-project/phpclient/?branch=master)
[![Build Status](https://travis-ci.org/eos-project/phpclient.svg?branch=master)](https://travis-ci.org/eos-project/phpclient)

# Simple static usage

Eos client can be used from static context

```php

\Eos\Client\Eos::sendString("Foo"); // Wont be sent - Eos not initialized
\Eos\Client\Eos::init("realm", "secret", "hostname"); // Initialize
\Eos\Client\Eos::sendString("Foo"); // Delivered using UDP
\Eos\Client\Eos::send(["message" => "hello", "ip" => $_SERVER["REMOTE_ADDRg"]]); // Delivered using UDP
```