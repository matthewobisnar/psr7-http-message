<?php
namespace Http\Message;

use Psr\Http\Message\UriInterface;
use Http\Message\Traits\UtilitiesTraits;

/**
 * Value object representing a URI.
 *
 * This interface is meant to represent URIs according to RFC 3986 and to
 * provide methods for most common operations. Additional functionality for
 * working with URIs can be provided on top of the interface or externally.
 * Its primary use is for HTTP requests, but may also be used in other
 * contexts.
 *
 * Instances of this interface are considered immutable; all methods that
 * might change state MUST be implemented such that they retain the internal
 * state of the current instance and return an instance that contains the
 * changed state.
 *
 * Typically the Host header will be also be present in the request message.
 * For server-side requests, the scheme will typically be discoverable in the
 * server parameters.
 *
 * @link http://tools.ietf.org/html/rfc3986 (the URI specification)
 */
class Uri implements UriInterface
{
    /**
     * Generic methods
     * 
     * @type traits
     */
    use UtilitiesTraits;

    /**
     * Parse_url constants
     * 
     * @var array constant
     */
    private const PARSE_URL_CONSTANTS = [
       "PHP_URL_SCHEME"   => PHP_URL_SCHEME,
       "PHP_URL_USER"     => PHP_URL_USER,
       "PHP_URL_PASS"     => PHP_URL_PASS,
       "PHP_URL_HOST"     => PHP_URL_HOST,
       "PHP_URL_PORT"     => PHP_URL_PORT,
       "PHP_URL_PATH"     => PHP_URL_PATH,
       "PHP_URL_QUERY"    => PHP_URL_QUERY,
       "PHP_URL_FRAGMENT" => PHP_URL_FRAGMENT
    ];
    
    /**
     * Default port.
     * 
     * @var array port => scheme
     */
    private const DEFAULT_PORT = [
        80   => "http", 
        8080 => "http", 
        443  => "https",
        21   => "ftp"
    ];

    /**
     * Unreserved characters for use in a regex.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-2.3
     */
    private const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';

    /**
     * Sub-delims for use in a regex.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-2.2
     */
    private const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    /**
     * Hold parts of url parse from parse_url method.
     * 
     * php_url_scheme   => holds http|https of  url.
     * php_url_user     => holds userInfo of url.
     * php_url_pass     => 
     * php_url_host     => holds host of the url.
     * php_url_port     => holds url port 80|443.
     * php_url_path     => holds url path.
     * php_url_query    => holds query string.
     * php_url_fragment => holds #fragment of url.
     * 
     * @var string(s)|null|int
     */

    public $php_url_scheme   = '';
    public $php_url_user     = '';
    public $php_url_pass     = '';
    public $php_url_host     = '';
    public $php_url_port     = 80;
    public $php_url_path     = '';
    public $php_url_query    = '';
    public $php_url_fragment = '';

    /**
     * Receives optional string url values every instance.
     * 
     * @param string
     * @return void
     */
    public function __construct(string $url = '')
    {
        if (!empty($url)) {
            if (filter_var($url, FILTER_VALIDATE_DOMAIN)) {
                foreach (self::PARSE_URL_CONSTANTS as $key_name => $url_constant) {

                    if (is_int($this->{strtolower($key_name)})) {
                   
                        $this->{strtolower($key_name)} = (parse_url($url, $url_constant) ?? null);
                    
                    } elseif (is_string($this->{strtolower($key_name)})) {
                   
                        if ($key_name === 'PHP_URL_PASS') {
                            $this->{strtolower($key_name)} = (parse_url($url, $url_constant) ?? '');
                        } else {
                            $this->{strtolower($key_name)} = (strtolower(parse_url($url, $url_constant)) ?? '');
                        }
                   
                    }

                }        
            } else {
                throw new \Exception(
                    sprintf("[ %s ] => Invalid domain passed in %s", 
                        $url, static::class
                    )
                );
            }
        }
    }

    /**
     * Get public access list of parsed url
     * 
     * @param void
     * @return array parse_url method.
     */
    public function getUrlComponents()
    {
        $components = [];
        
        foreach (self::PARSE_URL_CONSTANTS as $key => $names) {
            $components[strtoupper($key)] = $this->{strtolower($key)};
        }

        return $components;
    }

