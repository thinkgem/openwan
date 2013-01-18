<?php
// $Id: abstract.php 2677 2009-12-18 07:07:30Z firzen $

/**
 * 定义 QDB_ActiveRecord_Association_Abstract 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: abstract.php 2677 2009-12-18 07:07:30Z firzen $
 * @package orm
 */

/**
 * QDB_ActiveRecord_Association_Abstract 封装 ActiveRecord 之间的关联关系
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: abstract.php 2677 2009-12-18 07:07:30Z firzen $
 * @package orm
 */
abstract class QDB_ActiveRecord_Association_Abstract
{
    /**
     * 目标数据映射到来源数据的哪一个键，同时 mapping_name 也是关联的名字
     *
     * @var string
     */
    public $mapping_name;

    /**
     * 确定关联关系时，来源方使用哪一个键
     *
     * @var string
     */
    public $source_key;

    /**
     * 确定关联关系时，目标方使用哪一个键
     *
     * @var string
     */
    public $target_key;

    public $source_key_2nd;
    public $target_key_2nd;
    public $mid_common_key;
    /**
     * 指示是否读取目标数据
     *
     * skip|false   - 不读取
     * all|true     - 读取所有关联数据
     * 整数         - 仅读取指定个数的目标数据
     * 数组         - 由 count 和 offset 组成的数组，指定读取目标数据的起始位置和个数
     *
     * 对于所有类型的关联，on_find 的默认值都是 all
     *
     * @var string|int|array
     */
    public $on_find = 'all';

    /**
     * 查询目标数据时要使用的查询条件
     *
     * @var array|string
     */
    public $on_find_where = null;

    /**
     * 查询目标数据时的排序
     *
     * @var string
     */
    public $on_find_order = null;

    /**
     * 查询目标数据时要查询哪些属性
     *
     * @var array|string
     */
    public $on_find_keys = '*';

    /**
     * 指示在来源数据时，如何处理相关的目标数据
     *
     * cascade|true - 删除关联的目标数据
     * set_null     - 将目标数据的 target_key 键设置为 NULL
     * set_value    - 将目标数据的 target_key 键设置为指定的值
     * skip|false   - 不处理关联记录
     * reject       - 拒绝对来源数据的删除
     *
     * 对于 has many 和 has one 关联，默认值则是 cascade
     * 对于 belongs to 和 many to many 关联，on_delete 设置固定为 skip
     *
     * @var string|boolean
     */
    public $on_delete = 'skip';

    /**
     * 如果 on_delete 为 set_value，则通过 on_delete_set_value 指定要填充的值
     *
     * @var mixed
     */
    public $on_delete_set_value = null;

    /**
     * 指示保存来源数据时，是否保存关联的目标数据
     *
     * save|true    - 根据目标数据是否有 ID 或主键值来决定是创建新的目标数据还是更新已有的目标数据
     * create       - 强制创建新的目标数据
     * update       - 强制更新已有的目标数据
     * replace      - 尝试替换已有的目标数据，如果不存在则新建
     * skip|false   - 保存来源数据时，不保存目标数据
     * only_create  - 仅仅保存需要新建的目标数据
     * only_update  - 仅仅保存需要更新的目标数据
     *
     * 对于 many to many 关联，on_save 的默认值是 skip
     * 对于 has many 关联，on_save 的默认值是 save
     * 对于 has on 关联，on_save 的默认值是 replace
     * 对于 belongs to 关联，on_save 设置固定为 skip
     *
     * @var string
     */
    public $on_save = 'skip';

    /**
     * 查询多对多关联时，中间数据使用哪一个键关联到来源方
     *
     * @var string
     */
    public $mid_source_key;

    /**
     * 查询多对多关联时，中间数据使用哪一个键关联到目标方
     *
     * @var string
     */
    public $mid_target_key;

    /**
     * 查询多对多关联时，是否也要把中间数据放到结果中
     *
     * 如果 mid_on_find_keys 为 null，则不查询。如果为特定属性名，
     * 则会根据 mid_mapping_to 将中间数据指定为目标数据的一个键。
     *
     * @var array|string
     */
    public $mid_on_find_keys = null;

    /**
     * 查询多对多关联时，中间数据要指定到目标数据的哪一个键
     *
     * @var string
     */
    public $mid_mapping_to;

    /**
     * 指示关联两个数据时，是一对一关联还是一对多关联
     *
     * @var boolean
     */
    public $one_to_one = false;

    /**
     * 关联的类型
     *
     * @var int
     */
    public $type;

    /**
     * 当 enabled 为 false 时，表数据入口的任何操作都不会处理该关联
     *
     * enabled 的优先级高于 linkRead、linkCreate、linkUpdate 和 linkRemove。
     *
     * @var boolean
     */
    public $enabled = true;

    /**
     * 关联中的来源对象
     *
     * @var QDB_ActiveRecord_Meta
     */
    public $source_meta;

