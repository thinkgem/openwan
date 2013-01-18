<?php

class QGenerator_Model extends QGenerator_Abstract
{

    /**
     * 生成指定名称的模型
     *
     * @param string $model_name
     * @param string $table_name
     * @param QDB_Adapter_Abstract $dbo
     *
     * @return QGenerator_Model
     */
    function generate($model_name, $table_name, $dbo)
    {
        $class_name = $this->_normalizeClassName($model_name);
        $dir = rtrim($this->_module->moduleDir(), '/\\') . '/model';
        $path = $this->_classFilePath($dir, $class_name);

        $this->_logClean();
        if (file_exists($path))
        {
            throw new Q_ClassFileExistsException($class_name, $path);
        }

        // 确定数据表名称
        $arr = explode('.', $table_name);
        if (isset($arr[1]))
        {
            $table_name = $arr[1];
            $schema = $arr[0] . '.';
        }
        else
        {
            $table_name = $arr[0];
            $schema = '';
        }

        if (is_null($dbo))
        {
            $dsn = Q::ini('db_dsn_pool/default');
            $dbtype = $dsn['driver'];
            $objid = "dbo_{$dbtype}_" .  md5(serialize($dsn));
            $dbo_class_name = 'QDB_Adapter_' . ucfirst($dbtype);
            $dbo = new $dbo_class_name($dsn, $objid);
        }

        $prefix = $dbo->getTablePrefix();
        if ($prefix && substr($table_name, 0, strlen($prefix)) == $prefix)
        {
            $table_name = substr($table_name, strlen($prefix));
        }

        $table_name = "{$schema}{$table_name}";
        $config = array('name' => $table_name, 'conn' => $dbo);
        $table = new QDB_Table($config);

        $meta = $table->columns();
        $pk = array();
        foreach ($meta as $field)
        {
            if ($field['pk'])
            {
                $pk[] = $field['name'];
            }
        }

        $data = array(
            'class_name'  => $class_name,
            'table_name'  => $table_name,
            'meta'        => $meta,
            'pk'          => $pk,
        );

        $content = $this->_parseTemplate('model', $data);
        $this->_createFile($path, $content);
        return $this;
    }
}