    /**
     * Filters the path of a URI
     *
     * @param mixed $path
     *
     * @return string rawurlencoded path.
     * @throws \Excpetion If the path is invalid.
     */
    protected function filterPath($path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException(sprintf("[ %s ] path must be type string. ( %s ) is given in %s",
                    json_encode($path), gettype($path), __METHOD__ 
                )
            );
        }

        return preg_replace_callback(
            '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $path
        );
    }

    /**
     * Filters the path of a URI
     *
     * @param mixed $path
     *
     * @return string rawurlencoded path.
     * @throws \Excpetion If the path is invalid.
     */
    protected function filterQueryAndFragment($path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException(sprintf("[ %s ] path must be type string. ( %s ) is given in %s",
                    json_encode($path), gettype($path), __METHOD__ 
                )
            );
        }

        return preg_replace_callback(
            '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'urlencodeMatchZero'],
            $path
        );
    }

    /**
     * Filters the path of a URI
     *
     * @param mixed $path
     * @return string rawurlencoded path.
     */
    protected function rawurlencodeMatchZero ($matches) 
    {
        return rawurlencode($matches[0]);
    }

    /**
     * Filters the query
     *
     * @param mixed $query
     * @return string rawurlencoded path.
     */
    protected function urlencodeMatchZero ($matches) 
    {
        return urlencode($matches[0]);
    }

    /**
     * Retrieve the scheme component of the URI.
     *
     * If no scheme is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.1.
     *
     * The trailing ":" character is not part of the scheme and MUST NOT be
     * added.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string The URI scheme.
     */
    public function getScheme()
    {
        return $this->php_url_scheme;
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * If no authority information is present, this method MUST return an empty
     * string.
     *
     * The authority syntax of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        $authority = "";
        $host = preg_replace ('/^www\./i', '', $this->getHost());
        
        if (!empty($this->getUserInfo())) {
            $authority .= $this->getUserInfo() . "@" . $host; 
        } else {
            $authority .= $host;
        }

        if (!is_null($this->getPort())) {
            $authority .= ":" . $this->getPort();
        }

        return $authority;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * If no user information is present, this method MUST return an empty
     * string.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        $userInfo = "";

        if (!empty($this->php_url_user)) {
            $userInfo = $this->php_url_user . (!empty($this->php_url_pass) ? ":" . $this->php_url_pass : "");
        }

        return $userInfo;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * If no host is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     */
    public function getHost()
    {
        return strtolower($this->php_url_host);
    }

    /**
     * Retrieve the port component of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The URI port.
     */
    public function getPort()
    {
        $is_default_to_current_scheme = array_search($this->php_url_scheme, self::DEFAULT_PORT);

        if ($is_default_to_current_scheme == $this->php_url_port) {
            return null;
        }

       return $this->php_url_port;
    }

    /**
     * Retrieve the path component of the URI.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * Normally, the empty path "" and absolute path "/" are considered equal as
     * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
     * do this normalization because in contexts with a trimmed base path, e.g.
     * the front controller, this difference becomes significant. It's the task
     * of the user to handle both "" and "/".
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.3.
     *
     * As an example, if the value should include a slash ("/") not intended as
     * delimiter between path segments, that value MUST be passed in encoded
     * form (e.g., "%2F") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string The URI path.
     */
    public function getPath()
    {
       $path = preg_replace('/^[\/]*/', "/", $this->php_url_path);
       return $path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * If no query string is present, this method MUST return an empty string.
     *
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     *
     * As an example, if a value in a key/value pair of the query string should
     * include an ampersand ("&") not intended as a delimiter between values,
     * that value MUST be passed in encoded form (e.g., "%26") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     */
    public function getQuery()
    {
        return $this->php_url_query;
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * If no fragment is present, this method MUST return an empty string.
     *
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.5.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        return $this->php_url_fragment;
    }

    /**
     * Return an instance with the specified scheme.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified scheme.
     *
     * Implementations MUST support the schemes "http" and "https" case
     * insensitively, and MAY accommodate other schemes if required.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return static A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme) : self
    {
        if (!empty($scheme)) {

            if ($this->php_url_scheme === strtolower($scheme)) {
                return $this;
            }

            if (in_array(strtolower($scheme), self::DEFAULT_PORT)) {
                $scheme = strtolower($scheme);
            } else {
                throw new \InvalidArgumentException(
                        sprintf("[ %s ] is not a valid url scheme in function %s", 
                        $scheme, __METHOD__
                    )
                );
            }
        }

        $new = clone $this;
        $new->php_url_scheme = $scheme;
        return $new;
    }

    /**
     * Return an instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * @return static A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null) : self
    {
        if ($this->php_url_user == $user 
            && $this->php_url_password == $password) {
            return $this;
        }
        
        $new = clone $this;
        $new->php_url_user = $user;
        $new->php_url_password = $password;
        return $new;
    }

    /**
     * Return an instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     * @return static A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host) :self
    {

        if (!empty($this->requiredString($host))) {

            if ($this->php_url_host === strtolower($host)) {
                return $this;
            }

            if (filter_var(gethostbyname($host), FILTER_VALIDATE_IP)) {
                $host = strtolower($host);
            } else {
                throw new \InvalidArgumentException
                (sprintf("[ %s ] is invalid hostname.", $host));
            }

        }

        $new = clone $this;
        $new->php_url_host = $host;
        return $new;
    
    }

    /**
     * Return an instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     * @return static A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port) : self
    {
        if (!is_null($port)) {
            
            if (is_numeric($port)) {
                $port = $this->convertToInt($port);
            }

            $port = $this->requiredInt($port);

            if ($this->php_url_port === strtolower($port)) {
                return $this;
            }
        }

        $new = clone $this;
        $new->php_url_port = is_null($port) ? null : $port;
        return $new;
    }

    /**
     * Return an instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified path.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path The path to use with the new instance.
     * @return static A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path) : self
    {
        if (!empty($path)) {
            $path = $this->filterPath($path);

            if ($this->php_url_path === strtolower($path)) {
                return $this;
            }
        }

        $new = clone $this;
        $new->php_url_path = rawurldecode($path);
        return $new;
    }

    /**
     * Return an instance with the specified query string.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified query string.
     *
     * Users can provide both encoded and decoded query characters.
     * Implementations ensure the correct encoding as outlined in getQuery().
     *
     * An empty query string value is equivalent to removing the query string.
     *
     * @param string $query The query string to use with the new instance.
     * @return static A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query) : self
    {
        if (!empty($query)) {
            $query = $this->filterQueryAndFragment($query);

            if ($this->getQuery() === strtolower($query)) {
                return $this;
            }
        }
        
        $new = clone $this;
        $new->php_url_query = urldecode($query);
        return $new;
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified URI fragment.
     *
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in getFragment().
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * @return static A new instance with the specified fragment.
     */
    public function withFragment($fragment) : self
    {
        if (!empty($fragment)) {
            $fragment = $this->filterQueryAndFragment($fragment);

            if ($this->php_url_fragment === strtolower($fragment)) {
                return $this;
            }
        }

        $new = clone $this;
        $new->php_url_fragment = urldecode($fragment);
        return $new;
    }

    /**
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString()
    {
       return $this->getUriString();
    }

    /**
     * {@inheritdoc}
     * 
     * @param void
     * @return string
     */
    public function getUriString()
    {
        $uri = '';

        if (!empty($this->getScheme())) {
            $uri .= $this->getScheme() . ":";
        }

        if (!empty($this->getAuthority())) {

            if (!empty($this->getScheme())) {
                $uri .= "//" . $this->getAuthority();
            } else {
                $uri .= $this->getAuthority();
            }
            
        } else {

            if (!empty($this->getScheme())) {
                $uri .= "//" . $this->getHost();
            } else {
                $uri .= $this->getHost();
            }

        }

        if (!empty($this->getPath())) {
            $uri .= preg_replace('/^\/+/', '/', $this->getPath());
        }
       
        if (!empty($this->getQuery())) {
            $uri .= "?" . $this->getQuery();
        }

        if (!empty($this->getFragment())) {
            $uri .= "#" . $this->getFragment();
        }

        return $uri;
    }
}