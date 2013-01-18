<?php
// $Id: dom_element.php 2639 2009-07-31 07:38:22Z yangyi $

/**
 * 定义 QDom_Element 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: dom_element.php 2639 2009-07-31 07:38:22Z yangyi $
 * @package helper
 */

/**
 * QDom_Element 类对PHP5自带的DOMElement进行了自己的扩展
 *
 * @author yangyi.cn.gz@gmail.com
 * @version $Id: dom_element.php 2639 2009-07-31 07:38:22Z yangyi $
 * @package helper
 */
class QDom_Element extends DOMElement implements ArrayAccess
{
    /**
     * 魔法方法，获取attribute
     *
     * @param   string  $key
     * @return  mixed
     */
    public function __get($key) {
        return $this->getAttribute($key);
    }

    /**
     * 魔法方法，设置attribute
     *
     * @param   string  $key
     * @param   string  $val
     */
    public function __set($key, $val) {
        $this->setAttribute($key, $val);
    }

    /**
     * 魔法方法，检查attribute是否存在
     *
     * @param   string  $key
     */
    public function __isset($key) {
        return $this->hasAttribute($key);
    }

    /**
     * 魔法方法，删除attribute
     *
     * @param   string  $key
     */
    public function __unset($key) {
        $this->removeAttribute($key);
    }

    /**
     * 实现ArrayAccess接口
     * 
     * @param mixed $key 
     * @access public
     * @return void
     */
    public function offsetExists($key) {
        return $this->hasAttribute($key);
    }

    /**
     * 实现ArrayAccess接口
     * 
     * @param mixed $key 
     * @param mixed $value 
     * @access public
     * @return void
     */
    public function offsetSet($key, $value) {
        $this->setAttribute($key, $value);
    }

    /**
     * 实现ArrayAccess接口
     * 
     * @param mixed $key 
     * @access public
     * @return void
     */
    public function offsetGet($key) {
        if ($this->hasAttribute($key)) return $this->getAttribute($key);

        throw new QDom_Exception('Attribute ['. $key .'] not exists!');
    }

    /**
     * 实现ArrayAccess接口
     * 
     * @param mixed $key 
     * @access public
     * @return void
     */
    public function offsetUnset($key) {
        $this->removeAttribute($key);
    }

    /**
     * 返回当前element的xml字符串，相当于javascript dom里的outerHTML()
     *
     * @return  string
     */
    public function __toString() {
        if ($this->ownerDocument) {
            return $this->ownerDocument->saveXML($this);
        } else {
            $doc = new DOMDocument();
            $el = $this->cloneNode(true);
            $doc->appendChild($el);
            return $doc->saveXML($el);
        }
    }

    /**
     * 批量设置attribute
     *
     * @param   array   $attrs
     */
    public function setAttributes(array $attrs) {
        foreach ($attrs as $key => $val) {
            $this->$key = $val;
        }
    }

    /**
     * 批量获取attribute，如果指定了key则只返回指定的
     *
     * @param   string  $key
     * @return  array
     */
    public function getAttributes(/* string */$key = null/* [, $key2[, $key3[, ...]]] */) {
        $result = array();
        if ($keys = func_get_args()) {
            foreach ($keys as $key) {
                $result[] = $this->$key;
            }
        } else {
            foreach ($this->attributes as $attr) {
                $result[$attr->nodeName] = $attr->nodeValue;
            }
        }
        return $result;
    }

    /**
     * xpath查询
     *
     * @param   string  $query
     * @param   boolean $return_first
     */
    public function select(/* string */$query, /* boolean */$return_first = false) {
        if ($this->ownerDocument) {
            $result = $this->ownerDocument->xpath()->evaluate($query, $this);
            return ($return_first AND $result instanceof DOMNodelist) ? $result->item(0) : $result;
        } else {
            throw new QDom_Exception('Element must have ownerDocument while select()');
        }
    }

