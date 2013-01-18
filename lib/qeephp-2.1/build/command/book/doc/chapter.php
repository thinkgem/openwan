<?php

class Doc_Chapter extends Doc_Abstract
{
    public $book;
    public $sections = array();
    public $has_groups = false;
    public $groups = array();
    protected $_prev = -1;
    protected $_next = -1;

    function __construct(Doc_Book $book, array $chapter, $filename)
    {
        $this->book = $book;
        parent::__construct($chapter['subject'], $book->source_dir, $filename);

        if (!empty($chapter['sections']) && is_array($chapter['sections']))
        {
            foreach ($chapter['sections'] as $sec_filename => $sec_subject)
            {
                if (is_array($sec_subject))
                {
                    $this->has_groups = true;
                    $group_name = trim($sec_filename, '"');
                    $sections_t = $sec_subject;
                    foreach ($sections_t as $sec_filename => $sec_subject)
                    {
                        $sec_filename = $filename . '-' . $sec_filename;
                        $section = new Doc_Section($this, $sec_subject, $sec_filename, $group_name);
                        $this->sections[] = $section;
                        $this->book->sections[] = $section;
                        $this->groups[$group_name][] = $section;
                    }
                }
                else
                {
                    $sec_filename = $filename . '-' . $sec_filename;
                    $section = new Doc_Section($this, $sec_subject, $sec_filename);
                    $this->sections[] = $section;
                    $this->groups['default'][] = $section;
                    $this->book->sections[] = $section;
                }
            }
        }
    }

    function prev()
    {
        if (!is_object($this->_prev) && $this->_prev == -1)
        {
            $prev = null;
            foreach ($this->book->chapters as $chapter)
            {
                if ($chapter->filename == $this->filename) break;
                $prev = $chapter;
            }

            if ($prev)
            {
                // 连接到上一章的最后一个小节
                $c = count($prev->sections) - 1;
                if (isset($prev->sections[$c]))
                {
                    $prev = $prev->sections[$c];
                }
            }

            $this->_prev = $prev;
        }

        return $this->_prev;
    }

    function next()
    {
        if (!is_object($this->_next) && $this->_next == -1)
        {
            // 当前章的第一个小节
            if (isset($this->sections[0]))
            {
                $this->_next = $this->sections[0];
            }
            else
            {
                $chapter = reset($this->book->chapters);
                while ($chapter->filename != $this->filename)
                {
                    $chapter = next($this->book->chapters);
                }
                $this->_next = next($this->book->chapters);

                if ($this->_next)
                {
                    // 连接到下一章的第一个小节
                    if (isset($this->_next->sections[0]))
                    {
                        $this->_next = $this->_next->sections[0];
                    }
                }
            }
        }

        return $this->_next;
    }
}

