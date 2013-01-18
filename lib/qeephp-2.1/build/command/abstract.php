<?php

abstract class Command_Abstract
{
    protected $_source_dir;
    protected $_output_dir;
    protected $_docs_dir;
    protected $_excludes;
    protected $_docmode = 'online';
    protected $_img_root = '/upload/docs';
    static protected $_texy_options = array();

    function docmode($mode)
    {
        $this->_docmode = $mode;
        return $this;
    }

    function sourceDir($source_dir)
    {
        $this->_source_dir = rtrim($source_dir, '/\\');
        return $this;
    }

    function outputDir($output_dir)
    {
        $this->_output_dir = rtrim($output_dir, '/\\');
        return $this;
    }

    function docsDir($docs_dir)
    {
        $this->_docs_dir = trim($docs_dir, '/\\');
        return $this;
    }

    function excludes($excludes)
    {
        $this->_excludes = Q::normalize($excludes);
        return $this;
    }

    function imgRoot($root)
    {
        $this->_img_root = rtrim($root, '/\\');
        return $this;
    }

    abstract function execute();

    protected function _displayErrors($errors)
    {
        echo "\n\n";
        foreach ($errors as $ex)
        {
            echo $ex->getMessage();
            echo "\n";
        }

        echo "\n\n";
    }

    protected function _copyAssets($source)
    {
        $sources = array(
            $this->_source_dir . '/_assets',
            dirname(__FILE__) . '/../_assets',
            $source,
        );

        foreach ($sources as $source_dir)
        {
            $dir = realpath($source_dir);
            if (empty($dir)) continue;
            Helper_FileSys::copyDir($dir, $this->_output_dir, array('level' => 1));
        }
    }

    static function formatting($string, $formatter = 'texy')
    {
        switch ($formatter)
        {
        case 'markdown':
            return self::formattingByMarkdown($string);
            break;
        case 'texy':
            return self::formattingByTexy($string);
            break;
        default:
            return nl2br(h($string));
        }
    }

    static function formattingByMarkdown($source)
    {
        if (!function_exists('Markdown'))
        {
            require_once Q::ini('vendor_dir') . '/markdown/markdown.php';
        }

        return Markdown($source);
    }

    static function formattingByTexy($source)
    {
        static $texy;

        if (is_null($texy))
        {
            if (!class_exists('Texy', false))
            {
                $dir = Q::ini('vendor_dir');
                require_once "{$dir}/geshi/geshi.php";
                require_once "{$dir}/texy/texy.php";
            }

            Texy::$advertisingNotice = false;
            $texy = new Texy();
            $options = self::$_texy_options;
            foreach ($options as $module => $config)
            {
                foreach ($config as $key => $value)
                {
                    $m = $module . 'Module';
                    $texy->{$m}->{$key} = $value;
                }
            }

            $texy->addHandler('block', array(__CLASS__, 'texyBlockHandler'));
        }

        return $texy->process($source);
    }

    static function texySetting(array $options)
    {
        self::$_texy_options = $options;
    }

    static function texyBlockHandler($invocation, $blocktype, $content, $lang, $modifier)
    {
        if ($blocktype !== 'block/code')
        {
            return $invocation->proceed();
        }

        $texy = $invocation->getTexy();

        if ($lang == 'html')
        {
            $lang = 'html4strict';
        }
        elseif ($lang == 'yaml')
        {
            $lang = 'python';
        }
        $content = Texy::outdent($content);
        $geshi = new GeSHi($content, $lang);

        // GeSHi could not find the language
        if ($geshi->error)
        {
            return $invocation->proceed();
        }

        // do syntax-highlighting
        $geshi->set_encoding('UTF-8');
        $geshi->set_header_type(GESHI_HEADER_PRE);
        $geshi->enable_classes();
        $geshi->enable_keyword_links(false);
        $geshi->set_overall_style('');
        $geshi->set_overall_class('code');

        // save generated stylesheet
        $content = $geshi->parse_code();

        // check buggy GESHI, it sometimes produce not UTF-8 valid code :-((
        $content = iconv('UTF-8', 'UTF-8//IGNORE', $content);

        // protect output is in HTML
        $content = $texy->protect($content, Texy::CONTENT_BLOCK);

        $el = TexyHtml::el();
        $el->setText($content);
        return $el;
    }
}


