# PhpBitmap, writing bitmap images to PSR-7 streams

This library can handle RGB pixel buffers, create windows bitmap files and store the whole image file to a PSR-7 compatible output stream.

### Example: Write random pixels to the PHP output buffer.
```
$bitmap = new \KFlynns\PhpBitmap\Bitmap(64, 64);

for($y = 0; $y < 64; $y++)
{
    for($x = 0; $x < 64; $x++)
    {
        $bitmap
            ->getBuffer()
            ->setPixel($x, $y, random_int(0, 255),  random_int(0, 255),  random_int(0, 255));
    }
}

header('Content-Type: image/bmp');

$stream = new \GuzzleHttp\Psr7\Stream(
    fopen('php://output', 'w+')
);

$bitmap->output($stream);
$stream->close();
```

### Result
![example](test/example.png)

### Targets
At the moment, the Bitmap class only supports 32 bits v3 bitmap files. Maybe more formats will supported in the future. Maybe the pixel buffer get new features for drawing lines, recs, ... to it.

