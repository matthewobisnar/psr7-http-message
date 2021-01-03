<?php

namespace Http\Message;

use Throwable;
use Http\Exceptions\RuntimeException;
use Psr\Http\Message\StreamInterface;
use Http\Exceptions\InvalidArgumentException;

/**
 * Describes a data stream.
 *
 * Typically, an instance will wrap a PHP stream; this interface provides
 * a wrapper around the most common operations, including serialization of
 * the entire stream to a string.
 */
class Stream implements StreamInterface
{

    /**
     * Constant modes of stream.
     * 
     * @var array
     */
    private const MODES = [
        "read" => ['r', 'r+', 'w+', 'a+', 'x+', 'c+'],
        "write" => ['r+', 'w' ,'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+']
    ];

    /**
     * Resource.
     * 
     * @var resource|null
     */
    private $stream;

    /**
     * Stream Resource size.
     * 
     * @var int|null
     */
    private $size;
    
    /**
     * Is stream is seekable.
     * 
     * @var bool
     */
    private $seekable;
    
    /**
     * Is stream is readable.
     * 
     * @var bool
     */
    private $readable;
    
    /**
     * Is stream is writable.
     * 
     * @var bool
     */
    private $writable;

    /**
     * 
     * 
     */
    public function __construct($body = null)
    {

        if (!is_string($body) && !is_resource($body) && !is_null($body)) {
            throw new \InvalidArgumentException(sprintf("Invalid Arguments"));
        }

        if (is_string($body)) {

          $resource = fopen('php://temp', 'w+');
          fwrite($resource, $body);
          $body = $resource;

        }

        $this->stream = $body;

        if ($this->isSeekable()) {
            fseek($this->stream, 0, SEEK_CUR);
        }

    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        try {
           
            if ($this->isSeekable()) {
                $this->rewind(0);
            }
            
            return $this->getContents();

        } catch(Throwable $e){

            return "";
        
        }
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if (is_resource($this->stream)) {

            fclose($this->stream);
            $this->detach();
        
        }
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $resource = $this->stream;
        unset($this->stream);
        return $resource;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        if (!is_resource($this->stream)) {
            return null;
        }

        $this->size = fstat($this->stream)['size'] ?? null;

        return $this->size;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        if (!is_resource($this->stream)) {
            throw new RuntimeException(sprintf('ftell error'));
        }

         $position = ftell($this->stream);

        if ($position === false) {
            throw new RuntimeException(sprintf("ftell error"));
        }  

        return $position;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        if (!is_resource($this->stream)) {
            return false;
        }

        return feof($this->stream);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {

        if (!is_resource($this->stream)) {
            return false;
        }

        if (is_null($this->seekable)) {
            $this->seekable = $this->getMetadata('seekable') ?? false;
        }

        return $this->seekable;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!is_resource($this->stream)) {
            throw new RuntimeException(sprintf("Stream is detached."));
        }

        if (!$this->isSeekable()) {
            throw new RuntimeException(sprintf("seek is not seekable."));
        }

        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new RuntimeException(sprintf("Unable to seek stream."));
        }
    }

    /**
     * Seek to the beginning of the stream.
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        if (!is_resource($this->stream)) {
            return false;
        }

        if (is_null($this->writable)) {
            $mode = $this->getMetadata('mode');
            $this->writable = in_array($mode, self::MODES["write"]);
        }

        return $this->writable;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        if (!$this->isWritable()) {
            throw new RuntimeException(sprintf("stream is not writable"));
        }

        $result = fwrite($this->stream, $string);

        if ($result === false) {
            throw new RuntimeException(sprintf("Unable to write stream."));
        }

        return $result;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        if (!is_resource($this->stream)) {
            return false;
        }

        if (is_null($this->readable)) {
            $mode = $this->getMetadata('mode');
            $this->readable = in_array($mode, self::MODES["read"]);
        }

        return $this->readable;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        if (!$this->isReadable()) {
            throw new RuntimeException(sprintf("stream is not readable"));
        }

        $result = fread($this->stream, $length);

        if ($result === false) {
            throw new RuntimeException(sprintf("Unable to read stream."));
        }

        return $result;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        if (!is_resource($this->stream)) {
            throw new RuntimeException(sprintf("stream is not resource type."));
        }

        $result = stream_get_contents($this->stream);

        if ($result === false) {
            throw new RuntimeException(sprintf("Unable to read stream."));
        }

        return $result;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {

        if (is_null($this->stream) || !is_resource($this->stream)) {
            return null;
        }

        $meta = stream_get_meta_data($this->stream);

        if (is_null($key)) {
            return $meta;
        }

        if (isset($meta[$key])) {
            return $meta[$key];
        }
        
        return null;
    }
}