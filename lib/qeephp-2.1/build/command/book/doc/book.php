<?php

class Doc_Book extends Doc_Abstract
{
    public $chapters = array();
    public $sections = array();
    public $columns  = 4;
    public $name;

    function __construct(array $book, $source_dir)
    {
        parent::__construct($book['subject'], $source_dir, 'cover');
        $this->name = $book['name'];
        if (isset($book['columns']))
        {
            $this->columns = $book['columns'];
        }

        foreach ($book['chapters'] as $filename => $chapter)
        {
            $this->chapters[] = new Doc_Chapter($this, $chapter, $filename);
        }
    }

    function url($node)
    {
        $url = "/docs/{$this->name}/";
        return ($node == $this) ? $url : "{$url}node-{$node->filename}";
    }
}


