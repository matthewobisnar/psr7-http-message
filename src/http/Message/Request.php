<?php
namespace Http\Message;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\RequestInterface;
use Http\Message\Traits\UtilitiesTraits;
use Http\Message\Traits\StatusCodeTraits;
use Http\Message\Abstracts\AbstractMessage;
use Http\Exceptions\InvalidArgumentException;

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
class Request extends AbstractMessage implements RequestInterface
{
    /**
     * UriInterface instance.
     * 
     * @var UriInterface
     */
    protected $uri;

    /**
     * Http verb
     * 
     * @var string
     */
    protected $method;

    /**
     * Request Target
     * 
     * @var string
     */
    protected $requestTarget;

    /**
     * 
     * @param array
     * @param string
     * @param UrilInterface
     * @param 
     * @param
     */
    public function __construct($server, $uri = null, $body = null, $version = '1.1')
    {
        $this->method = strtolower($server['REQUEST_METHOD']);
        $this->protocolVersion = (string) $version;
        $this->setHeaders(headers_list());

        if (is_null($uri) && $uri == false) {

            $uri = sprintf(
                "%s://%s%s%s", 
                $server['REQUEST_SCHEME'], 
                $server['HTTP_HOST'], 
                $server['REQUEST_URI'], 
                $server['QUERY_STRING'] ?? null
            );
            
            $this->setUri($uri);
        } else {
            $this->setUri($uri);
        }

        $this->setBody($body);
    }

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget()
    {
        return $this->requestTarget;
    }

    /**
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return static
     */
    public function withRequestTarget($requestTarget)
    {
        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     * @return static
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        $method = strtoupper($this->requiredString($method));

        if (!in_array($method, $this->requestMethods)) {
            throw new InvalidArgumentException(
                sprintf('%s is not a valid method', $method)
            );
        }
        
        if ($method == strtoupper($this->method)) {
            return $this;
        }

        $new = clone $this;
        $new->method = $method;
        return $new;

    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $new = clone $this;
       
        if ($preserveHost === true) {
            $new->uri = $uri->withHost($this->uri->getHost());
        } else {
            $new->uri = $uri;
        }

        return $new;
    }
}

?>