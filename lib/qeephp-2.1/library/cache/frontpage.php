<?php

class QCache_FrontPage
{
    protected $_backend;
    protected $_cache_uri;
    protected $_enabled;

    function __construct($backend_cache)
    {
        $this->_backend = $backend_cache;
        $method = (!empty($_SERVER['REQUEST_METHOD'])) ? strtolower($_SERVER['REQUEST_METHOD']) : 'get';
        $this->_enabled = ($method == 'get');
    }

    function __destruct()
    {
        if ($this->_enabled && $this->_cache_uri)
        {
            $this->_backend->set('frontpage-' . $this->_cache_uri, ob_get_flush());
        }
    }

    function get()
    {
        if ($this->_enabled)
        {
            $uri = 'frontpage-' . $_SERVER['REQUEST_URI'];
            return $this->_backend->get($uri);
        }
        return false;
    }

    function caching($uri = null)
    {
        if ($this->_enabled)
        {
            if ($uri)
            {
                $this->_cache_uri = $uri;
            }
            else
            {
                $this->_cache_uri = $_SERVER['REQUEST_URI'];
            }
            ob_start();
        }
        return $this;
    }
}

