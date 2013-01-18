<?php

class Doc_Section extends Doc_Abstract
{
    public $chapter;
    public $group_name;
    protected $_prev = -1;
    protected $_next = -1;

    function __construct(Doc_Chapter $chapter, $subject, $filename, $group_name = '')
    {
        $this->chapter  = $chapter;
        $this->group_name = trim($group_name, '"');
        parent::__construct($subject, $chapter->source_dir, $filename);
    }

    function prev()
    {
        if (!is_object($this->_prev) && $this->_prev == -1)
        {
            $prev = null;
            foreach ($this->chapter->sections as $section)
            {
                if ($section->filename == $this->filename) break;
                $prev = $section;
            }

            if (is_null($prev))
            {
                // 如果当前章没找到上一个小节，则连接到当前章的首页
                $prev = $this->chapter;
            }

            $this->_prev = $prev;
        }

        return $this->_prev;
    }

    function next()
    {
        if (!is_object($this->_next) && $this->_next == -1)
        {
            $section = reset($this->chapter->sections);
            while ($section->filename != $this->filename)
            {
                $section = next($this->chapter->sections);
            }
            $this->_next = next($this->chapter->sections);

            if (!$this->_next)
            {
                // 当前章已经没有下一个小节了，寻找下一章
                $chapter = reset($this->chapter->book->chapters);
                while ($chapter->filename != $this->chapter->filename)
                {
                    $chapter = next($this->chapter->book->chapters);
                }
                $this->_next = next($this->chapter->book->chapters);
            }
        }

        return $this->_next;
    }
}

