<?php

/**
 * API 文档生成命令
 */

class Command_API extends Command_Abstract
{
    static function create()
    {
        return new Command_API();
    }

    function execute()
    {
        if (!is_dir($this->_source_dir))
        {
            throw new Command_Exception("Invalid source dir: \"{$this->_source_dir}\".");
        }

        if (!is_dir($this->_output_dir))
        {
            Helper_FileSys::mkdirs($this->_output_dir);
        }

        echo "processing source codes...";
        $model = $this->_buildModel();
        echo "ok\n";

        $errors = $model->lastErrors();
        if (!empty($errors))
        {
            $this->_displayErrors($errors);
        }

        switch ($this->_docmode)
        {
        case 'online':
            $this->_buildOnlineDocuments($model);
            break;
        case 'offline':
            $this->_buildOfflineDocuments($model);
            break;
        }
    }

    static function classUrl($class, $url)
    {
        $name = 'class-' . str_replace('_', '-', strtolower($class->name));
        return str_replace('%CLASS%', $name, $url);
    }

    static function className($name, $max = -1)
    {
        if ($max < 0) return $name;
        $len = strlen($name);
        if ($len <= $max) return $name;

        return substr($name, 0, $max) . '...';
    }

    protected function _buildModel()
    {
        $options = array(
            'extnames' => 'php',
            'excludes' => $this->_excludes,
        );
        $files = Helper_FileSys::findFiles($this->_source_dir, $options);
        return API_Model::create()->docsDir($this->_docs_dir)->build($files);
    }

    protected function _buildOnlineDocuments(API_Model $model)
    {
        $config = array(
            'view_dir' => dirname(__FILE__) . '/api/view',
        );
        $render = new QView_Render_PHP($config);

        error_reporting(0);

        $sort = array('core', 'mvc', 'orm', 'form', 'database', 'helper', 'cache', 'webcontrols', 'behavior');
        $arr  = $model->packages;
        $packages = array();
        foreach ($sort as $name)
        {
            $packages[$name] = $arr[$name];
            unset($arr[$name]);
        }
        $packages = array_merge($packages, $arr);

        $render->assign('packages', $packages);
        $render->assign('classes', $model->classes);
        $render->assign('class_url', '/docs/qeephp-api/%CLASS%');
        $render->assign('index_url', '/docs/qeephp-api/');

        foreach ($model->classes as $class)
        {
            $render->assign('class', $class);
            $contents = $render->fetch('class');
            $filename = 'class-' . str_replace('_', '-', strtolower($class->name)) . '.php';
            echo "write file \"{$filename}\"...\n";
            file_put_contents($this->_output_dir . DS . $filename, $contents);
        }

        // 生成索引页
        $contents = $render->fetch('index');
        $filename = 'index.php';
        echo "write file \"{$filename}\"...\n";
        file_put_contents($this->_output_dir . DS . $filename, $contents);

        // 生成内部索引页
        $contents = $render->fetch('book-index');
        $filename = 'book-index.php';
        echo "write file \"{$filename}\"...\n";
        file_put_contents($this->_output_dir . DS . $filename, $contents);

        // 复制附属文件
        $this->_copyAssets(dirname(__FILE__) . '/api/assets');
	}

}

