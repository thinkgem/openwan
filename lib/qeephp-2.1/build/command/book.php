<?php

class Command_Book extends Command_Abstract
{
    static function create()
    {
        return new Command_Book();
    }

    function execute()
    {
        if (!is_dir($this->_source_dir))
        {
            throw new Command_Exception("Invalid source dir: \"{$this->_source_dir}\".");
        }

        echo "processing source documents...";
        $book = new Doc_Book(Helper_YAML::load("{$this->_source_dir}/toc.yaml"), $this->_source_dir);
        echo "ok\n";

        $this->_output_dir = "{$this->_output_dir}/{$book->name}";
        if (!is_dir($this->_output_dir))
        {
            Helper_FileSys::mkdirs($this->_output_dir);
        }
        $this->_output_dir = trim(realpath($this->_output_dir), '/\\');

        switch ($this->_docmode)
        {
        case 'online':
            $this->_buildOnlineDocuments($book);
            break;
        case 'chm':
            $this->_buildCHMDocuments($book);
            break;
        case 'pdf':
            $this->_buildPDFDocuments($book);
            break;
        case 'html':
        default:
            exit ('invalid docmode:' . $this->_docmode);
        }
    }

    protected function _buildPDFDocuments(Doc_Book $book)
    {
        set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../_vendor/zf');
        $pdf = new Zend_Pdf();

        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $font = Zend_Pdf_Font::fontWithPath('c:\\windows\\fonts\\simkai.ttf');
        $page->setFont($font, 12);
        $pdf->pages[] = $page;

        $page->drawText('中文测试', 100, 430, 'UTF-8');

        $pdf->save('output.pdf');
    }

    protected function _buildCHMDocuments(Doc_Book $book)
    {
        $templates = array(
            'chapter'    => 'chm_chapter',
            'section'    => 'chm_section',
            'index'      => 'chm_index',
            'book-index' => '',
        );
        $files = $this->_buildOnlineDocuments($book, '.html', $templates);

        $this->_copyAssets(dirname(__FILE__) . '/book/_assets');
        $this->_copyAssets(dirname(__FILE__) . '/../_assets');

        $config = array('view_dir' => dirname(__FILE__) . '/book/view');
        $render = new QView_Render_PHP($config);
        $render->assign('book', $book);

        $path = "{$book->name}.hhc";
        $this->_buildPage($render, 'chm_hhc', $path);
        $files[] = $path;

        $path = "{$book->name}.hhp";
        $this->_buildPage($render, 'chm_hhp', $path);
        $files[] = $path;

        $t = str_replace(array('/', '.', '-'), array('\\/', '\\.', '\\-'), $book->url($book));
        $href_regx = '/href="' . $t . '([a-z0-9\-]+)?"/i';
        $img_regx = '/src=".+\/images\//i';
        $replace = 'href="\1.html"';
        foreach ($files as $filename)
        {
            $path = "{$this->_output_dir}/{$filename}";
            $contents = preg_replace($href_regx, $replace, file_get_contents($path));
            $contents = str_replace(
                array('href=".html"'),
                array('href="index.html"'),
                $contents);
            $contents = preg_replace($img_regx, 'src="images/', $contents);
            file_put_contents($path, $contents, LOCK_EX);
        }

        $names = array("{$book->name}.hhc", "{$book->name}.hhp");
        foreach ($names as $filename)
        {
            $path = "{$this->_output_dir}/{$filename}";
            $contents = iconv('UTF-8', 'GB2312//TRANSLIT', file_get_contents($path));
            file_put_contents($path, $contents, LOCK_EX);
        }
    }

    protected function _buildOnlineDocuments(Doc_Book $book, $extname = '.php', array $templates = null)
    {
        $default_templates = array(
            'chapter'    => 'chapter',
            'section'    => 'section',
            'index'      => 'index',
            'book-index' => 'book-index',
        );
        if (is_array($templates))
        {
            $templates = array_merge($default_templates, $templates);
        }
        else
        {
            $templates = $default_templates;
        }

        // 初始化
        $config = array('view_dir' => dirname(__FILE__) . '/book/view');
        $render = new QView_Render_PHP($config);
        $render->assign('book', $book);

        self::texySetting(array(
            'image' => array(
                'root'       => $this->_img_root . "/{$book->name}/images",
                'linkedRoot' => $this->_img_root . "/{$book->name}/images",
            ),
        ));

        // 生成索引页
        $files = array();
        $path = "index{$extname}";
        $this->_buildPage($render, $templates['index'], $path);
        $files[] = $path;

        // 生成内部索引页
        if (!empty($templates['book-index']))
        {
            $files = array();
            $path = "book-index{$extname}";
            $this->_buildPage($render, $templates['book-index'], $path);
            $files[] = $path;
        }

        // 生成各个章节的页面
        foreach ($book->chapters as $chapter)
        {
            $path = $chapter->nodename() . $extname;
            $this->_buildPage($render, $templates['chapter'], $path, array('chapter' => $chapter));
            $files[] = $path;

            foreach ($chapter->sections as $section)
            {
                $path = $section->nodename() . $extname;
                $this->_buildPage($render, $templates['section'], $path, array('section' => $section));
                $files[] = $path;
            }
        }

        // 复制附属文件
        $this->_copyAssets($this->_source_dir . '/assets');
        return $files;
    }

    protected function _buildPage($render, $viewname, $filename, array $vars = array())
    {
        $render->assign($vars);
        $error = error_reporting(0);
        $contents = $render->fetch($viewname);
        error_reporting($error);
        $filename = "{$this->_output_dir}/{$filename}";
        echo "write file \"{$filename}\"...\n";
        file_put_contents($filename, $contents, LOCK_EX);
	}
}

