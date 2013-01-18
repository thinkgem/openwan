<?php echo '<?php'; ?>

// $Id$

/**
 * <?php echo $class_name; ?> 封装来自 <?php echo $table_name; ?> 数据表的记录及领域逻辑
 */
class <?php echo $class_name; ?> extends QDB_ActiveRecord_Abstract
{

    /**
     * 返回对象的定义
     *
     * @static
     *
     * @return array
     */
    static function __define()
    {
        return array
        (
            // 指定该 ActiveRecord 要使用的行为插件
            'behaviors' => '',

            // 指定行为插件的配置
            'behaviors_settings' => array
            (
                # '插件名' => array('选项' => 设置),
            ),

            // 用什么数据表保存对象
            'table_name' => '<?php echo $table_name; ?>',

            // 指定数据表记录字段与对象属性之间的映射关系
            // 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
            'props' => array
            (
                // 主键应该是只读，确保领域对象的“不变量”
<?php
foreach ($pk as $p)
{
    echo "                '{$p}' => array('readonly' => true),\n";
}
?>
<?php if (isset($meta['created'])) : ?>

                // 对象创建时间应该是只读
                'created' => array('readonly' => true),

<?php endif; ?>
<?php if (isset($meta['updated'])) : ?>
                // 对象最后更新时间应该是只读
                'updated' => array('readonly' => true),

<?php endif; ?>

                /**
                 *  可以在此添加其他属性的设置
                 */
                # 'other_prop' => array('readonly' => true),

                /**
                 * 添加对象间的关联
                 */
                # 'other' => array('has_one' => 'Class'),

            ),

            /**
             * 允许使用 mass-assignment 方式赋值的属性
             *
             * 如果指定了 attr_accessible，则忽略 attr_protected 的设置。
             */
            'attr_accessible' => '',

            /**
             * 拒绝使用 mass-assignment 方式赋值的属性
             */
            'attr_protected' => '<?php echo implode(', ', $pk); ?>',

            /**
             * 指定在数据库中创建对象时，哪些属性的值不允许由外部提供
             *
             * 这里指定的属性会在创建记录时被过滤掉，从而让数据库自行填充值。
             */
            'create_reject' => '',

            /**
             * 指定更新数据库中的对象时，哪些属性的值不允许由外部提供
             */
            'update_reject' => '',

            /**
             * 指定在数据库中创建对象时，哪些属性的值由下面指定的内容进行覆盖
             *
             * 如果填充值为 self::AUTOFILL_TIMESTAMP 或 self::AUTOFILL_DATETIME，
             * 则会根据属性的类型来自动填充当前时间（整数或字符串）。
             *
             * 如果填充值为一个数组，则假定为 callback 方法。
             */
            'create_autofill' => array
            (
                # 属性名 => 填充值
                # 'is_locked' => 0,
<?php
foreach ($meta as $name => $f) :
    if ($name != 'created' && $name != 'updated')
    {
        continue;
    }
?>
                '<?php echo $name; ?>' => <?php if ($f['ptype'] == 'date' || $f['ptype'] == 'datetime') : ?>self::AUTOFILL_DATETIME <?php else : ?>self::AUTOFILL_TIMESTAMP <?php endif; ?>,
<?php
endforeach;
?>
            ),

            /**
             * 指定更新数据库中的对象时，哪些属性的值由下面指定的内容进行覆盖
             *
             * 填充值的指定规则同 create_autofill
             */
            'update_autofill' => array
            (
<?php
foreach ($meta as $name => $f) :
    if ($name != 'updated')
    {
        continue;
    }
?>
                '<?php echo $name; ?>' => <?php if ($f['ptype'] == 'date' || $f['ptype'] == 'datetime') : ?>self::AUTOFILL_DATETIME <?php else : ?>self::AUTOFILL_TIMESTAMP <?php endif; ?>,
<?php
endforeach;
?>
            ),

            /**
             * 在保存对象时，会按照下面指定的验证规则进行验证。验证失败会抛出异常。
             *
             * 除了在保存时自动验证，还可以通过对象的 ::meta()->validate() 方法对数组数据进行验证。
             *
             * 如果需要添加一个自定义验证，应该写成
             *
             * 'title' => array(
             *        array(array(__CLASS__, 'checkTitle'), '标题不能为空'),
             * )
             *
             * 然后在该类中添加 checkTitle() 方法。函数原型如下：
             *
             * static function checkTitle($title)
             *
             * 该方法返回 true 表示通过验证。
             */
            'validations' => array
            (
<?php
foreach ($meta as $name => $f) :
    if (in_array($name, $pk) || $name == 'created' || $name == 'updated' || $f['ptype'] == 'autoincr')
    {
        continue;
    }

    $desc = ! empty($f['desc']) ? $f['desc'] : $name;
    $rules = array();
    switch ($f['ptype'])
    {
    case 'int1': // 整数
    case 'int2':
    case 'int3':
    case 'int4':
        $rules[] = "array('is_int', '{$desc}必须是一个整数'),";
        break;
    case 'float': // 浮点数
    case 'double':
    case 'dec':
        $rules[] = "array('is_float', '{$desc}必须是一个浮点数'),";
        break;
    case 'date': // 日期
        $rules[] = "array('is_date', '{$desc}必须是一个有效的日期'),";
        break;
    case 'datetime': // 日期和时间
        $rules[] = "array('is_datetime', '{$desc}必须是一个有效的日期时间字符串'),";
        break;
    case 'time': // 时间
        $rules[] = "array('is_time', '{$desc}必须是一个有效的时间'),";
        break;
    case 'char': // 字符串
    case 'varchar':
    case 'text':
    case 'blob':
    case 'binary':
    case 'varbinary':
        if (! empty($f['length']))
        {
            if ($f['has_default'] == false && $f['not_null'] == true)
            {
                $rules[] = "array('not_empty', '{$desc}不能为空'),";
            }
            if ($f['length'] > 0)
            {
                $rules[] = "array('max_length', {$f['length']}, '{$desc}不能超过 {$f['length']} 个字符'),";
            }
        }
        break;

    }

    if (empty($rules))
    {
        continue;
    }

?>
                '<?php echo $name; ?>' => array
                (
<?php foreach ($rules as $rule) : ?>
                    <?php echo $rule; ?>

<?php endforeach; ?>

                ),

<?php endforeach; ?>

            ),
        );
    }


/* ------------------ 以下是自动生成的代码，不能修改 ------------------ */

    /**
     * 开启一个查询，查找符合条件的对象或对象集合
     *
     * @static
     *
     * @return QDB_Select
     */
    static function find()
    {
        $args = func_get_args();
        return QDB_ActiveRecord_Meta::instance(__CLASS__)->findByArgs($args);
    }

    /**
     * 返回当前 ActiveRecord 类的元数据对象
     *
     * @static
     *
     * @return QDB_ActiveRecord_Meta
     */
    static function meta()
    {
        return QDB_ActiveRecord_Meta::instance(__CLASS__);
    }


/* ------------------ 以上是自动生成的代码，不能修改 ------------------ */

}

