<?php

class Controller_Generate extends Controller_Abstract
{
    /**
     * 列出所有控制器
     */
    function actionControllers()
    {
        $controllers = new QColl('QReflection_Controller');
        foreach ($this->_managed_app->modules() as $module)
        {
            $controllers->append($module->controllers());
        }

        $this->_view['controllers'] = $controllers;
    }

    /**
     * 创建一个新控制器
     */
    function actionNewController()
    {
        $name = $this->_context->new_controller_name;
        if (!empty($name))
        {
            try
            {
                $log = $this->_managed_app->generateController($name)->log();
                return $this->_redirectMessage('成功', implode("\n", $log), url('generate/controllers'));
            }
            catch (QException $ex)
            {
                $error = $this->_getLastError();
                if ($error)
                {
                    $error = "\n\n{$error}";
                }
                return $this->_redirectMessage('失败', $ex->getMessage(), url('generate/controllers'));
            }
        }

        return $this->_redirect(url('generate/controllers'));
    }

    /**
     * 列出所有模型
     */
    function actionModels()
    {
        $models = new QColl('QReflection_Model');
        foreach ($this->_managed_app->modules() as $module)
        {
           $models->append($module->models());
        }

        $this->_view['models'] = $models;

        try
        {
            $tables = $this->_getDBO()->metaTables();
            if (!empty($tables))
            {
                $tables = array_combine($tables, $tables);
            }
            array_unshift($tables, 0);
            $tables[0] = '- 选择要使用的数据表 -';
        }
        catch (QException $ex)
        {
            $error = $this->_getLastError();
            if ($error)
            {
                $error = "\n\n{$error}";
            }
            return $this->_redirectMessage('失败 - 无法读取数据库或没有数据表', $ex->getMessage(), url('default/index'));
        }

        $this->_view['tables'] = $tables;
    }

    /**
     * 获得指定数据表的字段信息
     */
    function actionGetColumns()
    {
        $table_name = $this->_context->table;
        $this->_view['columns'] = @$this->_getDBO()->metaColumns($table_name);
        $this->_view['table_name'] = $table_name;
    }

    /**
     * 创建一个新模型
     */
    function actionNewModel()
    {
        $name = $this->_context->new_model_name;
        $table_name = $this->_context->table_name;
        if (!empty($name))
        {
            try
            {
                $log = $this->_managed_app->generateModel($name, $table_name, $this->_getDBO())->log();
                return $this->_redirectMessage('成功', implode("\n", $log), url('generate/models'));
            }
            catch (QException $ex)
            {
                $error = $this->_getLastError();
                if ($error)
                {
                    $error = "\n\n{$error}";
                }
                return $this->_redirectMessage('失败', $ex->getMessage(), url('generate/models'));
            }
        }

        return $this->_redirect(url('generate/models'));
    }

    protected function _getDBO()
    {
        $dsn = Q::ini('managed_app_ini/db_dsn_pool/default');

        if (!empty($dsn['_use']))
        {
            $used_dsn = Q::ini("managed_app_ini/db_dsn_pool/{$dsn['_use']}");
            $dsn = array_merge($dsn, $used_dsn);
            unset($dsn['_use']);
            if (!empty($dsn))
            {
                Q::replaceIni("managed_app_ini/db_dsn_pool/default", $dsn);
            }
        }

        $dbtype = $dsn['driver'];
        $objid = "dbo_{$dbtype}_" .  md5(serialize($dsn));
        $class_name = 'QDB_Adapter_' . ucfirst($dbtype);
        return new $class_name($dsn, $objid);
    }
}

