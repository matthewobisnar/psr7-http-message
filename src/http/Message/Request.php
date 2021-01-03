<?php
namespace Http\Message;

use Http\Message\Exceptions\InvalidArgumentException;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\RequestInterface;

use Http\Message\Traits\RequestTrait;
use Http\Message\Traits\UtilitiesTraits;
use Http\Message\Traits\StatusCodeTraits;
use Http\Message\Abstracts\AbstractMessage;

/**
 * Representation of an outgoing, client-side request.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - HTTP method
 * - URI
 * - Headers
 * - Message body
 *
 * During construction, implementations MUST attempt to set the Host header from
 * a provided URI if no Host header is provided.
 *
 * Requests are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */
final class Request extends AbstractMessage implements RequestInterface
{
    use RequestTrait;

    /**
     * UriInterface instance.
     * 
     * @var UriInterface
     */
    private $uri;

    /**
     * Http verb
     * 
     * @var string
     */
    private $method;

    /**
     * Request Target
     * 
     * @var string
     */
    private $requestTarget;

    /**
     * 
     * @param array
     * @param string
     * @param UrilInterface
     * @param 
     * @param
     */
    public function __construct(array $headers, string $method, $uri, $body = null, $version)
    {
        $this->method = strtolower($method);
        $this->protocolVersion = (string) $version;
        $this->setUri($uri);
        $this->setHeaders($headers);
        $this->setBody($body);
    }
}

?>