<?php
// $Id: validator_validatefailed_exception.php 2017 2009-01-08 19:09:51Z dualface $

/**
 * 定义 QValidator_ValidateFailedException 异常
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: validator_validatefailed_exception.php 2017 2009-01-08 19:09:51Z dualface $
 * @package exception
 */

/**
 * QValidator_ValidateFailedException 异常封装了验证失败事件
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: validator_validatefailed_exception.php 2017 2009-01-08 19:09:51Z dualface $
 * @package exception
 */
class QValidator_ValidateFailedException extends QException
{
    /**
     * 被验证的数据
     *
     * @var array
     */
    public $validate_data;

    /**
     * 验证失败的结果
     *
     * @var array
     */
    public $validate_errors;

    /**
     * 构造函数
     *
     * @param array $errors
     * @param array $data
     */
    function __construct(array $errors, array $data = array())
    {
        $this->validate_errors = $errors;
        $this->validate_data = $data;
        parent::__construct($this->formatToString());
    }

    /**
     * 格式化错误信息
     *
     * @param string $key
     *
     * @return string
     */
    function formatToString($key = null)
    {
        if (!is_null($key) && (isset($this->validate_errors[$key])))
        {
            $error = $this->validate_errors[$key];
        }
        else
        {
            $error = $this->validate_errors;
        }

        $arr = array();
        foreach ($error as $messages)
        {
            if (is_array($messages))
            {
                $arr[] = implode(', ', $messages);
            }
            else
            {
                $arr[] = $messages;
            }
        }
        return implode('; ', $arr);
    }

    /**
     * 将异常转换为字符串
     *
     * @return string
     */
    function __toString()
    {
        return $this->formatToString();
    }
}

