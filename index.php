<?php
require_once __DIR__ . '/../vendor/autoload.php';

use grupothx\Xpainel\X;

$instance = new X();

echo $instance->say("Hello World");