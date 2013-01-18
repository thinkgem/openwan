<?php
// $Id: abstract.php 2631 2009-07-18 06:02:33Z dualface $

/**
 * 定义 QDB_Adapter_Abstract 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: abstract.php 2631 2009-07-18 06:02:33Z dualface $
 * @package database
 */

/**
 * QDB_Adapter_Abstract 是所有数据库驱动的抽象基础类
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: abstract.php 2631 2009-07-18 06:02:33Z dualface $
 * @package database
 */
abstract class QDB_Adapter_Abstract
{

    /**
     * 数据库连接信息
     *
     * @var mixed
     */
    protected $_dsn;

    /**
     * 数据库访问对象 ID
     *
     * @var string
     */
    protected $_id;

    /**
     * 默认的 schema
     *
     * @var string
     */
    protected $_schema = '';

    /**
     * 指示返回结果集的形式
     *
     * @var const
     */
    protected $_fetch_mode = QDB::FETCH_MODE_ASSOC;

    /**
     * 数据库连接句柄
     *
     * @var resource
     */
    protected $_conn;

    /**
     * 是否将查询语句保存到日志
     *
     * @var boolean
     */
    protected $_log_enabled = false;

    /**
     * 最后一次数据库操作的错误信息
     *
     * @var mixed
     */
    protected $_last_err;

    /**
     * 最后一次数据库操作的错误代码
     *
     * @var mixed
     */
    protected $_last_err_code;

    /**
     * 最近一次插入操作或者 nextID() 操作返回的插入 ID
     *
     * @var mixed
     */
    protected $_insert_id;

    /**
     * 指示事务启动次数
     *
     * @var int
     */
    protected $_trans_count = 0;

    /**
     * 指示事务执行期间是否发生了错误
     *
     * @var boolean
     */
    protected $_has_failed_query = false;

    /**
     * SAVEPOINT 堆栈
     *
     * @var array
     */
    protected $_savepoints_stack = array();

    /**
     * 用于描绘 true、false 和 null 的数据库值
     */
    protected $_true_value = 1;
    protected $_false_value = 0;
    protected $_null_value = 'NULL';

    /**
     * 数据库接受的日期格式
     */
    protected $_timestamp_format = 'Y-m-d H:i:s';

    /**
     * 指示驱动是否支持原生的参数绑定
     *
     * @var boolean
     */
    protected $_bind_enabled = true;

    /**
     * 指示使用何种样式的参数占位符
     *
     * @var string
     */
    protected $_param_style = QDB::PARAM_QM;

    /**
     * 指示数据库是否有自增字段功能
     *
     * @var boolean
     */
    protected $_has_insert_id = true;

    /**
     * 指示数据库是否能获得更新、删除操作影响的记录行数量
     *
     * @var boolean
     */
    protected $_affected_rows_enabled = true;

    /**
     * 指示数据库是否支持事务
     *
     * @var boolean
     */
    protected $_transaction_enabled = true;

    /**
     * 指示数据库是否支持事务中的 SAVEPOINT 功能
     *
     * @var boolean
     */
    protected $_savepoint_enabled = false;

    /**
     * 指示是否将查询结果中的字段名转换为全小写
     *
     * @var boolean
     */
    protected $_result_field_name_lower = false;

    /**
     * 构造函数
     *
     * @param mixed $dsn
     * @param string $id
     */
    protected function __construct($dsn, $id)
    {
        $this->_dsn = $dsn;
        $this->_id = $id;

        if (Q::ini('db_log_enabled'))
        {
            $this->logEnabled(true);
        }
    }

    /**
     * 返回数据库访问对象使用的 DSN
     *
     * @return mixed
     */
    function getDSN()
    {
        return $this->_dsn;
    }

    /**
     * 返回数据库访问对象的 ID
     *
     * @return string
     */
    function getID()
    {
        return $this->_id;
    }

    /**
     * 返回数据库对象对应的 schema
     *
     * @return string
     */
    function getSchema()
    {
        return $this->_schema;
    }

    /**
     * 返回数据库对象对应的表前缀
     *
     * @return string
     */
    function getTablePrefix()
    {
        return ! empty($this->_dsn['prefix']) ? $this->_dsn['prefix'] : '';
    }

    /**
     * 连接数据库，失败时抛出异常
     *
     * 如果已经连接到了数据库，再次连接不会造成任何影响。
     */
    abstract function connect();

    /**
     * 创建一个持久连接，失败时抛出异常
     *
     * 如果已经连接到了数据库，再次连接不会造成任何影响。
     */
    abstract function pconnect();

