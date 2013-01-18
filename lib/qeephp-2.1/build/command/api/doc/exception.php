<?php

abstract class API_Doc_Exception extends QException
{
    public $source_file;
    public $line_num;

    function __construct(API_Doc_Abstract $doc, $msg)
    {
        $this->source_file = $doc->filename;
        $this->line_num    = $doc->start_line;
        parent::__construct($msg);
    }

}

