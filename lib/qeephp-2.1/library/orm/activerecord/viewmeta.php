<?php

/**
 * QDB_ActiveRecord_ViewMeta 类封装了 QDB_ActiveRecord_View 继承类的元信息
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @package orm
 */
class QDB_ActiveRecord_ViewMeta extends QDB_ActiveRecord_Meta
{
    protected $_find_sql;
    protected $_dsn;
    protected $_dbo;

    /**
     * 获得指定指定 ActiveRecord 继承类的元对象唯一实例
     *
     * @param string $class
     *
     * @return QDB_ActiveRecord_Meta
     */
    static function instance($class)
    {
        if (!isset(self::$_metas[$class]))
        {
            self::$_metas[$class] = new QDB_ActiveRecord_ViewMeta($class);
            self::$_metas[$class]->_init2();
        }
        return self::$_metas[$class];
    }

    /**
     * 开启一个查询，并根据提供的参数设置查询对象
     *
     * @param array $args
     *
     * @return QDB_Select
     */
    function findByArgs(array $args = array())
    {
        $select = new QDB_Select(QDB::getConn($this->_dsn));
        $select->setSQL($this->_find_sql)->asColl()->asObject($this->class_name);
        $c = count($args);
        if ($c > 0)
        {
            if ($c == 1 && is_int($args[0]) && $this->idname_count == 1)
            {
                $select->where(array(reset($this->idname) => $args[0]));
            }
            else
            {
                call_user_func_array(array($select, 'where'), $args);
            }
        }

        return $select;
    }

    /**
     * 更新符合条件的对象
     *
     * @param array $attribs
     */
    function updateWhere(array $attribs)
    {
    }

    /**
     * 更新符合条件的记录
     */
    function updateDbWhere()
    {
    }

    /**
     * 实例化符合条件的对象，并调用这些对象的 destroy() 方法，返回被删除的对象数
     *
     * @return int
     */
    function destroyWhere()
    {
        return 0;
    }

    /**
     * 从数据库中直接删除符合条件的对象
     *
     * 与 destroyWhere() 不同，deleteWhere() 会直接从数据库中删除符合条件的记录。
     * 而不是先把符合条件的对象查询出来再调用对象的 destroy() 方法进行删除。
     *
     * 因此，deleteWhere() 速度更快，但无法处理对象间的关联关系。
     */
    function deleteWhere()
    {
    }

    /**
     * 对数据进行验证，返回所有未通过验证数据的错误信息
     *
     * @param array $data 要验证的数据
     * @param array|string $props 指定仅仅验证哪些属性
     * @param string $mode 验证模式
     *
     * @return array 所有没有通过验证的属性名称及验证规则
     */
    function validate(array $data, $props = null, $mode = 'general')
    {
        return array();
    }

    /**
     * 第一步初始化
     *
     * @param string $class
     */
    protected function _init1($class)
    {
        // 从指定类获得初步的定义信息
        Q::loadClass($class);
        $this->class_name = $class;
        $ref = (array) call_user_func(array($class, '__define'));

        // 设置 find_sql
        if (!empty($ref['find_sql']))
        {
            $this->_find_sql = $ref['find_sql'];
        }
        else
        {
            throw new QDB_ActiveRecord_Exception('must set "find_sql".');
        }

        if (!empty($ref['dsn']))
        {
            $this->_dsn = $ref['dsn'];
        }

        // 根据字段定义确定字段属性
        if (empty($ref['props']) || ! is_array($ref['props']))
        {
            $ref['props'] = array();
        }

        foreach ($ref['props'] as $prop_name => $config)
        {
            $this->addProp($prop_name, $config);
        }

        // 绑定行为插件
        if (isset($ref['behaviors']))
        {
            $config = isset($ref['behaviors_settings']) ? $ref['behaviors_settings'] : array();
            $this->bindBehaviors($ref['behaviors'], $config);
        }
    }

}
