<?php
namespace Http\Message\Abstracts;

use Http\Message\Uri;
use Http\Message\Stream;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\MessageInterface;
use Http\Message\Traits\UtilitiesTraits;
use Http\Message\Traits\StatusCodeTraits;
use Http\Exceptions\InvalidArgumentException;

/**
 * HTTP messages consist of requests from a client to a server and responses
 * from a server to a client. This interface defines the methods common to
 * each.
 *
 * Messages are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 *
 * @link http://www.ietf.org/rfc/rfc7230.txt
 * @link http://www.ietf.org/rfc/rfc7231.txt
 */

abstract class AbstractMessage implements MessageInterface
{
    use UtilitiesTraits;

    /**
     * @var array list of Http verbs
     */
    protected $requestMethods = [
        'GET',
        'POST',
        'PUT',
        'OPTIONS',
        'PATCH',
        'DELETE',
        'HEAD'
    ];

    /**
     * Map headers.
     * 
     * @var array
     */

    public $headers = [];

    /**
     * Map header Names
     * 
     * @var array
     */
    public $headerNames = [];

    /**
     * Http protocol version.
     * 
     * @var number;
     */
    public $protocolVersion = '1.1';
    
    /**
     * 
     * @var StreamInterface
     */
    public $body;

    /**
     * 
     * @var CONST protocol version
     */
    private CONST protocolVersion = ['1.1', '1.0'];
    
    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new protocol version.
     *
     * @param string $version HTTP protocol version.
     * @return static
     */
    public function withProtocolVersion($version): self
    {
        if ($this->protocolVersion == (string) $version) {
            return $this;
        }

        $new = clone $this;
        $new->protocolVersion = (string) $version;

        return $new;
    }

    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     */
    public function getHeaders()
    {
        $header = $this->headers;
        
        foreach ($this->headerNames as $origin) {
            $name = strtolower($origin);
            
            if (isset($header[$name])) {
                $value = $header[$name];
                unset($header[$name]);
                $header[$origin] = $value;
            }
        }

        return $header;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name)
    {
        return isset($this->headers[strtolower($name)]);
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function getHeader($name)
    {
       $name = strtolower($name);

       if (!isset($this->headers[$name])) {
            return [];
       }

       return $this->headers[$name];
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name)
    {
        if (is_array($this->headers[strtolower($name)])) {
            return implode(', ', $this->headers[strtolower($name)]);
        }

       return $this->headers[strtolower($name)];
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value): self
    {

        $name = $this->requiredString($name);
        $value = $this->requiredString($value);        

        if (isset($this->headers[strtolower($name)]) 
        && $this->headers[strtolower($name)] == strtolower($value)) {

            return $this;
        }

        $new = clone $this;
        $new->headers[strtolower($name)] = strtolower($value);
        $new->headerNames[$name] = $name;

        return $new; 
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value): self
    {
        $name = $this->requiredString($name);
        $value = $this->requiredString($value);

        $new = clone $this;
        $new->headerNames[$name] = $name;

        if (isset($new->headers[strtolower($name)]) 
        && $new->headers[strtolower($name)] != strtolower($value)) {

           if (is_array($new->headers[strtolower($name)])) {
    
                $new->headers[strtolower($name)] = array_merge(
                    $new->headers[strtolower($name)], 
                    (array) strtolower($value)
                );

            } else {

                $new->headers[strtolower($name)] = array(
                    $new->headers[strtolower($name)], 
                    strtolower($value)
                );

            }

        } else {

            $new->headers[strtolower($name)] = strtolower($value);
        
        }

        return $new;
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return static
     */
    public function withoutHeader($name): self
    {

        $new = clone $this;

        if (isset($new->headers[strtolower($name)])) {
            unset($new->headers[strtolower($name)]);
            unset($new->headerNames[$name]);
        }

        return $new;
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody()
    {
        return $this->body;
    } 

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     * @return static
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body)
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }

    /**
     * 
     * Set headers
     * 
     * {@inheritdoc}
     * 
     * @param array of headers
     * @return array;
     * @return void
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $headerKey => $header) {

            $key = strtolower($headerKey);

            if (is_string($header)) {
                
                $value = strtolower($header);
                $this->headers[$key] = explode(',', $value);
            
            } else {

                $value = array_map('strtolower', $header);
                $this->headers[$key] = $value;
            
            }
        }
    }

    /**
     * {@inheritdoc}
     * 
     * @return void
     */
    public function setBody($body = null)
    {
        if (!($body instanceof StreamInterface)) {
            $body = new Stream($body);
        }
        
        $this->body = $body;
    }

    /**
     * 
     * {@inheritdoc}
     * 
     * @return void
     */
    protected function setUri($uri)
    {
        if (!($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }

        $this->uri = $uri;
    }

    /**
     * 
     * {@inheritdoc}
     * 
     * @param string
     * @return array
     */
    public function getServerHeaderFromUri($url)
    {
        $outputHeader = [];

        foreach (get_headers($url) as $header) {
            list($key, $value) = array_pad(str_split(':', $header), 2 ,'');

            $outputHeader[strtolower($key)] = strtolower($value);
        }

        return $outputHeader;
    }

    /**
     * {@inheritdoc}
     * 
     * @param string
     * @return array
     */
    public function defaultHeader()
    {
        return [
            'Content-Type' => [ 'text/html; charset=UTF-8', 'multipart/form-data']
        ];
    }
}