    /**
     * 关联到哪一个 ActiveRecord 类
     *
     * @var QDB_ActiveRecord_Meta
     */
    public $target_meta;

    /**
     * 封装中间表数据的 ActiveRecord 元信息对象
     *
     * @var QDB_ActiveRecord_Meta
     */
    public $mid_meta;

    /**
     * 封装中间表的表数据入口对象
     *
     * @var QDB_Table
     */
    public $mid_table;

    /**
     * 指示关联是否已经初始化
     *
     * @var boolean
     */
    protected $_inited = false;

    /**
     * 初始化参数
     *
     * @var array
     */
    protected $_init_config;

    /**
     * 用于初始化关联对象的参数
     *
     * @var array
     */
    protected static $_init_config_keys = array
    (
        'mapping_name',
        'source_key',
        'target_key',
        'source_key_2nd',
        'target_key_2nd',
        'on_find',
        'on_find_where',
        'on_find_order',
        'on_find_keys',
        'on_delete',
        'on_delete_set_value',
        'on_save',
        'mid_source_key',
        'mid_target_key',
    	'mid_common_key',
        'mid_on_find_keys',
        'mid_mapping_to',
        'enabled',
    );

    /**
     * 构造函数
     *
     * @param int $type
     * @param array $config
     * @param QDB_ActiveRecord_Meta $source_meta
     *
     * @return QDB_ActiveRecord_Association_Abstract
     */
    protected function __construct($type, array $config, QDB_ActiveRecord_Meta $source_meta)
    {
        $this->type = $type;

        #IFDEF DEBUG
        QLog::log(__METHOD__ . "({$config['mapping_name']})", QLog::DEBUG);
        #ENDIF

        foreach (self::$_init_config_keys as $key)
        {
            if (! empty($config[$key]))
            {
                $this->{$key} = $config[$key];
            }
        }

        $this->_init_config = $config;
        $this->source_meta = $source_meta;
    }

    /**
     * 创建一个关联对象
     *
     * @param int $type
     * @param array $config
     * @param QDB_ActiveRecord_Meta $source_meta
     *
     * @return QDB_ActiveRecord_Association_Abstract
     */
    static function create($type, array $config, QDB_ActiveRecord_Meta $source_meta)
    {
        if (empty($config['mapping_name']))
        {
            // LC_MSG: 创建关联必须指定关联的 mapping_name 属性.
            throw new QDB_ActiveRecord_Association_Exception(__('创建关联必须指定关联的 mapping_name 属性.'));
        }
        else
        {
        	$config['mapping_name'] = strtolower($config['mapping_name']);
        }

        switch ($type)
        {
        case QDB::HAS_ONE:
            return new QDB_ActiveRecord_Association_HasOne(QDB::HAS_ONE, $config, $source_meta);
        case QDB::HAS_MANY:
            return new QDB_ActiveRecord_Association_HasMany(QDB::HAS_MANY, $config, $source_meta);
        case QDB::BELONGS_TO:
            return new QDB_ActiveRecord_Association_BelongsTo(QDB::BELONGS_TO, $config, $source_meta);
        case QDB::MANY_TO_MANY:
            return new QDB_ActiveRecord_Association_ManyToMany(QDB::MANY_TO_MANY, $config, $source_meta);
        default:
            // LC_MSG: 无效的关联类型 "%s".
            throw new QDB_ActiveRecord_Association_Exception(__('无效的关联类型 "%s".', $type));
        }
    }

    /**
     * 注册回调方法
     *
     * @param array $assoc_info
     */
    function registerCallbacks(array $assoc_info)
    {
        return $this;
    }

    /**
     * 禁用当前关联
     *
     * @return QDB_ActiveRecord_Association_Abstract
     */
    function disable()
    {
        $this->enabled = false;
        return $this;
    }

    /**
     * 启用当前关联
     *
     * @return QDB_ActiveRecord_Association_Abstract
     */
    function enable()
    {
        $this->enabled = true;
        return $this;
    }

    /**
     * 初始化关联
     *
     * @return QDB_ActiveRecord_Association_Abstract
     */
    function init()
    {
    	if (!$this->_inited)
    	{
            $this->target_meta = QDB_ActiveRecord_Meta::instance($this->_init_config['target_class']);
	        $this->source_key_alias = $this->mapping_name . '_source_key';
	        $this->target_key_alias = $this->mapping_name . '_target_key';

            $this->_inited = true;
    	}
        return $this;
    }

    /**
     * 源对象保存时调用
     *
     * @param QDB_ActiveRecord_Abstract $source
     * @param int $recursion
     *
     * @return QDB_ActiveRecord_Association_Abstract
     */
    abstract function onSourceSave(QDB_ActiveRecord_Abstract $source, $recursion);

    /**
     * 源对象销毁时调用
     *
     * @param QDB_ActiveRecord_Abstract $source
     *
     * @return QDB_ActiveRecord_Association_Abstract
     */
    abstract function onSourceDestroy(QDB_ActiveRecord_Abstract $source);
}

