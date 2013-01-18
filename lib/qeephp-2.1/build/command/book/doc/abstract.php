<?php

abstract class Doc_Abstract
{
    public $subject;
    public $source_dir;
    public $filename;
    public $contents;
    public $has_contents;
    public $last_modified;

    function __construct($subject, $source_dir = null, $filename = null)
    {
        $this->subject = $subject;
        $this->source_dir = $source_dir;
        $this->filename = $filename;

        $this->_loadContents();
    }

    function path()
    {
        return rtrim($this->source_dir, '/\\') . "/{$this->filename}.texy";
    }

    function nodename()
    {
        return "node-{$this->filename}";
    }

    protected function _loadContents()
    {
        $path = $this->path();
        if (!is_readable($path))
        {
            echo "\nread file \"{$path}\" failed.";
            $this->contents = '';
        }
        else
        {
            $this->contents = trim(file_get_contents($path));
        }

        if (empty($this->contents))
        {
            $this->has_contents = false;
            $this->last_modified = 0;
        }
        else
        {
            $this->has_contents = true;
            $this->last_modified = filemtime($path);

            $m = null;
            $p = str_replace(array('.', '-'), array('\\.', '\\-'), basename($path));
            $regx = "/\\\$Id: {$p} ([0-9]+) ([0-9]{4,}\-[0-9]{2,}\-[0-9]{2,}) ([0-9]{2,}:[0-9]{2,}:[0-9]{2,})([A-Z]) (.+) \\\$/i";
            if (preg_match($regx, $this->contents, $m))
            {
                $this->last_modified = strtotime("{$m[2]} {$m[3]}");
            }
        }
    }
}

