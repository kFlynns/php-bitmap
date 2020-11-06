<?php

require_once __DIR__ . '/../vendor/autoload.php';

$bitmap = new \KFlynns\PhpBitmap\Bitmap(64, 64);


for($y = 0; $y < 64; $y++)
{
    for($x = 0; $x < 64; $x++)
    {
        $bitmap->getBuffer()->setPixel($x, $y, random_int(0, 255),  random_int(0, 255),  random_int(0, 255));
    }
}





header('Content-Type: image/bmp');
$stream = new \GuzzleHttp\Psr7\Stream(fopen('php://output', 'w+'));
$bitmap->output($stream);
$stream->close();