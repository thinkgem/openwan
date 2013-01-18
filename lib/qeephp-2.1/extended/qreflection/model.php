<?php

class QReflection_Model
{
    /**
     * 模型的类名称
     *
     * @var string
     */
    protected $_model_class_name;

    /**
     * 模型所用数据表的名称
     *
     * @var string
     */
    protected $_model_table_name;

    /**
     * 模型文件完整路径
     *
     * @var string
     */
    protected $_model_file_path;

    /**
     * 该模型所属模块的反射
     *
     * @var QReflection_Module
     */
    protected $_module;

    function __construct(QReflection_Module $module, $model_name, $path)
    {
        $this->_module = $module;
        $this->_model_name = $model_name;
        $this->_model_file_path = $path;
    }

    /**
     * 返回该模型所属模块的反射
     *
     * @return QReflection_Module
     */
    function module()
    {
        return $this->_module;
    }

    /**
     * 返回模型所述模块的名字
     *
     * @return string
     */
    function moduleName()
    {
        return $this->_module->moduleName();
    }

    /**
     * 返回模型名称
     *
     * @return string
     */
    function modelName()
    {
        return $this->_model_name;
    }

    /**
     * 返回模型文件的完整路径
     *
     * @return string
     */
    function filePath()
    {
        return $this->_model_file_path;
    }

    /**
     * 返回模型所用数据表的名称（不含全局前缀）
     *
     * @return string
     */
    function tableName()
    {
        if (is_null($this->_model_table_name))
        {
            $this->_parseClassFile();
        }
        return $this->_model_table_name;
    }

    /**
     * 返回模型的类名称
     *
     * @return string
     */
    function className()
    {
        if (is_null($this->_model_class_name))
        {
            $this->_parseClassFile();
        }
        return $this->_model_class_name;
    }

    /**
     * 测试指定的文件是否是模型定义文件，成功返回模型类名称，失败返回 false
     *
     * @param string $file
     *
     * @return string|boolean
     */
    static function testModelFile($file)
    {
        static $tested = array();

        $file = realpath($file);
        if (empty($file)) return false;
        if (isset($tested[$file])) return $tested[$file];

        $contents = file_get_contents($file);
        // 模型类必然是一个继承类，并且有 __define() 静态方法
        if (preg_match('/extends[ \t]+/i', $contents) == 0
            || preg_match('/static[ \t]+function[ \t]+__define()/i', $contents) == 0)
        {
            return false;
        }

        // 尝试取得类名称
        $m = array();
        preg_match('/class[ \t]+([a-z][a-z0-9_]+)[ \t]+extends/i', $contents, $m);
        if (!isset($m[1])) return false;

        /**
         * 提取模型信息
         */
        $info = array('class' => $m[1]);

        // 取得表名称
        $m = array();
        preg_match("/'table_name'[ \t]*=>[ \t]*'([a-z0-9_\-]+)'/i", $contents, $m);
        if (isset($m[1]))
        {
            $info['table_name'] = $m[1];
        }
        else
        {
            $info['table_name'] = null;
        }

        $tested[$file] = $info;

        return $info;
    }

    /**
     * 分类类文件，确定模型类名称和所用数据表名称
     */
    protected function _parseClassFile()
    {
        $info = self::testModelFile($this->filePath());
        if ($info == false)
        {
            throw new QReflection_NotModelFileException($this->_filePath());
        }

        $this->_model_table_name = $info['table_name'];
        $this->_model_class_name = $info['class'];
    }

}

