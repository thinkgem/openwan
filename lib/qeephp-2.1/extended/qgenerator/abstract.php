<?php

abstract class QGenerator_Abstract
{
    /**
     * 日志
     *
     * @var array of log
     */
    protected $_log = array();

    /**
     * 该生成器所述的应用程序模块反射
     *
     * @var QReflection_Module
     */
    protected $_module;

    /**
     * 构造函数
     *
     * @param QReflection_Module $module
     */
    function __construct(QReflection_Module $module)
    {
        $this->_module = $module;
    }

    /**
     * 返回日志信息
     *
     * @return array of log
     */
    function log()
    {
        return $this->_log;
    }

    /**
     * 追加日志信息
     */
    protected function _log()
    {
        $args = func_get_args();
        $this->_log[] = call_user_func_array('sprintf', $args);
    }

    /**
     * 清除日志信息
     */
    protected function _logClean()
    {
        $this->_log = array();
    }

    /**
     * 格式化类名称，确保每个词首字母都是大写
     *
     * @param string $class_name
     *
     * @return string
     */
    protected function _normalizeClassName($class_name)
    {
        $arr = explode('_', $class_name);
        foreach ($arr as $offset => $name)
        {
            $arr[$offset] = ucfirst($name);
        }

        if (preg_match('/[^a-z0-9_]/i', $class_name))
        {
            throw new Q_IllegalClassNameException($class_name);
        }

        return implode('_', $arr);
    }

    /**
     * 获得类定义文件的完整路径
     *
     * @param string $dir
     * @param string $class_name
     * @param string $suffix
     * @param string $prefix
     *
     * @return string
     */
    protected function _classFilePath($dir, $class_name, $suffix = '.php', $prefix = '')
    {
        $arr = explode('_', strtolower($class_name));
        $c = count($arr);
        for ($i = 1; $i < $c; $i++)
        {
            $j = $i - 1;
            $dir .= "/{$arr[$j]}";
        }
        $c--;
        return "{$dir}/{$prefix}{$arr[$c]}{$suffix}";
    }

    /**
     * 创建指定文件
     *
     * @param string $path
     * @param string $content
     */
    protected function _createFile($path, $content)
    {
        $path = strtolower($path);
        $this->_createDirs(dirname($path));
        if (@file_put_contents($path, $content))
        {
            $path = realpath($path);
            chmod($path, 0666);
            $this->_log('Create file "%s" successed.', $path);
        }
        else
        {
            throw new Q_CreateFileFailedException($path);
        }
    }

    /**
     * 建立需要的目录路径
     *
     * @param string $dir
     */
    protected function _createDirs($dir)
    {
        $dir = str_replace('/\\', DS, strtolower($dir));
        if (!file_exists($dir))
        {
            Helper_FileSys::mkdirs($dir, 0777);
            $dir = realpath($dir);
            $this->_log('Create directory "%s" successed.', $dir);
        }
    }

    /**
     * 载入模板，返回解析结果
     *
     * @param string $template
     * @param array $data
     *
     * @return string
     */
    protected function _parseTemplate($template, $data)
    {
        ob_start();
        $path = dirname(__FILE__) . '/_templates/code/' . $template . '.php';
        self::_includeTemplateFile($path, $data);
        return ob_get_clean();
    }

    /**
     * 载入模板，返回解析结果（静态方法）
     *
     * @param string $tpl
     * @param array $data
     *
     * @static
     */
    static private function _includeTemplateFile($___path, $___data)
    {
        extract($___data);
        require $___path;
    }
}

