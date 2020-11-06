<?php

namespace KFlynns\PhpBitmap;

use GuzzleHttp\Psr7\Stream;

/**
 * Class Buffer
 * @package KFlynns\PhpBitmap
 */
class Buffer
{

    /** @var int  max 10 mib ram, then put to temp file */
    const MAX_RAM = 10485760;

    /** @var Stream */
    private $memory;

    /** @var int */
    private $size;

    /** @var int */
    private $bitsPerPixel;

    /**
     * Buffer constructor - fill with white.
     * @param int $width
     * @param int $height
     * @param int $bitsPerPixel
     */
    public function __construct($width, $height, $bitsPerPixel)
    {

        $this->bitsPerPixel = $bitsPerPixel;
        $this->size = $width * $height * ($bitsPerPixel / 8);
        $this->memory = new Stream(
            fopen(
                'php://temp/maxmemory:' . self::MAX_RAM,
                'a'
            )
        );

        $fillLength = 256;
        $whitePixels = str_repeat(chr(0xff), $fillLength);
        $whence = 0;

        do
        {
            $this->memory->seek($whence);

            if($whence + $fillLength > $this->size)
            {
                $whitePixels = substr($whitePixels, 0,$this->size - $whence);
            }

            $this->memory->write($whitePixels);
            $whence += $fillLength;

        } while ($whence < $this->size);

    }

    /**
     * Close stream.
     */
    public function __destruct()
    {
        $this->memory->close();
    }


    /**
     * Calculate the pixel offset in memory.
     * @param int $x
     * @param int $y
     * @return float|int
     */
    protected function getWhence($x, $y)
    {
        return $x * $y * ($this->bitsPerPixel / 8) - ($this->bitsPerPixel / 8);
    }


    /**
     * @param int $x
     * @param int $y
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function setPixel($x, $y, $red, $green, $blue)
    {
        $whence = $this->getWhence($x, $y);
        if($whence >= $this->size)
        {
            throw new \Exception('The pixel is out of range.');
        }
        $this->memory->seek($whence);
        $this->memory->write(pack('CCC', $blue, $green, $red));
    }

    /**
     * @param int $x
     * @param int $y
     * @return array
     */
    public function getPixel($x, $y)
    {
        $whence = $this->getWhence($x, $y);
        if($whence >= $this->size)
        {
            throw new \Exception('The pixel is out of range.');
        }
        $this->memory->seek($whence);
        $pixel = $this->memory->read(($this->bitsPerPixel / 8));

        // todo: works only with 24 bits per pixel... make dynamic
        return unpack('C1b\C1g\C1r', $pixel);
    }

    /**
     * @return Stream
     */
    public function getData()
    {
        return $this->memory;
    }

}