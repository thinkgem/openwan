<?php
// $Id: cond.php 2644 2009-08-10 02:36:20Z jerry $

/**
 * 定义 QDB_Cond 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: cond.php 2644 2009-08-10 02:36:20Z jerry $
 * @package database
 */

/**
 * QDB_Cond 类封装复杂的查询条件
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: cond.php 2644 2009-08-10 02:36:20Z jerry $
 * @package database
 */
class QDB_Cond
{
    /**
     * 定义组
     */
    const BEGIN_GROUP = '(';

    const END_GROUP = ')';

    /**
     * 构成查询条件的各个部分
     *
     * @var array
     */
    protected $_parts = array();

    /**
     * 构造函数
     */
    function __construct()
    {
        $args = func_get_args();
        if (!empty($args))
        {
            $this->_parts[] = array($args, true);
        }
    }

    /**
     * 创建一个 QDB_Cond 对象，便于使用连贯接口
     *
     * @return QDB_Cond
     */
    static function create()
    {
        $cond = new QDB_Cond();
        $args = func_get_args();
        if (!empty($args))
        {
            $cond->appendDirect($args);
        }
        return $cond;
    }

    /**
     * 直接创建一个 QDB_Cond 对象
     *
     * @param string|array|QDB_Expr|QDB_Cond $cond
     * @param array $cond_args
     *
     * @return QDB_Cond
     */
    static function createByArgs($cond, array $cond_args = null)
    {
        if (!is_array($cond_args))
        {
            $cond_args = array();
        }
        $c = new QDB_Cond();
        if (! empty($cond))
        {
            array_unshift($cond_args, $cond);
            $c->appendDirect($cond_args);
        }
        return $c;
    }

    /**
     * 直接添加一个查询条件
     *
     * @param array $args
     * @param bool $bool
     *
     * @return QDB_Cond
     */
    function appendDirect(array $args, $bool = true)
    {
        $this->_parts[] = array(
            $args,
            $bool
        );
        return $this;
    }

    /**
     * 添加一个新条件，与其他条件之间使用 AND 布尔运算符连接
     *
     * @return QDB_Cond
     */
    function andCond()
    {
        $this->_parts[] = array(
            func_get_args(),
            true
        );
        return $this;
    }

    /**
     * 添加一个新条件，与其他条件之间使用 OR 布尔运算符连接
     *
     * @return QDB_Cond
     */
    function orCond()
    {
        $this->_parts[] = array(
            func_get_args(),
            false
        );
        return $this;
    }

    /**
     * 开始一个条件组，AND
     *
     * @return QDB_Cond
     */
    function andGroup()
    {
        $this->_parts[] = array(
            self::BEGIN_GROUP,
            true
        );
        $this->_parts[] = array(
            func_get_args(),
            true
        );
        return $this;
    }

    /**
     * 开始一个条件组，OR
     *
     * @return QDB_Cond
     */
    function orGroup()
    {
        $this->_parts[] = array(
            self::BEGIN_GROUP,
            false
        );
        $this->_parts[] = array(
            func_get_args(),
            false
        );
        return $this;
    }

    /**
     * 结束一个条件组
     *
     * @return QDB_Cond
     */
    function endGroup()
    {
        $this->_parts[] = array(
            self::END_GROUP,
            null
        );
        return $this;
    }

