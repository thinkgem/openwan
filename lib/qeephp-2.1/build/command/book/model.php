<?php

class Book_Model
{
    /**
     * @var Doc_Book
     */
    public $book = array();

    static function create()
    {
        return new Book_Model();
    }

    function build($source_dir)
    {
        $source_dir = rtrim($source_dir, '/\\');
        $this->book = new Guide_Doc_Book(Helper_YAML::load("{$source_dir}/toc.yaml"), $source_dir);
        return $this;
    }
}