    /**
     * 插入一个新的子节点到指定的子节点之后，返回插入的新子节点
     *
     * @param   DOMNode $newnode
     * @param   DOMNode $refnode
     *
     * @return  DOMNode
     */
    public function insertAfter(DOMNode $newnode, DOMNode $refnode) {
        if ($refnode = $refnode->nextSibling) {
            $this->insertBefore($newnode, $refnode);
        } else {
            $this->appendChild($newnode);
        }
        return $newnode;
    }

    /**
     * 把节点插入到指定节点的指定位置
     *
     * @param   DOMNode $refnode
     * @param   string  $where
     * @return  DOMNode
     */
    public function inject(DOMNode $refnode, $where = 'bottom') {
        $where = strtolower($where);

        if ('before' == $where) {
            $refnode->parentNode->insertBefore($this, $refnode);
        } elseif ('after' == $where) {
            $refnode->parentNode->insertAfter($this, $refnode);
        } else {
            if ('top' == $where AND $first = $refnode->firstChild) {
                $refnode->insertBefore($this, $first);
            } else {
                $refnode->appendChild($this);
            }
        }

        return $this;
    }

    /**
     * 是否是第一个子节点
     *
     * @return  boolean
     */
    public function isFirst() {
        return $this->previousSibling ? false : true;
    }

    /**
     * 是否最后一个子节点
     *
     * @return  boolean
     */
    public function isLast() {
        return $this->nextSibling ? false : true;
    }

    /**
     * 清除所有的子节点
     *
     * @return DOMNode
     */
    public function clean() {
        foreach ($this->childNodes as $child) {
            $this->removeChild($child);
        }
        return $this;
    }

    /**
     * 删除自己
     */
    public function erase() {
        $this->parentNode->removeChild($this);
    }

    /**
     * 把xml字符串插入到当前节点尾部
     *
     * @param   string  $xml
     * @return  DOMElement
     */
    public function appendXML($xml) {
        if ($this->ownerDocument) {
            $fragment = $this->ownerDocument->createDocumentFragment();
            $fragment->appendXML($xml);
            return $this->appendChild($fragment);
        } else {
            throw new QDom_Exception('Element must have ownerDocument while appendXML()');
        }
    }

    /**
     * 用xml字符串替换当前节点的所有子节点
     *
     * @param   string  $xml
     * @return  DOMElement
     */
    public function replaceXML($xml) {
        $this->clean();
        return $this->appendXML($xml);
    }

    /**
     * 根据给定的nodeName和attribute数组，对节点进行比较
     * 
     * @param DOMElement $node
     * @param string $nodeName
     * @param array $attributes
     * @access protected
     * @return boolean
     */
    protected function _match($node, $nodeName, array $attributes = array()) {
        if ($node->nodeName != $nodeName) return false;

        while (list($key, $value) = each($attributes)) {
            if ($node->getAttribute($key) != $value) return false;
        }

        return true;
    }

    /**
     * 在同级之前的节点中查找
     * 可以指定nodeName和attributes匹配条件
     * 
     * @param string $nodeName 
     * @param array $attributes 
     * @param boolean $returnFirst
     * @access protected
     * @return mixed
     */
    public function prev($nodeName = null, array $attributes = array()) {
        $currentEl = $this;
        while ($prevEl = $currentEl->previousSibling) {
            if (empty($nodeNode)) return $prevEl;
            if ($this->_match($prevEl, $nodeName, $attributes)) return $prevEl;
            $currentEl = $prevEl;
        }
        return null;
    }

    /**
     * 在同级之后的节点中查找
     * 可以指定nodeName和attributes匹配条件
     * 
     * @param string $nodeName 
     * @param array $attributes 
     * @access public
     * @return mixed
     */
    public function next($nodeName = null, array $attributes = array()) {
        $currentEl = $this;
        while ($nextEl = $currentEl->nextSibling) {
            if (empty($nodeName)) return $nextEl;
            if ($this->_match($nextEl, $nodeName, $attributes)) return $nextEl;
            $currentEl = $nextEl;
        }
        return null;
    }
}
