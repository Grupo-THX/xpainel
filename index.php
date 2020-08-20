<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ourcodeworld\HelloComposer\Hello;

$instance = new Hello();

echo $instance->say("Hello World");

echo 'ok';