<?php
require_once __DIR__ . '/vendor/autoload.php';

use Xpainel\X;

$instance = new X();

echo $instance->say("Hello World");