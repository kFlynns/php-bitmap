<?php

require_once __DIR__ . '/../vendor/autoload.php';


$bitmap = new \KFlynns\PhpBitmap\Bitmap(10, 10);


$bitmap->getBuffer()->setPixel(9,10, 0, 0, 0);


$stream = new \GuzzleHttp\Psr7\Stream(fopen('php://output', 'a+'));
$bitmap->output($stream);
$stream->close();