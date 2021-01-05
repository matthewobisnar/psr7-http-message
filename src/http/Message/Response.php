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
    public const INFORMATIONAL_CONTINUE                     = 100;
    public const INFORMATIONAL_SWITCHING_PROTOCOLS          = 101;
    public const INFORMATIONAL_PROCESSING                   = 102;
    public const SUCCESSFUL_OK                              = 200;
    public const SUCCESSFUL_CREATED                         = 201;
    public const SUCCESSFUL_ACCEPTED                        = 202;
    public const SUCCESSFUL_NON_AUTHORITATIVE_INFORMATION   = 203;
    public const SUCCESSFUL_NO_CONTENT                      = 204;
    public const SUCCESSFUL_RESET_CONTENT                   = 205;
    public const SUCCESSFUL_PARTIAL_CONTENT                 = 206;
    public const SUCCESSFUL_MULTI_STATUS                    = 207;
    public const SUCCESSFUL_ALREADY_REPORTED                = 208;
    public const SUCCESSFUL_IM_USED                         = 226;
    public const REDIRECTION_MULTIPLE_CHOICES               = 300;
    public const REDIRECTION_MOVED_PERMANENTLY              = 301;
    public const REDIRECTION_FOUND                          = 302;
    public const REDIRECTION_SEE_OTHER                      = 303;
    public const REDIRECTION_NOT_MODIFIED                   = 304;
    public const REDIRECTION_USE_PROXY                      = 305;
    public const REDIRECTION_SWITCH_PROXY                   = 306;
    public const REDIRECTION_TEMPORARY_REDIRECT             = 307;
    public const REDIRECTION_PERMANENT_REDIRECT             = 308;
    public const CLIENT_ERROR_BAD_REQUEST                   = 400;
    public const CLIENT_ERROR_UNAUTHORIZED                  = 401;
    public const CLIENT_ERROR_PAYMENT_D                     = 402;
    public const CLIENT_ERROR_FORBIDDEN                     = 403;
    public const CLIENT_ERROR_NOT_FOUND                     = 404;
    public const CLIENT_ERROR_METHOD_NOT_ALLOWED            = 405;
    public const CLIENT_ERROR_NOT_ACCEPTABLE                = 406;
    public const CLIENT_ERROR_PROXY_AUTHENTICATION_D        = 407;
    public const CLIENT_ERROR_REQUEST_TIMEOUT               = 408;
    public const CLIENT_ERROR_CONFLICT                      = 409;
    public const CLIENT_ERROR_GONE                          = 410;
    public const CLIENT_ERROR_LENGTH_D                      = 411;
    public const CLIENT_ERROR_PRECONDITION_FAILED           = 412;
    public const CLIENT_ERROR_REQUEST_ENTITY_TOO_LARGE      = 413;
    public const CLIENT_ERROR_REQUEST_URI_TOO_LONG            = 414;
    public const CLIENT_ERROR_UNSUPPORTED_MEDIA_TYPE          = 415;
    public const CLIENT_ERROR_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const CLIENT_ERROR_EXPECTATION_FAILED              = 417;
    public const CLIENT_ERROR_IM_A_TEAPOT                     = 418;
    public const CLIENT_ERROR_AUTHENTICATION_TIMEOUT          = 419;
    public const CLIENT_ERROR_METHOD_FAILURE                  = 420;
    public const CLIENT_ERROR_UNPROCESSABLE_ENTITY            = 422;
    public const CLIENT_ERROR_LOCKED                          = 423;
    public const CLIENT_ERROR_METHOD_FAILURE2                 = 424;
    public const CLIENT_ERROR_UNORDERED_COLLECTION            = 425;
    public const CLIENT_ERROR_UPGRADE_D                       = 426;
    public const CLIENT_ERROR_PRECONDITION_D                  = 428;
    public const CLIENT_ERROR_TOO_MANY_REQUESTS               = 429;
    public const CLIENT_ERROR_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    public const CLIENT_ERROR_NO_RESPONSE                     = 444;
    public const CLIENT_ERROR_RETRY_WITH                      = 449;
    public const CLIENT_ERROR_BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450;
    public const CLIENT_ERROR_UNAVAILABLE_FOR_LEGAL_REASONS   = 451;
    public const CLIENT_ERROR_REQUEST_HEADER_TOO_LARGE        = 494;
    public const CLIENT_ERROR_CERT_ERROR                      = 495;
    public const CLIENT_ERROR_NO_CERT                         = 496;
    public const CLIENT_ERROR_HTTP_TO_HTTPS                   = 497;
    public const CLIENT_ERROR_CLIENT_CLOSED_REQUEST           = 499;
    public const SERVER_ERROR_INTERNAL_SERVER_ERROR           = 500;
    public const SERVER_ERROR_NOT_IMPLEMENTED                 = 501;
    public const SERVER_ERROR_BAD_GATEWAY                     = 502;
    public const SERVER_ERROR_SERVICE_UNAVAILABLE             = 503;
    public const SERVER_ERROR_GATEWAY_TIMEOUT                 = 504;
    public const SERVER_ERROR_HTTP_VERSION_NOT_SUPPORTED      = 505;
    public const SERVER_ERROR_VARIANT_ALSO_NEGOTIATES         = 506;
    public const SERVER_ERROR_INSUFFICIENT_STORAGE            = 507;
    public const SERVER_ERROR_LOOP_DETECTED                   = 508;
    public const SERVER_ERROR_BANDWIDTH_LIMIT_EXCEEDED        = 509;
    public const SERVER_ERROR_NOT_EXTENDED                    = 510;
    public const SERVER_ERROR_NETWORK_AUTHENTICATION_D        = 511;
    public const SERVER_ERROR_NETWORK_READ_TIMEOUT_ERROR      = 598;
    public const SERVER_ERROR_NETWORK_CONNECT_TIMEOUT_ERROR   = 599;

     /**
     * Response status code and descriptions
     * 
     * @var array
     */
    private $http_status_codes = array(
        self::INFORMATIONAL_CONTINUE => 'Informational: Continue',
        self::INFORMATIONAL_SWITCHING_PROTOCOLS => 'Informational: Switching Protocols',
        self::INFORMATIONAL_PROCESSING => 'Informational: Processing',
        self::SUCCESSFUL_OK => 'Successful: OK',
        self::SUCCESSFUL_CREATED => 'Successful: Created',
        self::SUCCESSFUL_ACCEPTED => 'Successful: Accepted',
        self::SUCCESSFUL_NON_AUTHORITATIVE_INFORMATION => 'Successful: Non-Authoritative Information',
        self::SUCCESSFUL_NO_CONTENT => 'Successful: No Content',
        self::SUCCESSFUL_RESET_CONTENT => 'Successful: Reset Content',
        self::SUCCESSFUL_PARTIAL_CONTENT => 'Successful: Partial Content',
        self::SUCCESSFUL_MULTI_STATUS => 'Successful: Multi-Status',
        self::SUCCESSFUL_ALREADY_REPORTED => 'Successful: Already Reported',
        self::SUCCESSFUL_IM_USED => 'Successful: IM Used',
        self::REDIRECTION_MULTIPLE_CHOICES => 'Redirection: Multiple Choices',
        self::REDIRECTION_MOVED_PERMANENTLY => 'Redirection: Moved Permanently',
        self::REDIRECTION_FOUND => 'Redirection: Found',
        self::REDIRECTION_SEE_OTHER => 'Redirection: See Other',
        self::REDIRECTION_NOT_MODIFIED => 'Redirection: Not Modified',
        self::REDIRECTION_USE_PROXY => 'Redirection: Use Proxy',
        self::REDIRECTION_SWITCH_PROXY => 'Redirection: Switch Proxy',
        self::REDIRECTION_TEMPORARY_REDIRECT => 'Redirection: Temporary Redirect',
        self::REDIRECTION_PERMANENT_REDIRECT => 'Redirection: Permanent Redirect',
        self::CLIENT_ERROR_BAD_REQUEST => 'Client Error: Bad Request',
        self::CLIENT_ERROR_UNAUTHORIZED => 'Client Error: Unauthorized',
        self::CLIENT_ERROR_PAYMENT_D => 'Client Error: Payment d',
        self::CLIENT_ERROR_FORBIDDEN => 'Client Error: Forbidden',
        self::CLIENT_ERROR_NOT_FOUND => 'Client Error: Not Found',
        self::CLIENT_ERROR_METHOD_NOT_ALLOWED => 'Client Error: Method Not Allowed',
        self::CLIENT_ERROR_NOT_ACCEPTABLE => 'Client Error: Not Acceptable',
        self::CLIENT_ERROR_PROXY_AUTHENTICATION_D => 'Client Error: Proxy Authentication d',
        self::CLIENT_ERROR_REQUEST_TIMEOUT => 'Client Error: Request Timeout',
        self::CLIENT_ERROR_CONFLICT => 'Client Error: Conflict',
        self::CLIENT_ERROR_GONE => 'Client Error: Gone',
        self::CLIENT_ERROR_LENGTH_D => 'Client Error: Length d',
        self::CLIENT_ERROR_PRECONDITION_FAILED => 'Client Error: Precondition Failed',
        self::CLIENT_ERROR_REQUEST_ENTITY_TOO_LARGE => 'Client Error: Request Entity Too Large',
        self::CLIENT_ERROR_REQUEST_URI_TOO_LONG => 'Client Error: Request-URI Too Long',
        self::CLIENT_ERROR_UNSUPPORTED_MEDIA_TYPE => 'Client Error: Unsupported Media Type',
        self::CLIENT_ERROR_REQUESTED_RANGE_NOT_SATISFIABLE => 'Client Error: Requested Range Not Satisfiable',
        self::CLIENT_ERROR_EXPECTATION_FAILED => 'Client Error: Expectation Failed',
        self::CLIENT_ERROR_IM_A_TEAPOT => 'Client Error: Im a teapot',
        self::CLIENT_ERROR_AUTHENTICATION_TIMEOUT => 'Client Error: Authentication Timeout',
        self::CLIENT_ERROR_METHOD_FAILURE => 'Client Error: Method Failure',
        self::CLIENT_ERROR_UNPROCESSABLE_ENTITY => 'Client Error: Unprocessable Entity',
        self::CLIENT_ERROR_LOCKED => 'Client Error: Locked',
        self::CLIENT_ERROR_METHOD_FAILURE => 'Client Error: Method Failure',
        self::CLIENT_ERROR_UNORDERED_COLLECTION => 'Client Error: Unordered Collection',
        self::CLIENT_ERROR_UPGRADE_D => 'Client Error: Upgrade d',
        self::CLIENT_ERROR_PRECONDITION_D => 'Client Error: Precondition d',
        self::CLIENT_ERROR_TOO_MANY_REQUESTS => 'Client Error: Too Many Requests',
        self::CLIENT_ERROR_REQUEST_HEADER_FIELDS_TOO_LARGE => 'Client Error: Request Header Fields Too Large',
        self::CLIENT_ERROR_NO_RESPONSE => 'Client Error: No Response',
        self::CLIENT_ERROR_RETRY_WITH => 'Client Error: Retry With',
        self::CLIENT_ERROR_BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS => 'Client Error: Blocked by Windows Parental Controls',
        self::CLIENT_ERROR_UNAVAILABLE_FOR_LEGAL_REASONS => 'Client Error: Unavailable For Legal Reasons',
        self::CLIENT_ERROR_REQUEST_HEADER_TOO_LARGE => 'Client Error: Request Header Too Large',
        self::CLIENT_ERROR_CERT_ERROR => 'Client Error: Cert Error',
        self::CLIENT_ERROR_NO_CERT => 'Client Error: No Cert',
        self::CLIENT_ERROR_HTTP_TO_HTTPS => 'Client Error: HTTP to HTTPS',
        self::CLIENT_ERROR_CLIENT_CLOSED_REQUEST => 'Client Error: Client Closed Request',
        self::SERVER_ERROR_INTERNAL_SERVER_ERROR => 'Server Error: Internal Server Error',
        self::SERVER_ERROR_NOT_IMPLEMENTED => 'Server Error: Not Implemented',
        self::SERVER_ERROR_BAD_GATEWAY => 'Server Error: Bad Gateway',
        self::SERVER_ERROR_SERVICE_UNAVAILABLE => 'Server Error: Service Unavailable',
        self::SERVER_ERROR_GATEWAY_TIMEOUT => 'Server Error: Gateway Timeout',
        self::SERVER_ERROR_HTTP_VERSION_NOT_SUPPORTED => 'Server Error: HTTP Version Not Supported',
        self::SERVER_ERROR_VARIANT_ALSO_NEGOTIATES => 'Server Error: Variant Also Negotiates',
        self::SERVER_ERROR_INSUFFICIENT_STORAGE => 'Server Error: Insufficient Storage',
        self::SERVER_ERROR_LOOP_DETECTED => 'Server Error: Loop Detected',
        self::SERVER_ERROR_BANDWIDTH_LIMIT_EXCEEDED => 'Server Error: Bandwidth Limit Exceeded',
        self::SERVER_ERROR_NOT_EXTENDED => 'Server Error: Not Extended',
        self::SERVER_ERROR_NETWORK_AUTHENTICATION_D => 'Server Error: Network Authentication d',
        self::SERVER_ERROR_NETWORK_READ_TIMEOUT_ERROR => 'Server Error: Network read timeout error',
        self::SERVER_ERROR_NETWORK_CONNECT_TIMEOUT_ERROR => 'Server Error: Network connect timeout error'
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

        $code = $this->isNumericParam($code);

        if (!in_array($code, array_keys($this->http_status_codes))) {
            throw new InvalidArgumentException(sprintf("%s does not exists in http_status_code.", $code));
        }
        
        $this->status = $code;
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