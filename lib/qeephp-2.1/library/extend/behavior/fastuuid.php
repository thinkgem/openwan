<?php
// $Id: fastuuid.php 2264 2009-02-21 07:05:30Z dualface $

/**
 * 定义 Behavior_Fastuuid 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: fastuuid.php 2264 2009-02-21 07:05:30Z dualface $
 * @package behavior
 */

/**
 * Behavior_Fastuuid 为模型生成 64 位整数或混淆字符串的不重复 ID
 *
 * 感谢“Ivan Tan|谭俊青 DrinChing (at) Gmail.com”提供的算法。
 *
 * 通常我们习惯使用自增字段来做主键，简单易用。
 *
 * 但在于大规模应用中，使用自增字段将难以实现分布式数据库架构。
 * 并且对数据进行纵向和横向分割（分表分库）造成障碍。
 * 此时最好的解决方案是使用 UUID。
 *
 * 但 UUID 不是每一种数据库都支持，用字符串来模拟效率太低。
 * 并且如果通过 URL 传递，UUID 也显得太长。
 *
 *
 * fastuuid 插件提供了另一种解决方案：
 * 使用 64bit 整数存储主键，主键由 fastuuid 插件在创建记录时自动生成。
 *
 * fastuuid 插件支持下列设置：
 *
 * -  begin_timestamp: 计算 ID 值时，使用的起始日期初值，通常无需指定。
 *    **注意：**如果在应用程序使用过程中修改这个设置，可能导致出现重复的 ID 值，
 *
 * -  suffix_len: 生成的 ID 值附加多少位随机数，默认值为 3。
 *    即便不附加随机数也不会生成重复 ID，但附加的随机数可以让 ID 更难被猜测。
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: fastuuid.php 2264 2009-02-21 07:05:30Z dualface $
 * @package behavior
 */
class Model_Behavior_Fastuuid extends QDB_ActiveRecord_Behavior_Abstract
{

    /**
     * 设置
     *
     * @var array
     */
    protected $_settings = array
    (
        //! 计算种子数的开始时间
        'being_timestamp' => 1206576000, // 2008-03-27
        //! 计算 ID 时要添加多少位随机数
        'suffix_len' => 3
    );

    /**
     * 构造函数
     *
     * @param QDB_ActiveRecord_Meta $meta
     * @param array $settings
     *
     * @access private
     */
    function __construct(QDB_ActiveRecord_Meta $meta, array $settings)
    {
        parent::__construct($meta, $settings);

        if ($meta->idname_count > 1)
        {
            throw new QDB_ActiveRecord_CompositePKIncompatibleException($this->_meta->class_name, __CLASS__);
        }
    }

    /**
     * 绑定行为插件
     *
     * @access private
     */
    function bind()
    {
        $this->_addStaticMethod('genUUID', array(__CLASS__, 'genUUID'));
        $this->_addEventHandler(self::BEFORE_CREATE, array($this, '_before_create'));
    }

    /**
     * 在数据库中创建 ActiveRecord 对象前调用
     *
     * @param QDB_ActiveRecord_Abstract $obj
     */
    function _before_create(QDB_ActiveRecord_Abstract $obj)
    {
        $new_id = self::genUUID($this->_settings['being_timestamp'], $this->_settings['suffix_len']);
        $idname = reset($this->_meta->idname);
        $obj->changePropForce($idname, $new_id);
    }

    /**
     * 生成不重复的 UUID
     *
     * @param int $being_timestamp
     * @param int $suffix_len
     *
     * @return string
     */
    static function genUUID($being_timestamp, $suffix_len)
    {
        $time = explode(' ', microtime());
        $id = ($time[1] - $being_timestamp) . sprintf('%06u', substr($time[0], 2, 6));
        if ($suffix_len > 0)
        {
            $id .= substr(sprintf('%010u', mt_rand()), 0, $suffix_len);
        }
        return $id;
    }
}