    /**
     * 强制创建一个新连接，失败时抛出异常
     *
     * 如果已经连接到了数据库，再次连接不会造成任何影响。
     */
    abstract function nconnect();

    /**
     * 关闭数据库连接
     */
    abstract function close();

    /**
     * 确认是否已经连接到数据库
     *
     * @return boolean
     */
    function isConnected()
    {
        return is_resource($this->_conn);
    }

    /**
     * 返回连接数据库的句柄
     *
     * @return resource
     */
    function handle()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        return $this->_conn;
    }

    /**
     * 选择要操作的 SCHEMA
     *
     * @param string $schema
     *
     * @return boolean
     */
    function setSchema($schema)
    {
        $this->_schema = $schema;
    }

    /**
     * 转义值
     *
     * 为了能够在 SQL 语句中安全的插入数据，应该用 qstr() 方法将数据中的特殊字符转义。
     *
     * example:
     * @code
     * $param = "It's live";
     * $param = $dbo->qstr($param);
     * $sql = "INSERT INTO posts (title) VALUES ({$param})";
     * $dbo->execute($sql);
     * @endcode
     *
     * 但更有效，而且更简单的方式是使用参数占位符：
     *
     * example:
     * @code
     * $param = "It's live";
     * $sql = "INSERT INTO posts (title) VALUES (?)";
     * $dbo->execute($sql, array($param));
     * @endcode
     *
     * 而且对于 Oracle 等数据库，由于限制每条 SQL 语句不能超过 4000 字节，
     * 因此在插入包含大量数据的记录时，必须使用参数占位符的形式。
     *
     * example:
     * @code
     * $title = isset($POST['title']) ? $POST['title'] : null;
     * $body = isset($POST['body']) ? $POST['body'] : null;
     *
     * ... 检查 $title、$body 是否为空 ...
     *
     * $sql = "INSERT INTO posts (title, body) VALUES (:title, :body)";
     * $dbo->execute($sql, array('title' => $title, 'body' => $body));
     * @endcode
     *
     * @param mixed $value
     *
     * @return string
     */
    abstract function qstr($value);

    /**
     * 将 SQL 中用“[]”指示的字段名转义为完全限定名
     *
     * @param string $sql 要处理的 SQL 字符串
     * @param string $table_name 转义字段名时，使用什么数据表名称
     * @param array $mapping 字段名映射，用于将字段名转换为映射名
     * @param callback $callback 如果提取到数据表名称，则调用回调函数进行转换
     *
     * @return string 转义后的 SQL 字符串
     */
    function qsql($sql, $table_name, array $mapping = null, $callback = null)
    {
        if (empty($sql)) { return ''; }

        $matches = null;
        preg_match_all('/\[[a-z0-9_][a-z0-9\-_\.]*\]|\[\*\]/i', $sql, $matches, PREG_OFFSET_CAPTURE);
        $matches = reset($matches);
        if (! is_array($mapping))
        {
            $mapping = array();
        }

        $out = '';
        $offset = 0;

        foreach ($matches as $m)
        {
            $len = strlen($m[0]);
            $field = substr($m[0], 1, $len - 2);
            $arr = explode('.', $field);
            switch (count($arr))
            {
            case 3:
                $f = (! empty($mapping[$arr[2]])) ? $mapping[$arr[2]] : $arr[2];
                $table = "{$arr[0]}.{$arr[1]}";
                break;
            case 2:
                $f = (! empty($mapping[$arr[1]])) ? $mapping[$arr[1]] : $arr[1];
                $table = $arr[0];
                break;
            default:
                $f = (! empty($mapping[$arr[0]])) ? $mapping[$arr[0]] : $arr[0];
                $table = $table_name;
            }

            if ($callback)
            {
                $table = call_user_func($callback, $table);
            }
            $field = $this->qid("{$table}.{$f}");
            $out .= substr($sql, $offset, $m[1] - $offset) . $field;
            $offset = $m[1] + $len;
        }
        $out .= substr($sql, $offset);

        return $out;
    }

    /**
     * 获得完全限定名
     *
     * @param string $name
     * @param string $alias
     * @param string $as
     *
     * @return string
     */
    function qid($name, $alias = null, $as = null)
    {
    	$name = str_replace('`', '', $name);
        if (strpos($name, '.') === false)
        {
            $name = $this->identifier($name);
        }
        else
        {
            $arr = explode('.', $name);
            foreach ($arr as $offset => $name)
            {
                if (empty($name))
                {
                    unset($arr[$offset]);
                }
                else
                {
                    $arr[$offset] = $this->identifier($name);
                }
            }
            $name = implode('.', $arr);
        }

        if ($alias)
        {
            return "{$name} {$as} " . $this->identifier($alias);
        }
        else
        {
            return $name;
        }
    }

    /**
     * 获得多个完全限定名
     *
     * @param array|string $names
     * @param string $as
     *
     * @return array
     */
    function qids($names, $as = null)
    {
        $arr = array();
        $names = Q::normalize($names);
        foreach ($names as $alias => $name)
        {
            if (! is_string($alias))
            {
                $alias = null;
            }
            $arr[] = $this->qid($name, $alias, $as);
        }
        return $arr;
    }

    /**
     * 获得一个名字的规范名
     *
     * @param string $name
     *
     * @return string
     */
    abstract function identifier($name);

    /**
     * 将 SQL 语句中的参数占位符替换为相应的参数值
     *
     * @param string $sql 要处理的 SQL 字符串
     * @param array $params 占位符对应的参数值
     * @param enum $param_style 占位符样式
     * @param boolean $return_parameters_count 是否返回占位符个数
     *
     * @return string|array
     */
    function qinto($sql, array $params = null, $param_style = null, $return_parameters_count = false)
    {
        if (is_null($param_style))
        {
            $param_style = $this->_param_style;
        }

        $callback = array(
            $this,
            'qstr'
        );
        switch ($param_style)
        {
        case QDB::PARAM_QM:
        case QDB::PARAM_DL_SEQUENCE:
            if ($param_style == QDB::PARAM_QM)
            {
                $parts = explode('?', $sql);
            }
            else
            {
                $parts = preg_split('/\$[0-9]+/', $sql);
            }
            $str = $parts[0];
            $offset = 1;
            foreach ($params as $arg_value)
            {
                if (! isset($parts[$offset]))
                {
                    break;
                }
                if (is_array($arg_value))
                {
                    $arg_value = array_unique($arg_value);
                    $arg_value = array_map($callback, $arg_value);
                    $str .= implode(',', $arg_value) . $parts[$offset];
                }
                else
                {
                    $str .= $this->qstr($arg_value) . $parts[$offset];
                }
                $offset ++;
            }
            if ($return_parameters_count)
            {
                return array(
                    $str,
                    count($parts) - 1
                );
            }
            else
            {
                return $str;
            }

        case QDB::PARAM_CL_NAMED:
        case QDB::PARAM_AT_NAMED:
            $split = ($param_style == QDB::PARAM_CL_NAMED) ? ':' : '@';
            $parts = preg_split('/(' . $split . '[a-z0-9_\-]+)/i', $sql, - 1, PREG_SPLIT_DELIM_CAPTURE);
            $max = count($parts);
            $str = $parts[0];

            for ($offset = 1; $offset < $max; $offset += 2)
            {
                $arg_name = substr($parts[$offset], 1);
                if (! isset($params[$arg_name]))
                {
                    throw new QDB_Exception($sql, __('Invalid parameter "%s" for "%s"', $arg_name, $sql), 0);
                }
                if (is_array($params[$arg_name]))
                {
                    $arg_value = array_map($callback, $params[$arg_name]);
                    $str .= implode(',', $arg_value) . $parts[$offset + 1];
                }
                else
                {
                    $str .= $this->qstr($params[$arg_name]) . $parts[$offset + 1];
                }
            }
            if ($return_parameters_count)
            {
                return array( $str, intval($max / 2) - 1 );
            }
            else
            {
                return $str;
            }

        default:
            return $sql;
        }
    }

    /**
     * 插入一条记录到数据库
     *
     * @param string $table_name 要操作的数据表
     * @param array $row 要插入的记录数据
     * @param string|array $restricted_fields 限定只使用哪些字段
     */
    function insert($table_name, array $row, array $restricted_fields = null)
    {
        $holders = $this->getPlaceholder($row, $restricted_fields);
        $sql = 'INSERT INTO ' . $this->qid($table_name) . ' (';

        if ($this->_bind_enabled)
        {
            // 使用参数绑定
            $fields = array();
            $values = array();
            foreach ($holders as $key => $h)
            {
                list ($holder, $field_name) = $h;
                $fields[] = $field_name;
                $values[$key] = $holder;
            }
            $sql .= implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';

            $stmt = $this->prepare($sql);
            foreach ($values as $key => $holder)
            {
                if ($row[$key] instanceof QDB_Expr)
                {
                    $stmt->bindParam($holder, $row[$key]->formatToString($this, $table_name));
                }
                else
                {
                    $stmt->bindParam($holder, $row[$key]);
                }
            }
            $stmt->execute();
        }
        else
        {
            $fields = array();
            $values = array();
            foreach ($holders as $key => $h)
            {
                list (, $field_name) = $h;
                $fields[] = $field_name;
                if ($row[$key] instanceof QDB_Expr)
                {
                    $values[] = $this->qstr($row[$key]->formatToString($this, $table_name));
                }
                else
                {
                    $values[] = $this->qstr($row[$key]);
                }
                unset($row[$key]);
            }
            $sql .= implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
            $this->execute($sql);
        }
    }

    /**
     * 更新数据库中的记录
     *
     * @param string $table_name 要操作的数据表
     * @param array|QDB_Expr $row 要更新的数据
     */
    function update($table_name, $row, array $where = null, array $restricted_fields = null)
    {
        $sql = 'UPDATE ' . $this->qid($table_name) . ' SET ';
        list ($where) = $this->parseSQLInternal($table_name, $where);

        if (!is_array($row) && !($row instanceof QDB_Expr))
        {
            throw new QDB_Exception(__('Type mismatch'));
        }

        if (!is_array($row))
        {
            $sql .= $row->formatToString($this, $table_name);
            if ($where)
            {
                $sql .= ' WHERE ' . $where;
            }
            $this->execute($sql);
            return;
        }

        $holders = $this->getPlaceholder($row, $restricted_fields);
        if ($this->_bind_enabled)
        {
            // 使用参数绑定
            $pairs = array();
            $values = array();
            foreach ($holders as $key => $h)
            {
                list ($holder, $field_name) = $h;
                $pairs[] = $field_name . ' = ' . $holder;
                $values[$key] = $holder;
            }
            $sql .= implode(', ', $pairs);
            if ($where)
            {
                $sql .= ' WHERE ' . $where;
            }

            $stmt = $this->prepare($sql);
            foreach ($values as $key => $holder)
            {
                if ($row[$key] instanceof QDB_Expr)
                {
                    $stmt->bindParam($holder, $row[$key]->formatToString($this, $table_name));
                }
                else
                {
                    $stmt->bindParam($holder, $row[$key]);
                }
            }
            $stmt->execute();
        }
        else
        {
            $pairs = array();
            foreach ($holders as $key => $h)
            {
                list ($holder, $field_name) = $h;
                $pair = $field_name . ' = ';
                if ($row[$key] instanceof QDB_Expr)
                {
                    $pair .= $this->qstr($row[$key]->formatToString($this, $table_name));
                }
                else
                {
                    $pair .= $this->qstr($row[$key]);
                }
                $pairs[] = $pair;
            }
            $sql .= implode(', ', $pairs);
            if ($where)
            {
                $sql .= ' WHERE ' . $where;
            }
            $this->execute($sql);
        }
    }

    /**
     * 删除指定数据表中符合条件的记录
     */
    function delete($table_name, array $where = null)
    {
        list ($where) = $this->parseSQLInternal($table_name, $where);
        $sql = 'DELETE FROM ' . $this->qid($table_name);
        if ($where)
        {
            $sql .= ' WHERE ' . $where;
        }
        $this->execute($sql);
    }

    /**
     * 返回输入数组键名及其对应的参数占位符和转义后的字段名
     *
     * @param array $inputarr
     * @param array $restricted_fields
     *
     * @return array
     */
    function getPlaceholder(array $inputarr, array $restricted_fields = null, $param_style = null)
    {
        if (! is_null($restricted_fields) && $this->_result_field_name_lower)
        {
            $restricted_fields = array_change_key_case($restricted_fields, CASE_LOWER);
        }
        if (is_null($param_style))
        {
            $param_style = $this->_param_style;
        }

        $holders = array();
        foreach (array_keys($inputarr) as $offset => $key)
        {
            if ($restricted_fields && ! isset($restricted_fields[$key]))
            {
                continue;
            }
            switch ($param_style)
            {
            case QDB::PARAM_QM:
                $holders[$key] = array( '?', $this->identifier($key) );
                break;
            case QDB::PARAM_DL_SEQUENCE:
                $holders[$key] = array( '$' . ($offset + 1), $this->identifier($key) );
                break;
            default:
                $holders[$key] = array("{$param_style}{$key}", $this->identifier($key) );
            }
        }

        return $holders;
    }

    /**
     * 为数据表产生下一个序列值，失败时抛出异常
     *
     * 调用 nextID() 方法，将获得指定名称序列的下一个值。
     * 此处所指的序列，是指一个不断增大的数字。
     *
     * 假设本次调用 nextID() 返回 3，那么下一次调用 nextID() 就会返回一个比 3 更大的值。
     * nextID() 返回的序列值，可以作为记录的主键字段值，以便确保插入记录时总是使用不同的主键值。
     *
     * 可以使用多个序列，只需要指定不同的 $seq_name 参数即可。
     *
     * 在不同的数据库中，序列的产生方式各有不同。
     * PostgreSQL、Oracle 等数据库中，会使用数据库自带的序列功能来实现。
     * 其他部分数据库会创建一个后缀为 _seq 表来存放序列值。
     *
     * 例如 $seq_name 为 posts，则存放该序列的表名称为 posts_seq。
     *
     * @param string $table_name
     * @param string $field_name
     * @param string $start_value
     *
     * @return int
     */
    abstract function nextID($table_name, $field_name, $start_value = 1);

    /**
     * 创建一个新的序列，失败时抛出异常
     *
     * 调用 nextID() 时，如果指定的序列不存在，则会自动调用 create_seq() 创建。
     * 开发者也可以自行调用 create_seq() 创建一个新序列。
     *
     * @param string $seq_name
     * @param int $start_value
     */
    abstract function createSeq($seq_name, $start_value = 1);

    /**
     * 删除一个序列，失败时抛出异常
     *
     * @param string $seq_name
     */
    abstract function dropSeq($seq_name);

    /**
     * 获取自增字段的最后一个值或者 nextID() 方法产生的最后一个值
     *
     * 某些数据库（例如 MySQL）可以将一个字段设置为自增。
     * 也就是每次往数据表插入一条记录，该字段的都会自动填充一个更大的新值。
     *
     * insertID() 方法可以获得最后一次插入记录时产生的自增字段值，或者最后一次调用 nextID() 返回的值。
     *
     * 如果在一次操作中，插入了多条记录，那么 insertID() 有可能返回的是第一条记录的自增值。
     * 这个问题是由数据库本身的实现决定的。
     *
     * @return int
     */
    abstract function insertID();

    /**
     * 返回最近一次数据库操作受到影响的记录数
     *
     * 这些操作通常是插入记录、更新记录以及删除记录。
     * 不同的数据库对于其他操作，也可能影响到 affectedRows() 返回的值。
     *
     * @return int
     */
    abstract function affectedRows();

    /**
     * 执行一个查询，返回一个查询对象或者 boolean 值，出错时抛出异常
     *
     * $sql 是要执行的 SQL 语句字符串，而 $inputarr 则是提供给 SQL 语句中参数占位符需要的值。
     *
     * 如果执行的查询是诸如 INSERT、DELETE、UPDATE 等不会返回结果集的操作，
     * 则 execute() 执行成功后会返回 true，失败时将抛出异常。
     *
     * 如果执行的查询是 SELECT 等会返回结果集的操作，
     * 则 execute() 执行成功后会返回一个 DBO_Result 对象，失败时将抛出异常。
     *
     * QDB_Result_Abstract 对象封装了查询结果句柄，而不是结果集。
     * 因此要获得查询的数据，需要调用 QDB_Result_Abstract 的 fetchAll() 等方法。
     *
     * 如果希望执行 SQL 后直接获得结果集，可以使用驱动的 getAll()、getRow() 等方法。
     *
     * example:
     * @code
     * $sql = "INSERT INTO posts (title, body) VALUES (?, ?)";
     * $dbo->execute($sql, array($title, $body));
     * @endcode
     *
     * example:
     * @code
     * $sql = "SELECT * FROM posts WHERE post_id < 12";
     * $handle = $dbo->execute($sql);
     * $rowset = $handle->fetchAll();
     * $handle->free();
     * @endcode
     *
     * @param string $sql
     * @param array $inputarr
     *
     * @return QDB_Result_Abstract
     */
    abstract function execute($sql, $inputarr = null);

    /**
     * 发起一个查询，获得一个 QDB_Select 查询对象
     *
     * @return QDB_Select
     */
    function select()
    {
        $select = new QDB_Select($this);
        $args = func_get_args();
        if (! empty($args))
        {
            call_user_func_array(array( $select, 'where' ), $args);
        }
        return $select;
    }

    /*
	 * 进行限定范围的查询，并且返回 QDB_Result_Abstract 对象，出错时抛出异常
	 *
	 * 使用 selectLimit()，可以限定 SELECT 查询返回的结果集的大小。
	 * $length 参数指定结果集最多包含多少条记录。而 $offset 参数则指定在查询结果中，从什么位置开始提取记录。
	 *
	 * 假设 SELECT * FROM posts ORDER BY post_id ASC 的查询结果一共有 500 条记录。
	 * 现在通过指定 $length 为 20，则可以限定只提取其中的 20 条记录作为结果集。
	 * 进一步指定 $offset 参数为 59，则可以从查询结果的第 60 条记录开始提取 20 条作为结果集。
	 *
	 * 注意：$offset 参数是从 0 开始计算的。因此 $offset 为 59 时，实际上是从第 60 条记录开始提取。
	 *
	 * selectLimit() 并不直接返回结果集，而是返回 QDB_Result_Abstract 对象。
	 * 因此需要调用 QDB_Result_Abstract 对象的 fetchAll() 等方法来获得数据。
	 *
	 * example:
	 * @code
	 * $sql = "SELECT * FROM posts WHERE created > ? ORDER BY post_id DESC";
	 * $length = 20;
	 * $offset = 0;
	 * $current = time() - 60 * 60 * 24 * 15; // 查询创建时间在 15 天内的记录
	 * $handle = $dbo->selectLimit($sql, $offset, $length, array($current));
	 * $rowset = $handle->fetchAll();
	 * $handle->free();
	 * @endcode
	 *
	 * @param string $sql
	 * @param int $offset
     * @param int $length
	 * @param array $inputarr
	 *
	 * @return QDB_Result_Abstract
	 */
    abstract function selectLimit($sql, $offset = 0, $length = 30, array $inputarr = null);

    /**
     * 执行一个查询并返回记录集，失败时抛出异常
     *
     * getAll() 等同于执行下面的代码：
     *
     * @code
     * $rowset = $dbo->execute($sql, $inputarr)->fetchAll();
     * @endcode
     *
     * @param string $sql
     * @param array $inputarr
     *
     * @return array
     */
    function getAll($sql, array $inputarr = null)
    {
        return $this->execute($sql, $inputarr)->fetchAll();
    }

    /**
     * 执行查询，返回第一条记录
     *
     * @param string $sql
     * @param array $inputarr
     *
     * @return mixed
     */
    function getRow($sql, array $inputarr = null)
    {
        return $this->selectLimit($sql, 0, 1, $inputarr)->fetchRow();
    }

    /**
     * 执行查询，返回第一条记录的第一个字段
     *
     * @param string $sql
     * @param array $inputarr
     *
     * @return mixed
     */
    function getOne($sql, array $inputarr = null)
    {
        return $this->selectLimit($sql, 0, 1, $inputarr)->fetchOne();
    }

    /**
     * 执行查询，返回结果集的指定列
     *
     * @param string|resource $sql
     * @param int $col 要返回的列，0 为第一列
     * @param array $inputarr
     *
     * @return mixed
     */
    function getCol($sql, $col = 0, array $inputarr = null)
    {
        return $this->execute($sql, $inputarr)->fetchCol($col);
    }

    /**
     * 将 unix timestamp 转换为数据库可以接受的日期格式
     *
     * example:
     * @code
     * // 假设 created 是 DATETIME 类型的字段
     * $sql = "INSERT INTO posts (title, body, created) VALUES (?, ?, ?)";
     * $created = $dbo->dbTimestamp(time());
     * $dbo->execute($sql, array($title, $body, $created));
     * @endcode
     *
     * @param int $timestamp
     *
     * @return string
     */
    function dbTimestamp($timestamp)
    {
        return date($this->_timestamp_format, $timestamp);
    }

    /**
     * 开始一个事务
     *
     * 调用 startTrans() 开始一个事务后，应该在关闭数据库连接前调用 completeTrans() 提交或回滚事务。
     */
    abstract function startTrans();

    /**
     * 完成事务，根据事务期间的查询是否出错决定是提交还是回滚事务
     *
     * 如果 $commit_on_no_errors 参数为 true，当事务期间所有查询都成功完成时，则提交事务，否则回滚事务；
     * 如果 $commit_on_no_errors 参数为 false，则强制回滚事务。
     *
     * @param boolean $commit_on_no_errors
     */
    abstract function completeTrans($commit_on_no_errors = true);

    /**
     * 指示在调用 completeTrans() 时回滚事务
     */
    function setTransFailed()
    {
        $this->_has_failed_query = true;
    }

    /**
     * 检查事务过程中是否出现失败的查询
     */
    function hasFailedQuery()
    {
        return $this->_has_failed_query;
    }

    /**
     * 返回指定数据表（或者视图）的元数据
     *
     * 返回的结果是一个二维数组，每一项为一个字段的元数据。
     * 每个字段包含下列属性：
     *
     * -   name:            字段名
     * -   scale:           小数位数
     * -   type:            字段类型
     * -   ptype:           简单字段类型（与数据库无关）
     * -   length:          最大长度
     * -   not_null:        是否不允许保存 NULL 值
     * -   pk:              是否是主键
     * -   auto_incr:       是否是自动增量字段
     * -   binary:          是否是二进制数据
     * -   unsigned:        是否是无符号数值
     * -   has_default:     是否有默认值
     * -   default:         默认值
     * -   desc:            字段描述
     *
     * ptype 是下列值之一：
     *
     * -   c char/varchar 等类型
     * -   x text 等类型
     * -   b 二进制数据
     * -   n 数值或者浮点数
     * -   d 日期
     * -   t TimeStamp
     * -   l 逻辑布尔值
     * -   i 整数
     * -   r 自动增量
     * -   p 非自增的主键字段
     *
     * @param string $table_name
     *
     * @return array
     */
    abstract function metaColumns($table_name);

    /**
     * 获得所有数据表的名称
     *
     * @param string $pattern
     * @param string $schema
     *
     * @return array
     */
    abstract function metaTables($pattern = null, $schema = null);

    /**
     * 确定驱动是否支持参数绑定
     *
     * @param boolean $enabled
     *
     * @return boolean
     */
    function bindEnabled($enabled = null)
    {
        if (!is_null($enabled))
        {
            $this->_bind_enabled = (bool)$enabled;
        }
        return $this->_bind_enabled;
    }

    /**
     * 确定是否把查询语句保存到日志
     *
     * @param boolean $enabled
     *
     * @return boolean
     */
    function logEnabled($enabled = null)
    {
        if (!is_null($enabled))
        {
            $this->_log_enabled = (bool)$enabled;
        }
        return $this->_log_enabled;
    }

    /**
     * 返回驱动使用的参数占位符样式
     *
     * @return string
     */
    function paramStyle()
    {
        return $this->_param_style;
    }

    /**
     * 分析 SQL 中的字段名、查询条件，返回符合规范的 SQL 语句
     *
     * @param string $table_name
     *
     * @return string
     */
    function parseSQL($table_name)
    {
        $args = func_get_args();
        array_shift($args);
        list ($where) = $this->parseSQLInternal($table_name, $args);
        return $where;
    }

    /**
     * 分析 SQL 中的字段名、查询条件，返回符合规范的 SQL 语句（内部调用版本）
     *
     * 与 parseSQL() 的区别在于 parseSQLInternal() 用第三参数来传递所有的占位符参数及参数值。
     * 并且 parseSQLInternal() 的返回结果是一个数组，
     * 分别由处理后的 SQL 语句、从 SQL 语句中分析出来的数据表名称、分析用到的参数个数组成。
     *
     * @code
     * list($sql, $used_tables, $args_count) = parseSQLInternal(...);
     * @endcode
     *
     * @param string $table_name
     * @param array $args
     *
     * @return array
     */
    function parseSQLInternal($table_name, array $args = null)
    {
        if (empty($args))
        {
            return array( null, null, null );
        }
        $sql = array_shift($args);

        if (is_array($sql))
        {
            return $this->_parseSQLArray($table_name, $sql, $args);
        }
        else
        {
            return $this->_parseSQLString($table_name, $sql, $args);
        }
    }

    /**
     * 按照模式 2（数组）对查询条件进行分析
     *
     * @param string $table_name
     * @param array $arr
     * @param array $args
     *
     * @return array
     */
    protected function _parseSQLArray($table_name, array $arr, array $args)
    {
        static $keywords = array(
            '(' => true,
            'AND' => true,
            'OR' => true,
            'NOT' => true,
            'BETWEEN' => true,
            'CASE' => true,
            '&&' => true,
            '||' => true,
            '=' => true,
            '<=>' => true,
            '>=' => true,
            '>' => true,
            '<=' => true,
            '<' => true,
            '<>' => true,
            '!=' => true,
            'IS' => true,
            'LIKE' => true
        );

        $parts = array();
        $next_op = '';
        $args_count = 0;
        $used_tables = array();

        foreach ($arr as $key => $value)
        {
            if (is_int($key))
            {
                /**
                 * 如果键名是整数，则判断键值是否是关键字或 ')' 符号。
                 *
                 * 如果键值不是关键字，则假定为需要再分析的 SQL，需要再次调用 parseSQLInternal() 进行分析。
                 */
                if (is_string($value) && isset($keywords[$value]))
                {
                    $next_op = '';
                    $sql = $value;
                }
                elseif ($value == ')')
                {
                    $next_op = 'AND';
                    $sql = $value;
                }
                else
                {
                    if ($next_op != '')
                    {
                        $parts[] = $next_op;
                    }
                    array_unshift($args, $value);
                    list ($sql, $u_t, $args_count) = $this->parseSQLInternal($table_name, $args);
                    array_shift($args);
                    if (empty($sql))
                    {
                        continue;
                    }
                    $used_tables = array_merge($used_tables, $u_t);
                    if ($args_count > 0)
                    {
                        $args = array_slice($args, $args_count);
                    }
                    $next_op = 'AND';
                }
                $parts[] = $sql;
            }
            else
            {
                /**
                 * 如果键名是字符串，则假定为字段名
                 */
                if ($next_op != '')
                {
                    $parts[] = $next_op;
                }

                if (strpos($key, '.'))
                {
                    // 如果字段名带有 .，则需要分离出数据表名称和 schema
                    $key = explode('.', $key);
                    switch (count($key))
                    {
                    case 3:
                        $used_tables[] = "{$key[0]}.{$key[1]}";
                        $field = $this->qid("{$key[0]}.{$key[1]}.{$key[2]}");
                        break;
                    case 2:
                        $used_tables[] = $key[0];
                        $field = $this->qid("{$key[0]}.{$key[1]}");
                        break;
                    }
                }
                else
                {
                    $field = $this->qid("{$table_name}.{$key}");
                }

                if (is_array($value))
                {
                    // 如果 $value 是数组，则假定为 IN (??, ??) 表达式
                    $value = array_unique($value);
                    $values = array();
                    foreach ($value as $v)
                    {
                        if ($v instanceof QDB_Expr)
                        {
                            $values[] = $v->formatToString($this, $table_name);
                        }
                        else
                        {
                            $values[] = $this->qstr($v);
                        }
                    }
                    $parts[] = $field . ' IN (' . implode(',', $values) . ')';
                    unset($values);
                    unset($value);
                }
                else
                {
                    if ($value instanceof QDB_Expr)
                    {
                        $value = $value->formatToString($this, $table_name);
                    }
                    else
                    {
                        $value = $this->qstr($value);
                    }
                    $parts[] = $field . ' = ' . $value;
                }
                $next_op = 'AND';
            }
        }

        return array(
            implode(' ', $parts),
            $used_tables,
            $args_count
        );
    }

    /**
     * 按照模式 1（字符串）对查询条件进行分析
     *
     * @param string $table_name
     * @param string $where
     * @param array $args
     *
     * @return array
     */
    protected function _parseSQLString($table_name, $where, array $args)
    {
        $matches = array();
        preg_match_all('/\[[a-z0-9_][a-z0-9_\.]*\]/i', $where, $matches, PREG_OFFSET_CAPTURE);
        $matches = reset($matches);

        $out = '';
        $offset = 0;
        $used_tables = array();
        foreach ($matches as $m)
        {
            $len = strlen($m[0]);
            $field = substr($m[0], 1, $len - 2);
            $arr = explode('.', $field);
            switch (count($arr))
            {
            case 3:
                $schema = $arr[0];
                $table = $arr[1];
                $field = $arr[2];
                $used_tables[] = $schema . '.' . $table;
                break;
            case 2:
                $schema = null;
                $table = $arr[0];
                $field = $arr[1];
                $used_tables[] = $table;
                break;
            default:
                $schema = null;
                $table = $table_name;
                $field = $arr[0];
            }

            $field = $this->identifier("{$schema}.{$table}.{$field}");
            $out .= substr($where, $offset, $m[1] - $offset) . $field;
            $offset = $m[1] + $len;
        }
        $out .= substr($where, $offset);
        $where = $out;

        // 分析查询条件中的参数占位符
        $args_count = null;

        if (strpos($where, '?') !== false)
        {
            // 使用 ? 作为占位符的情况
            $ret = $this->qinto($where, $args, QDB::PARAM_QM, true);
        }
        elseif (strpos($where, ':') !== false)
        {
            // 使用 : 开头的命名参数占位符
            $ret = $this->qinto($where, reset($args), QDB::PARAM_CL_NAMED, true);
        }
        else
        {
            $ret = $where;
        }
        if (is_array($ret))
        {
            list ($where, $args_count) = $ret;
        }
        else
        {
            $where = $ret;
        }
        return array(
            $where,
            $used_tables,
            $args_count
        );
    }

    /**
     * 关闭数据库连接后清理资源
     */
    protected function _clear()
    {
        $this->_conn = null;
        $this->_last_err = null;
        $this->_last_err_code = null;
        $this->_insert_id = null;
        $this->_trans_count = 0;
    }
}