    /**
     * 格式化为字符串
     *
     * @param QDB_Adapter_Abstract $conn
     * @param string $table_name
     * @param array $fields_mapping
     * @param callback $callback
     *
     * @return string
     */
    function formatToString($conn, $table_name = null, array $fields_mapping = null, $callback = null)
    {
        if (empty($this->_parts))
        {
            return '';
        }
        if (is_null($fields_mapping))
        {
            $fields_mapping = array();
        }
        $sql = '';

        $skip_cond_link = true;
        $bool = true;

        /**
         * _parts 的存储结构是一个二维数组
         *
         * 数组的每一项如下：
         *
         * - 要处理的查询条件
         * - 该查询条件与其他查询条件是 AND 还是 OR 关系
         */
        foreach ($this->_parts as $part)
        {
            list ($args, $_bool) = $part;
            if (empty($args))
            {
                // 如果查询条件为空，忽略该项
                $skip_cond_link = true;
                continue;
            }

            if (! is_null($_bool))
            {
                // 如果该项查询条件没有指定 AND/OR 关系，则不改变当前的 AND/OR 关系状态
                $bool = $_bool;
            }

            if (! is_array($args))
            {
                // 查询如果不是一个数组，则判断是否是特殊占位符
                if ($args == self::BEGIN_GROUP)
                {
                    if (! $skip_cond_link)
                    {
                        $sql .= ($bool) ? ' AND ' : ' OR ';
                    }
                    $sql .= self::BEGIN_GROUP;
                    $skip_cond_link = true;
                }
                else
                {
                    $sql .= self::END_GROUP;
                }
                continue;
            }
            else
            {
                if ($skip_cond_link)
                {
                    $skip_cond_link = false;
                }
                else
                {
                    /**
                     * 如果 $skip_cond_link 为 false，表示前一个项目是一个查询条件，
                     * 因此需要用 AND/OR 来连接多个查询条件。
                     */
                    $sql .= ($bool) ? ' AND ' : ' OR ';
                }
            }

            // 剥离出查询条件，$args 剩下的内容是查询参数
            $cond = reset($args);
            array_shift($args);

            if ($cond instanceof QDB_Cond || $cond instanceof QDB_Expr)
            {
                // 使用 QDB_Cond 作为查询条件
                $part = $cond->formatToString($conn, $table_name, $fields_mapping, $callback);
            }
            elseif (is_array($cond))
            {
                // 使用数组作为查询条件
                $part = array();
                foreach ($cond as $field => $value)
                {
                    if (! is_string($field))
                    {
                        // 如果键名不是字符串，说明键值是一个查询条件
                        if (empty($value))
                        {
                            continue;
                        }

                        if ($value instanceof QDB_Cond || $cond instanceof QDB_Expr)
                        {
                            // 查询条件如果是 QDB_Cond 或 QDB_Expr，则格式化为字符串
                            $value = $value->formatToString($conn, $table_name, $fields_mapping, $callback);
                        }

                        $value = $conn->qsql($value, $table_name, $fields_mapping, $callback);
                        $style = (strpos($value, '?') === false) ? QDB::PARAM_CL_NAMED : QDB::PARAM_QM;
                        $part[] = $conn->qinto($value, $args, $style);
                    }
                    else
                    {
                        // 如果键名是一个字符串，则假定为 “字段名” => “查询值” 这样的名值对
                        $field = '[' . trim($field, '[]') . ']';
                    	$field = $conn->qsql($field, $table_name, $fields_mapping, $callback);

                        // 转义查询值
                        if (! is_array($value))
                        {
                            $part[] = $conn->qid("{$field}") . ' = ' . $conn->qstr($value);
                        }
                        else
                        {
                            $values = array();
                            foreach ($value as $_v)
                            {
                                $values[] = $conn->qstr($_v);
                            }
                            unset($value);
                            $part[] = $conn->qid("{$field}") . ' IN(' . implode(', ', $values) . ')';
                        }
                    }
                }
                // 用 AND 连接多个查询条件
                $part = implode(' AND ', $part);
            }
            else
            {
                // 使用字符串做查询条件
                $part = $conn->qsql($cond, $table_name, $fields_mapping, $callback);
                $style = (strpos($part, '?') === false) ? QDB::PARAM_CL_NAMED : QDB::PARAM_QM;
                $part = $conn->qinto($part, $args, $style);
            }

            if (empty($part) || $part == '()')
            {
                // 除去多余的 " OR " 和 " AND " 字符

                if(strrpos($sql, ' OR ') !== false)
                {
                    $sql = substr($sql, 0, -4);
                }

                if(strrpos($sql, ' AND ') !== false)
                {
                    $sql = substr($sql, 0, -5);
                }

            	$skip_cond_link = true;
                continue;
            }

            $sql .= $part;
        }

        if (empty($sql))
        {
            return '';
        }
        return '(' . $sql . ')';
    }
}

