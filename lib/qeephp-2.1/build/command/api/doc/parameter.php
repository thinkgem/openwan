<?php

class API_Doc_Parameter extends API_Doc_Abstract
{
    public $name;

    public $is_passed_by_reference;

    public $allows_null;

    public $is_optional;

    public $is_default_value_available;

    public $position;

    public $type_hint;

    public $doc_comment;

    public $declaring_method;

    function __construct(API_Doc_Method $method, ReflectionParameter $parameter)
    {
        $this->name = $parameter->getName();
        $this->is_passed_by_reference = $parameter->isPassedByReference();
        $this->allows_null = $parameter->allowsNull();
        $this->is_optional = $parameter->isOptional();
        $this->is_default_value_available = $parameter->isDefaultValueAvailable();
        $this->position = $parameter->getPosition();
        $this->declaring_method = $method;
    }
}

