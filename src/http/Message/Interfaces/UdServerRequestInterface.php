<?php
namespace Http\Message\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

interface UdServerRequestInterface extends ServerRequestInterface
{
    /**
     * 
     * @param string
     * @return array
     */
    public function getQuery($name);
} 