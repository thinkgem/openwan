<?php
// $Id: callbacks.php 1937 2009-01-05 19:09:40Z dualface $

/**
 * 定义 QDB_ActiveRecord_Callbacks 接口
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: callbacks.php 1937 2009-01-05 19:09:40Z dualface $
 * @package orm
 */

/**
 * QDB_ActiveRecord_Callbacks 定义了 ActiveRecord 对象及行为插件可用的回调类型
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: callbacks.php 1937 2009-01-05 19:09:40Z dualface $
 * @package orm
 */
interface QDB_ActiveRecord_Callbacks
{
    //! 查询前
    const BEFORE_FIND           = 'before_find';

    //! 查询后
    const AFTER_FIND            = 'after_find';

    //! 初始化后
    const AFTER_INITIALIZE      = 'after_initialize';

    //! 保存之前
    const BEFORE_SAVE           = 'before_save';

    //! 保存之后
    const AFTER_SAVE            = 'after_save';

    //! 创建之前
    const BEFORE_CREATE         = 'before_create';

    //! 创建之后
    const AFTER_CREATE          = 'after_create';

    //! 更新之前
    const BEFORE_UPDATE         = 'before_update';

    //! 更新之后
    const AFTER_UPDATE          = 'after_update';

    //! 验证之前
    const BEFORE_VALIDATE       = 'before_validate';

    //! 验证之后
    const AFTER_VALIDATE        = 'after_validate';

    //! 创建记录验证之前
    const BEFORE_VALIDATE_ON_CREATE = 'before_validate_on_create';

    //! 创建记录验证之后
    const AFTER_VALIDATE_ON_CREATE  = 'after_validate_on_create';

    //! 更新记录验证之前
    const BEFORE_VALIDATE_ON_UPDATE = 'before_validate_on_update';

    //! 更新记录验证之后
    const AFTER_VALIDATE_ON_UPDATE  = 'after_validate_on_update';

    //! 销毁之前
    const BEFORE_DESTROY        = 'before_destroy';

    //! 销毁之后
    const AFTER_DESTROY         = 'after_destroy';

    //! 创建失败的异常
    const CREATE_EXCEPTION      = 'create_exception';

    //! 更新失败的异常
    const UPDATE_EXCEPTION      = 'update_exception';

    //! 替换失败的异常
    const REPLACE_EXCEPTION     = 'replace_exception';

    //! 销毁失败的异常
    const DESTROY_EXCEPTION     = 'destroy_exception';
}

