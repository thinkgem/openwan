<?php

abstract class API_Doc_Abstract implements ArrayAccess
{
    function offsetGet($key)
    {
        return $this->{$key};
    }

    function offsetSet($key, $value)
    {
        $this->{$key} = $value;
    }

    function offsetUnset($key)
    {
        unset($this->{$key});
    }

    function offsetExists($key)
    {
        return true;
    }

    protected function _docComment($comment)
    {
        $comment = trim(trim($comment), '/');
        $comment = trim(preg_replace('/^\s*\**( |\t)?/m', '', $comment));

        // 处理 @code 和 @endcode
        $comment = str_replace(array('@code', '@endcode'), array('/---code', '\---'), $comment);

        $matches = null;
		if(preg_match('/^\s*@\w+/m', $comment, $matches, PREG_OFFSET_CAPTURE))
		{
			$meta = substr($comment, $matches[0][1]);
			$comment = trim(substr($comment, 0, $matches[0][1]));
		}
        else
        {
            $meta = '';
        }
        return array($comment, $meta);
    }

    protected function _processDescription($comment)
    {
        $pos = strpos($comment, "\n");

        if ($pos === false)
        {
            // 如果注释中没有换行符，则全部当作 summary
            $this->summary = $comment;
        }
        else
        {
            $this->summary = substr($comment, 0, $pos);
        }
        $this->description = $comment;
    }

    protected function _processMeta($meta, $allows_tags = null)
    {
        if ($allows_tags)
        {
            $allows_tags = Q::normalize($allows_tags);
        }

        $tags = preg_split('/^\s*@/m', $meta, -1, PREG_SPLIT_NO_EMPTY);
        $arr = array();
		foreach($tags as $tag)
        {
			$segs = preg_split('/\s+/', trim($tag), 2);
			$tag_name = $segs[0];
            $param = isset($segs[1]) ? trim($segs[1]) : '';
            if ($allows_tags && !in_array($tag_name, $allows_tags)) continue;

            $tag_method = '_processTag' . ucfirst($tag_name);
            if (method_exists($this, $tag_method))
            {
                $this->{$tag_method}($param);
            }
            elseif (property_exists($this, $tag_name))
            {
                $this->{$tag_name} = $param;
            }
            else
            {
                throw new API_Doc_NotSupportedTagException($this, $tag_name);
            }
		}
    }

    protected function _processTagParam($param)
    {
        $segs = preg_split('/\s+/', $param, 3);
        $arr = array();
        $type = $segs[0];
        if (!isset($segs[1]))
        {
            // throw new API_Doc_UndefinedParameterException($this, 'UNKNOWN');
        }

        $name = trim($segs[1], '$');
        $comment = isset($segs[2]) ? $segs[2] : '';

        if (!isset($this->parameters[$name]))
        {
            // throw new API_Doc_UndefinedParameterException($this, $name);
        }
        else
        {
            $this->parameters[$name]->type_hint = $type;
            $this->parameters[$name]->doc_comment = $comment;
        }
    }

    protected function _processTagReturn($param)
    {
        $segs = preg_split('/\s+/', $param, 2);
        $this->return_type = $segs[0];
        $this->return_comment = isset($segs[1]) ? $segs[1] : '';
    }

    protected function _processTagAccess($param)
    {
        switch ($param)
        {
        case 'private':
            $this->is_public    = false;
            $this->is_protected = false;
            $this->is_private   = true;
            break;
        case 'protected':
            $this->is_public    = false;
            $this->is_protected = true;
            $this->is_private   = false;
            break;
        default:
            $this->is_public    = true;
            $this->is_protected = false;
            $this->is_private   = false;
        }
    }

    protected function _processTagThrow($param)
    {
        $this->throws[] = $param;
    }

    protected function _processTagPackage($param)
    {
        $this->package = API_Doc_Package::instance($param);
    }

    protected function _processTagVar($param)
    {
        $this->type_hint = $param;
    }
}

