<?php
// $Id: uniqueness.php 2638 2009-07-30 08:12:24Z jerry $

/**
 * 定义 Behavior_Uniqueness 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: uniqueness.php 2638 2009-07-30 08:12:24Z jerry $
 * @package behavior
 */

/**
 * Behavior_Uniqueness 用于检查指定的属性是否存在重复值
 *
 * uniqueness 行为插件支持下列设置选项：
 *
 * -  check_props: 要检查的属性
 *
 *    假设 email 属性不允许重复，则 check_props 设置为 'email' 即可。
 *    当在创建和更新对象时，如果发现重复的 email 属性值，
 *    则会抛出 QDB_ActiveRecord_ValidateFailedException 异常。
 *
 *    如果要检查多个属性的值是否重复，可以将 check_props 设置为 'username, email' 的形式。
 *    每个属性名之间用逗号分隔。
 *
 *    如果要验证的属性是一个 BELONGS_TO 或 HAS_ONE 关联，则会以关联模型的主键值作为检查条件。
 *
 *    @code php
 *    // category 是模型的一个 BELONGS_TO 关联
 *    'check_props' => 'category',
 *    @endcode
 *
 *    **对于 HAS_MANY 和 MANY_TO_MANY 关联模型的验证不支持。**
 *
 *
 * -  error_messages: 当某个属性出现重复值时，要在异常中使用的错误信息。
 *
 *    error_messages 设置是一个数组，以属性名为键名，错误信息为键值，例如：
 *
 *    @code php
 *    'error_messages' => array(
 *        'username' => '用户名不能重复',
 *        'email'    => '电子邮件地址不能重复',
 *    ),
 *    @endcode
 *
 *
 * **组合验证**
 *
 * 有时候，我们希望能够验证多个属性值的组合是否出现重复。
 * 例如 prefix 和 suffix 属性值的组合不能重复，此时可以在 check_props 设置中写上：
 *
 * @code php
 * 'check_props' => 'prefix+suffix',
 * @endcode
 *
 * 则在创建和更新对象时，将以 prefix 和 suffix 两个属性值的组合来检查。
 * 对于这类组合检查，error_messages 应该设置为：
 *
 * @code php
 * 'error_messages' => array(
 *     'prefix+suffix' => '前缀和后缀的组合不能出现重复',
 * ),
 * @endcode
 *
 *
 * **混合使用**
 *
 * 我们可以将单个属性的检查和组合属性的检查结合起来使用：
 *
 * @code php
 * 'check_props' => 'username, email, prefix+suffix',
 * 'error_messages' => array(
 *     'username' => '用户名不能重复',
 *     'email'    => '电子邮件地址不能重复',
 *     'prefix+suffix' => '前缀和后缀的组合不能出现重复',
 * ),
 * @endcode
 *
 * 
 * **忽略空值的检查**
 *
 * 如果某些属性值允许为空（null 或空字符串），那么应该在属性名后面加上“?”来注明：
 *
 * @code php
 * 'check_props' => 'username, email?, prefix+suffix?',
 * 'error_messages' => array(
 *     'username' => '用户名不能重复',
 *     'email'    => '电子邮件地址不能重复',
 *     'prefix+suffix' => '前缀和后缀的组合不能出现重复',
 * ),
 * @endcode
 *
 * 上述设置的效果：
 *
 * -  如果 email 属性值为空，则不会检查 email 属性值。
 * -  如果 suffix 属性值为空，则不会检查 prefix+suffix 属性值的组合。
 *
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: uniqueness.php 2638 2009-07-30 08:12:24Z jerry $
 * @package behavior
 */
class Model_Behavior_Uniqueness extends QDB_ActiveRecord_Behavior_Abstract
{
    /**
     * 插件的设置信息
     *
     * @var array
     */
    protected $_settings = array
    (
        // 要检查的属性
        'check_props' => '',
        // 检查未通过时的消息
        'error_messages' => array(),
    );

    /**
     * 绑定行为插件
     */
    function bind()
    {
        $this->_addEventHandler(self::BEFORE_UPDATE, array($this, '_before_update'));
        $this->_addEventHandler(self::BEFORE_CREATE, array($this, '_before_create'));
    }

    /**
     * BEFORE_CREATE 事件的处理函数
     */
    function _before_create(QDB_ActiveRecord_Abstract $obj)
    {
        $this->_checkUniqueness($obj);
    }

    /**
     * BEFORE_UPDATE 事件的处理函数
     */
    function _before_update(QDB_ActiveRecord_Abstract $obj)
    {
        $cond = new QDB_Cond();
        foreach ($this->_meta->idname as $idname)
        {
            $cond->orCond("[{$idname}] <> ?", $obj[$idname]);
        }
        $this->_checkUniqueness($obj, $cond, true);
    }

    /**
     * 检查唯一性
     *
     * @param QDB_ActiveRecord_Abstract $obj
     * @param QDB_Cond $more_cond
     * @param boolean $ignore_id
     */
    private function _checkUniqueness(QDB_ActiveRecord_Abstract $obj, QDB_Cond $more_cond = null, $ignore_id = false)
    {
        $check_props = Q::normalize($this->_settings['check_props']);
        if (empty($check_props)) return;

        $failed = array();

        foreach ($check_props as $check)
        {
            // 如果忽略主键字段，而要检查的属性正好是主键字段，则跳过检查
            if ($ignore_id && in_array($check, $this->_meta->idname)) continue;

            if (strpos($check, '+') !== false)
            {
                $check = Q::normalize($check, '+');
            }
            else
            {
                $check = array($check);
            }

            $cond = array();
            $skip_valid = false; // FIXED! 标注是否跳过验证
            foreach ($check as $offset => $prop_name)
            {
                if (substr($prop_name, -1, 1) == '?')
                {
                    $prop_name = substr($prop_name, 0, -1);
                    $value = $obj[$prop_name];
                    if (strlen($value) == 0)
                    {
                        $skip_valid = true;
                        continue;
                    }
                    $check[$offset] = $prop_name;
                }
                else
                {
                    $value = $obj[$prop_name];
                }

                if (is_object($value))
                {
                    $value = $value->id();
                    if (is_array($value))
                    {
                        foreach ($value as $p => $v)
                        {
                            $cond[$p] = $v;
                        }
                    }
                    else
                    {
                        $cond[$obj[$prop_name]->idname()] = $value;
                    }
                }
                else
                {
                    $cond[$prop_name] = $value;
                }
            }

            if($skip_valid) continue;

            if (!is_null($more_cond)) $cond[] = $more_cond;
            if ($this->_meta->find($cond)->getCount() < 1) continue;

            // 验证失败，保存错误消息
            $check = implode('+', $check);
            if (isset($this->_settings['error_messages'][$check]))
            {
                $failed[$check] = $this->_settings['error_messages'][$check];
            }
            else
            {
                $failed[$check] = "{$check} duplicated";
            }
        }

        if (!empty($failed))
        {
            throw new QDB_ActiveRecord_ValidateFailedException($failed, $obj);
        }
    }

}

