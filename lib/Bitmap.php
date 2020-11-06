<?php

namespace KFlynns\PhpBitmap;

use Psr\Http\Message\StreamInterface;

/**
 * Generate bitmap images from buffers,
 * like described under https://en.wikipedia.org/wiki/BMP_file_format
 * Class Bitmap
 * @package KFlynns\PhpBitmap
 */
class Bitmap
{

    const INT_SIZE_SHORT = 16;
    const INT_SIZE_LONG = 32;
    const BITS_PER_PIXEL = 24;
    const FILE_HEADER_SIZE = 0xE;
    const BITMAP_HEADER_SIZE = 0x28;
    const FILE_HEADER_MAGIC_WORD = 0x424D;

    /** @var Buffer */
    private $buffer;

    /** @var int  */
    private $bufferSize;

    /** @var int  */
    private $fileSize;

    /**
     * Bitmap constructor.
     * @param int $width
     * @param int $height
     * @throws \Exception
     */
    public function __construct($width, $height)
    {

        $this->integerToBinary(
            -0xFF,
            self::INT_SIZE_SHORT, [
                'signed' => true
            ]);

        $this->buffer = new Buffer(
            $width,
            $height,
            self::BITS_PER_PIXEL
        );

        $this->bufferSize = $width * $height * (self::BITS_PER_PIXEL / 8);
        $this->fileSize = self::FILE_HEADER_SIZE +
            self::BITMAP_HEADER_SIZE +
            $this->bufferSize;

    }

    /**
     * @return Buffer
     */
    public function getBuffer()
    {
        return $this->buffer;
    }


    /**
     * Generate file header (BITMAPFILEHEADER).
     * @return string
     */
    protected function getFileHeader()
    {
        return;    //. $this->integerToBinary($this->fileSize, self::INT_SIZE_LONG);
    }

    /**
     * Generate bitmap header (BITMAPINFOHEADER v3)
     * @return string
     */
    protected function getBitmapHeader()
    {
        return '1';
    }





    private function integerToBinary($integer, $size, array $options = [])
    {
        if(pow(2, $size) <= $size)
        {
            throw new \Exception('Integer "' . $integer . '" exceeds size of "' . $size . '" bits.');
        }

        $signed = $options['signed'] ?? false;

        if($signed)
        {
            $sign = $integer < 0 ? 32768 : 0;
            $integer = $integer >> 1;
            //$integer = $integer | $sign;
        }

        print_r(  base_convert($integer, 10, 2) );
        die();


    }

    /**
     * Output bitmap data to given output stream.
     * @param StreamInterface $stream
     */
    public function output(StreamInterface $stream)
    {
        foreach (['getFileHeader', 'getBitmapHeader', 'getData'] as $method)
        {
            $stream->write($this->{$method}());
        }
    }

}