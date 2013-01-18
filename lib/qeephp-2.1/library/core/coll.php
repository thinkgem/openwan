<?php

/**
 * 定义 QColl 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: coll.php 2590 2009-06-18 08:09:45Z jerry $
 * @package core
 */

/**
 * QColl 实现了一个类型安全的对象集合
 *
 * QColl 会检查每一个对象的类型是否符合预期，以便将同一类型的对象组织在一起。
 * QColl 具有和 PHP 内置数组相似的性质，因此可以按照使用数组的方式来使用 QColl 集合。
 *
 * 在构造一个集合时，必须指定该集合能够容纳的对象类型：
 *
 * @code php
 * $coll = new QColl('MyObject');
 * $coll[] = new MyObject();
 *
 * // 在尝试存入 MyObject2 类型的对象到 $coll 中时将抛出异常
 * $coll[] = new MyObject2();
 *
 * // 指定一个对象
 * $coll[$offset] = $item;
 *
 * // 遍历一个集合
 * foreach ($coll as $offset => $item)
 * {
 *     dump($item, $offset);
 * }
 * @endcode
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: coll.php 2590 2009-06-18 08:09:45Z jerry $
 * @package core
 */
class QColl implements Iterator, ArrayAccess, Countable
{
    /**
     * 集合对象的类型
     *
     * @var string
     * @access private
     */
    protected $_type;

    /**
     * 保存对象的数组
     *
     * @var array
     * @access private
     */
    protected $_coll = array();

    /**
     * 指示迭代对象是否有效
     *
     * @var boolean
     * @access private
     */
    protected $_is_valid = false;

    /**
     * 构造函数
     *
     * @param string $type 集合对象类型
     */
    function __construct($type)
    {
        $this->_type = $type;
    }

    /**
     * 从数组创建一个集合
     *
     * QColl::createFromArray() 方法从一个包含多个对象的数组创建集合。
     * 新建的集合包含数组中的所有对象，并且确保对象的类型符合要求。
     *
     * @param array $objects 包含多个对象的数组
     * @param string $type 集合对象类型
     * @param boolean $keep_keys 是否在创建集合时保持数组的键名
     *
     * @return QColl 新创建的集合对象
     */
    static function createFromArray(array $objects, $type, $keep_keys = false)
    {
        $coll = new QColl($type);
        if ($keep_keys)
        {
            foreach ($objects as $offset => $object) $coll[$offset] = $object;
        }
        else
        {
            foreach ($objects as $object) $coll[] = $object;
        }
        return $coll;
    }

    /**
     * 遍历集合中的所有对象，返回包含特定属性值的数组
     *
     * @code php
     * $coll = new QColl('Post');
     * $coll[] = new Post(array('title' => 't1'));
     * $coll[] = new Post(array('title' => 't2'));
     *
     * // 此时 $titles 中包含 t1 和 t2 两个值
     * $titles = $coll->values('title');
     * @endcode
     *
     * @param string $prop_name 要获取集合对象的哪一个属性
     *
     * @return array 包含所有集合对象指定属性值的数组
     */
    function values($prop_name)
    {
        $return = array();
        foreach (array_keys($this->_coll) as $offset)
        {
            if (isset($this->_coll[$offset]->{$prop_name}))
            {
                $return[] = $this->_coll[$offset]->{$prop_name};
            }
        }
        return $return;
    }

    /**
     * 检查指定索引的对象是否存在，实现 ArrayAccess 接口
     *
     * @code php
     * echo isset($coll[1]);
     * @endcode
     *
     * @param mixed $offset
     *
     * @return boolean
     */
    function offsetExists($offset)
    {
        return isset($this->_coll[$offset]);
    }

