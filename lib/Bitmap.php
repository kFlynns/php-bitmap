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

    /** @var int 16 bit for short integers */
    const INT_SIZE_SHORT = 16;

    /** @var int 32 bit for long integers */
    const INT_SIZE_LONG = 32;

    /** @var int only 32 bits per pixel supported now */
    const BITS_PER_PIXEL = 32;

    /** @var int 14 bytes BITMAPFILEHEADER */
    const FILE_HEADER_SIZE = 0xE;

    /** @var int 40 bytes BITMAPINFOHEADER */
    const BITMAP_HEADER_SIZE = 0x28;


    /** @var Buffer */
    private $buffer;

    /** @var int  */
    private $bufferSize;

    /** @var int  */
    private $fileSize;

    /** @var int */
    private $width;

    /** @var int */
    private $height;

    /**
     * Bitmap constructor.
     * @param int $width
     * @param int $height
     * @throws \Exception
     */
    public function __construct($width, $height)
    {

        $this->buffer = new Buffer(
            $width,
            $height,
            self::BITS_PER_PIXEL
        );

        $this->bufferSize = $width * $height * (self::BITS_PER_PIXEL / 8);
        $this->fileSize = self::FILE_HEADER_SIZE +
            self::BITMAP_HEADER_SIZE +
            $this->bufferSize;

        $this->width = $width;
        $this->height = $height;

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
        return pack(
            'ccVVV',
            0x42,
            0x4D,
            $this->fileSize,
            0,
            self::FILE_HEADER_SIZE + self::BITMAP_HEADER_SIZE
        );
    }

    /**
     * Generate bitmap header (BITMAPINFOHEADER v3)
     * @return string
     */
    protected function getBitmapHeader()
    {
        return pack(
            'VllvvVVllVV',
            self::BITMAP_HEADER_SIZE,
            $this->width,
            $this->height,
            1,
            self::BITS_PER_PIXEL,
            0,
            $this->bufferSize,
            0, 0, 0, 0
        );
    }

    /**
     * Output bitmap data to given output stream.
     * @param StreamInterface $stream
     */
    public function output(StreamInterface $stream)
    {
        foreach (['getFileHeader', 'getBitmapHeader'] as $method)
        {
            $stream->write($this->{$method}());
        }
        $stream->write($this->getBuffer()->getData());
    }

}