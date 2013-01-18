<?php

class API_Doc_UndefinedParameterException extends API_Doc_Exception
{
    public $method_name;
    public $parameter_name;

    function __construct(API_Doc_Abstract $doc, $parameter_name)
    {
        $this->method_name = $doc->name;
        $this->parameter_name = $parameter_name;

        $msg = "Undefined parameter '{$parameter_name}' at '{$doc->filename}[{$doc->start_line}]'.";
        parent::__construct($doc, $msg);
    }
}

