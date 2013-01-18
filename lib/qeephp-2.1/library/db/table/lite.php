<?php
// $Id: lite.php 1937 2009-01-05 19:09:40Z dualface $

/**
 * 定义 QDB_Table_Lite 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: lite.php 1937 2009-01-05 19:09:40Z dualface $
 * @package database
 */

/**
 * QDB_Table_Lite类 在程序运行中创建QDB_Table对象而不用事先创建类
 *
 * QDB_Table_Lite类只是QDB_Table类的轻量级封装，
 * 提供了一种在程序运行期快速创建QDB_Table的途径。
 * 如果要给每个表绑定特定的方法，还是使用传统方式创建QDB_Table类
 *
 * @author yangyi.cn.gz@gmail.com
 * @version $Id: lite.php 1937 2009-01-05 19:09:40Z dualface $
 * @package database
 */
class QDB_Table_Lite extends QDB_Table {
    /**
     * 默认数据库连接
     *
     * @var QDB_Adapter_Abstract
     */
    protected static $_defaultConn;

    /**
     * 获得一个表对象实例
     *
     * $config内容参考QDB_Table的$config
     *
     * @param string $table_name
     * @param array $config
     *
     * @return QDB_Table
     */
    public static function instance($table_name, $config = array()) {
        static $tables = array();
        $parse = self::parseTableName($table_name);
        $id = md5($parse['schema'] . $parse['table']);

        if (!array_key_exists($id, $tables)) {
            $config['name'] = $parse['table'];
            if (!empty($parse['schema'])) { $config['schema'] = $parse['schema']; }

            if (!array_key_exists('conn', $config)) {
                if (!$conn = self::getDefaultConn()) {
                    $conn = QDB::getConn();
                    self::setDefaultConn($conn);
                }
            }

            $tables[$id] = new self($config);
        }

        return $tables[$id];
    }

    /**
     * 解析表名，把schema name和table name解析出来
     *
     * @param string $table_name
     *
     * @return array
     */
    protected static function parseTableName($table_name) {
        $parse = explode('.', $table_name);
        if (2 == count($parse)) {
            $schema = trim($parse[0], '\'"');
            $table = trim($parse[1], '\'"');
        } else {
            $schema = null;
            $table = trim($parse[0], '\'"');
        }
        return array('schema' => $schema, 'table' => $table);
    }

    /**
     * 获取数据库默认连接
     *
     * @return QDB_Adapter_Abstract
     */
    public static function getDefaultConn() {
        return self::$_defaultConn;
    }

    /**
     * 指定默认数据库连接
     *
     * @param string|QDB_Adapter_Abstract $conn
     */
    public static function setDefaultConn($conn) {
        self::$_defaultConn = self::_setupConnection($conn);
    }

    /**
     * 设置连接
     *
     * @param string|QDB_Adapter_Abstract $conn
     *
     * @return QDB_Adapter_Abstract
     */
    protected static function _setupConnection($conn) {
        if (is_string($conn)) {
            $conn = QDB::getConn($conn);
        }

        if (!$conn instanceof QDB_Adapter_Abstract) {
            throw new QDB_Table_Exception('Argument must be of type QDB_Adapter_Abstract, or dsn name');
        }

        return $conn;
    }
}