    /**
     * 返回指定索引的对象，实现 ArrayAccess 接口
     *
     * @code php
     * $item = $coll[1];
     * @endcode
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    function offsetGet($offset)
    {
        if (isset($this->_coll[$offset]))
        {
            return $this->_coll[$offset];
        }
        // LC_MSG: 无效的键名 "%s".
        throw new QException(__('无效的键名 "%s".', $offset));
    }

    /**
     * 设置指定索引的对象，实现 ArrayAccess 接口
     *
     * @code php
     * $coll[1] = $item;
     * @endcode
     *
     * @param mixed $offset
     * @param mixed $value
     */
    function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $offset = count($this->_coll);
        }
        $this->_checkType($value);
        while (isset($this->_coll[$offset])) $offset++;
        $this->_coll[$offset] = $value;
    }

    /**
     * 注销指定索引的对象，实现 ArrayAccess 接口
     *
     * @code php
     * unset($coll[1]);
     * @endcode
     *
     * @param mixed $offset
     */
    function offsetUnset($offset)
    {
        unset($this->_coll[$offset]);
    }

    /**
     * Shift an element off the beginning of QColl
     *
     * @return mixed
     */
    function shift()
    {
        $ret = reset($this->_coll);
        unset($this->_coll[key($this->_coll)]);
        return $ret;
    }

    /**
     * 返回当前位置的对象，实现 Iterator 接口
     *
     * @return mixed
     */
    function current()
    {
        return current($this->_coll);
    }

    /**
     * 返回遍历时的当前索引，实现 Iterator 接口
     *
     * @return mixed
     */
    function key()
    {
        return key($this->_coll);
    }

    /**
     * 遍历下一个对象，实现 Iterator 接口
     */
    function next()
    {
        $this->_is_valid = (false !== next($this->_coll));
    }

    /**
     * 重置遍历索引，实现 Iterator 接口
     */
    function rewind()
    {
        $this->_is_valid = (false !== reset($this->_coll));
    }

    /**
     * 判断是否是调用了 rewind() 或 next() 之后获得的有效对象，实现 Iterator 接口
     *
     * @return boolean
     */
    function valid()
    {
        return $this->_is_valid;
    }

    /**
     * 返回对象总数，实现 Countable 接口
     *
     * @return int
     */
    function count()
    {
        return count($this->_coll);
    }

    /**
     * 确定集合是否是空的
     *
     * @return boolean
     */
    function isEmpty()
    {
        return empty($this->_coll);
    }

    /**
     * 返回集合中的第一个对象，如果没有任何对象，则抛出异常
     *
     * @return object
     */
    function first()
    {
        if (count($this->_coll))
        {
            return reset($this->_coll);
        }
        // LC_MSG: "%s" 集合中没有任何对象.
        throw new QException(__('"%s" 集合中没有任何对象.', $this->_type));
    }

    /**
     * 返回集合中的最后一个对象，如果没有任何对象，则抛出异常
     *
     * @return object
     */
    function last()
    {
        if (count($this->_coll))
        {
            $keys = array_keys($this->_coll);
            $key = array_pop($keys);
            return $this->_coll[$key];
        }
        // LC_MSG: "%s" 集合中没有任何对象.
        throw new QException(__('"%s" 集合中没有任何对象.', $this->_type));
    }

    /**
     * 追加数组或 QColl 对象的内容到集合中
     *
     * @code php
     * $data = array(
     *     $item1,
     *     $item2,
     *     $item3
     * );
     *
     * $coll->append($data);
     * @endcode
     *
     * QColl::append() 在追加数据时不会保持键名。
     *
     * @param array|QColl $data 要追加的数据
     *
     * @return QColl 返回集合对象本身，实现连贯接口
     */
    function append($data)
    {
        if (is_array($data) || ($data instanceof Iterator))
        {
            foreach ($data as $item)
            {
                $this->offsetSet(null, $item);
            }
        }
        else
        {
            // LC_MSG: "%s()" 的参数 "%s" 必须是 "%s"，但实际提供的是 "%s".
            throw new QException(__('"%s()" 的参数 "%s" 必须是 "%s"，但实际提供的是 "%s".',
                    __METHOD__, '$data', '数组或实现了 Iterator 的对象', gettype($data)));
        }

        return $this;
    }

    /**
     * 查找符合指定属性值的对象，没找到返回 NULL
     *
     * @code php
     * // 在 $coll 集合中搜索 title 属性等于 T1 的第一个对象
     * $item = $coll->search('title', 'T1');
     * @endcode
     *
     * @param string $prop_name 要搜索的属性名
     * @param mixed $needle 需要的属性值
     * @param boolean $strict 是否严格比对属性值
     *
     * @return mixed
     */
    function search($prop_name, $needle, $strict = false)
    {
        foreach ($this->_coll as $item)
        {
            if ($strict)
            {
                if ($item->{$prop_name} === $needle) return $item;
            }
            else
            {
                if ($item->{$prop_name} == $needle) return $item;
            }
        }
        return null;
    }

    /**
     * 将集合所有元素的值转换为一个名值对数组
     *
     * @param string $key_name
     * @param string $value_name
     *
     * @return array
     */
    function toHashMap($key_name, $value_name = null)
    {
        $ret = array();
        if ($value_name)
        {
            foreach ($this->_coll as $obj)
            {
                $ret[$obj[$key_name]] = $obj[$value_name];
            }
        }
        else
        {
            foreach ($this->_coll as $obj)
            {
                $ret[$obj[$key_name]] = $obj;
            }
        }

        return $ret;
    }

    /**
     * 返回指定键的所有值
     *
     * @param string $col 要查询的键
     *
     * @return array 包含指定键所有值的数组
     */
    function getCols($col)
    {
        $ret = array();
        foreach ($this->_coll as $obj) 
        {
            if (isset($obj[$col])) { $ret[] = $obj[$col]; }
        }
        return $ret;
    }

    /**
     * 对集合中每一个对象调用指定的方法
     *
     * @code php
     * class OrderItem
     * {
     *     public $price;
     *     public $quantity;
     *
     *     function __construct($price, $quantity)
     *     {
     *         $this->price = $price;
     *         $this->quantity = $quantity;
     *     }
     *
     *     // 计算订单项目的小计
     *     function sum()
     *     {
     *         return $this->price * $this->quantity;
     *     }
     *
     *     // 返回单价
     *     function price()
     *     {
     *         return $this->price;
     *     }
     *
     *     // 返回数量
     *     function quantity()
     *     {
     *         return $this->quantity;
     *     }
     *
     *     // 累加多个合计
     *     static function totalSum($objects)
     *     {
     *         $total = 0;
     *         while (list(, $item) = each($objects))
     *         {
     *             $total += $item->sum();
     *         }
     *         return $total;
     *     }
     *
     *     // 用于 QColl 的回调方法
     *     static function _qcoll_callback()
     *     {
     *         return array('sum' => 'totalSum');
     *     }
     * }
     *
     * // 构造一个集合，包含多个 OrderItem 对象
     * $coll = QColl::create(array(
     *     new OrderItem(100, 3),
     *     new OrderItem(200, 5),
     *     new OrderItem(300, 2)), 'OrderItem');
     *
     * // 取得集合中所有订单项目的金额合计
     * $sum = $coll->sum();
     *
     * // 将会输出 1900 （根据 100 * 3 + 200 * 5 + 300 * 2 计算）
     * echo $sum;
     *
     * // 取得每个项目的单价
     * $price = $coll->price();
     * // 将会输出 array(100, 200, 300)
     * dump($price);
     * @endcode
     *
     * 当调用 QColl 自身没有定义的方法时，QColl 将认为开发者是要对集合中的每一个对象调用指定方法。
     *
     * -  此时，QColl 首先检查集合中的对象是否提供了 _qcoll_callback() 静态方法；
     * -  如果有，则通过 _qcoll_callback() 取得一个方法映射表；
     * -  QColl 根据 _qcoll_callback() 返回的方法映射表调用对象的其他静态方法。
     * -  如果没有提供 _qcoll_callback() 方法，或方法映射表中没有指定的方法。
     *    QColl 则遍历集合中的所有对象，尝试调用对象的指定方法。
     *
     * @param string $method
     * @param array $args;
     *
     * @return mixed
     */
    function __call($method, $args)
    {
        $not_implement = false;
        $method = strtolower($method);
        if (method_exists($this->_type, '_qcoll_callback'))
        {
            $map = call_user_func(array($this->_type, '_qcoll_callback'));
            $map = array_change_key_case($map, CASE_LOWER);
            if (isset($map[$method]))
            {
                array_unshift($args, $this->_coll);
                return call_user_func_array(array($this->_type, $map[$method]), $args);
            }
        }

        $result = array();
        foreach ($this->_coll as $object)
        {
            $result[] = call_user_func_array(array($object, $method), $args);
        }

        return $result;
    }

    /**
     * 检查值是否符合类型要求
     *
     * @param object $object
     */
    protected function _checkType($object)
    {
        if (is_object($object))
        {
            if ($object instanceof $this->_type) return;
            $type = get_class($object);
        }
        else
        {
            $type = gettype($object);
        }
        // LC_MSG: 集合只能容纳 "%s" 类型的对象，而不是 "%s" 类型的值.
        throw new QException(__('集合只能容纳 "%s" 类型的对象，而不是 "%s" 类型的值.', $this->_type, $type));
    }

}

