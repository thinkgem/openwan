<?php

class API_Doc_NotSupportedTagException extends API_Doc_Exception
{
    public $tag_name;

    function __construct(API_Doc_Abstract $doc, $tag_name)
    {
        $this->tag_name = $tag_name;
        $msg = "Not supported tag '{$tag_name}' at '{$doc->filename}[{$doc->start_line}]'.";
        parent::__construct($doc, $msg);
    }
}

