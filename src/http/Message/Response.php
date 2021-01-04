<?php
namespace Http\Message;

use Psr\Http\Message\ResponseInterface;
use Http\Message\Traits\UtilitiesTraits;
use Http\Message\Abstracts\AbstractMessage;
use Http\Exceptions\InvalidArgumentException;
/**
 * Representation of an outgoing, server-side response.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - Status code and reason phrase
 * - Headers
 * - Message body
 *
 * Responses are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */
final class Response extends AbstractMessage implements ResponseInterface
{
    public const INFORMATIONAL_CONTINUE             = 100;
    public const INFORMATIONAL_SWITCHING_PROTOCOLS  = 101;
    public const INFORMATIONAL_PROCESSING           = 102;
    public const SUCCESSFUL_OK                      = 200;
    public const SUCCESSFUL_CREATED                 = 201;
    public const SUCCESSFUL_ACCEPTED                = 202;

     /**
     * Response status code and descriptions
     * 
     * @var array
     */
    private $http_status_codes = array(
        100 => 'Informational: Continue',
        101 => 'Informational: Switching Protocols',
        102 => 'Informational: Processing',
        200 => 'Successful: OK',
        201 => 'Successful: Created',
        202 => 'Successful: Accepted',
        203 => 'Successful: Non-Authoritative Information',
        204 => 'Successful: No Content',
        205 => 'Successful: Reset Content',
        206 => 'Successful: Partial Content',
        207 => 'Successful: Multi-Status',
        208 => 'Successful: Already Reported',
        226 => 'Successful: IM Used',
        300 => 'Redirection: Multiple Choices',
        301 => 'Redirection: Moved Permanently',
        302 => 'Redirection: Found',
        303 => 'Redirection: See Other',
        304 => 'Redirection: Not Modified',
        305 => 'Redirection: Use Proxy',
        306 => 'Redirection: Switch Proxy',
        307 => 'Redirection: Temporary Redirect',
        308 => 'Redirection: Permanent Redirect',
        400 => 'Client Error: Bad Request',
        401 => 'Client Error: Unauthorized',
        402 => 'Client Error: Payment Required',
        403 => 'Client Error: Forbidden',
        404 => 'Client Error: Not Found',
        405 => 'Client Error: Method Not Allowed',
        406 => 'Client Error: Not Acceptable',
        407 => 'Client Error: Proxy Authentication Required',
        408 => 'Client Error: Request Timeout',
        409 => 'Client Error: Conflict',
        410 => 'Client Error: Gone',
        411 => 'Client Error: Length Required',
        412 => 'Client Error: Precondition Failed',
        413 => 'Client Error: Request Entity Too Large',
        414 => 'Client Error: Request-URI Too Long',
        415 => 'Client Error: Unsupported Media Type',
        416 => 'Client Error: Requested Range Not Satisfiable',
        417 => 'Client Error: Expectation Failed',
        418 => 'Client Error: I\'m a teapot',
        419 => 'Client Error: Authentication Timeout',
        420 => 'Client Error: Enhance Your Calm',
        420 => 'Client Error: Method Failure',
        422 => 'Client Error: Unprocessable Entity',
        423 => 'Client Error: Locked',
        424 => 'Client Error: Failed Dependency',
        424 => 'Client Error: Method Failure',
        425 => 'Client Error: Unordered Collection',
        426 => 'Client Error: Upgrade Required',
        428 => 'Client Error: Precondition Required',
        429 => 'Client Error: Too Many Requests',
        431 => 'Client Error: Request Header Fields Too Large',
        444 => 'Client Error: No Response',
        449 => 'Client Error: Retry With',
        450 => 'Client Error: Blocked by Windows Parental Controls',
        451 => 'Client Error: Redirect',
        451 => 'Client Error: Unavailable For Legal Reasons',
        494 => 'Client Error: Request Header Too Large',
        495 => 'Client Error: Cert Error',
        496 => 'Client Error: No Cert',
        497 => 'Client Error: HTTP to HTTPS',
        499 => 'Client Error: Client Closed Request',
        500 => 'Server Error: Internal Server Error',
        501 => 'Server Error: Not Implemented',
        502 => 'Server Error: Bad Gateway',
        503 => 'Server Error: Service Unavailable',
        504 => 'Server Error: Gateway Timeout',
        505 => 'Server Error: HTTP Version Not Supported',
        506 => 'Server Error: Variant Also Negotiates',
        507 => 'Server Error: Insufficient Storage',
        508 => 'Server Error: Loop Detected',
        509 => 'Server Error: Bandwidth Limit Exceeded',
        510 => 'Server Error: Not Extended',
        511 => 'Server Error: Network Authentication Required',
        598 => 'Server Error: Network read timeout error',
        599 => 'Server Error: Network connect timeout error',
    );
    
    /**
     * Status code
     * 
     * @var int
     */
    public $status;
    
    /**
     * Status Description
     * 
     * @var string
     */
    public $reasonPhrase;

    /**
     * 
     * 
     */
    public function __construct($code, $body = '', $headers = [], $version = '1.1')
    {
        $this->status = $this->isNumericParam($code);
        $this->reasonPhrase = $this->http_status_codes[$this->status];
        $this->protocolVersion = (string) $version;

        $this->setHeaders($headers);
        $this->setBody($body);
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode()
    {
       return $this->status;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return static
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $code = $this->isNumericParam($code);

        if (!array_key_exists($code, $this->http_status_codes)) {
            throw new InvalidArgumentException(sprintf("Invalid response status code. Status code does not exists."));
        }

        if ($this->status == $code) {
            return $this;
        }

        $new = clone $this;
        $new->status = $code;
        $new->reasonPhrase = $this->http_status_codes[$code];

        return $new;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }
}

?>