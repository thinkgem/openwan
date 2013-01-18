<?php
// $Id: pagination.php 883 2010-03-23 01:44:52Z thinkgem $
/**
 * 分页控件
 */
class Control_Pagination extends QUI_Control_Abstract
{
    function render()
    {
        $pagination = $this->pagination;
        $udi        = $this->get('udi', $this->_context->requestUDI());
        $length     = $this->get('length', 6);
        $slider     = $this->get('slider', 1);
        $prev_label = $this->get('prev_label', '上页');
        $next_label = $this->get('prev_label', '下页');
        $url_args   = $this->get('url_args');
        $show_count = $this->get('show_count');

        $out = "<div class=\"pagination\">\n";
        
        $out .= '<ul id="' . h($this->id()) . "\">\n";

        $url_args = (array)$url_args;
        if ($pagination['current'] == $pagination['first'])
        {
            $out .= "<li class=\"disabled\">&#171; {$prev_label}</li>\n";
        }
        else
        {
            $url_args['page'] = $pagination['prev'];
            $url = url($udi, $url_args);
            $out .= "<li><a href=\"{$url}\">&#171; {$prev_label}</a></li>\n";
        }

        $base = $pagination['first'];
        $current = $pagination['current'];

        $mid = intval($length / 2);
        if ($current < $pagination['first'])
        {
            $current = $pagination['first'];
        }
        if ($current > $pagination['last'])
        {
            $current = $pagination['last'];
        }

        $begin = $current - $mid;
        if ($begin < $pagination['first'])
        {
            $begin = $pagination['first'];
        }
        $end = $begin + $length - 1;
        if ($end >= $pagination['last'])
        {
            $end = $pagination['last'];
            $begin = $end - $length + 1;
            if ($begin < $pagination['first'])
            {
                $begin = $pagination['first'];
            }
        }

        if ($begin > $pagination['first'])
        {
            for ($i = $pagination['first']; $i < $pagination['first'] + $slider && $i < $begin; $i ++)
            {
                $url_args['page'] = $i;
                $in = $i + 1 - $base;
                $url = url($udi, $url_args);
                $out .= "<li><a href=\"{$url}\">{$in}</a></li>\n";
            }

            if ($i < $begin)
            {
                $out .= "<li class=\"none\">...</li>\n";
            }
        }

        for ($i = $begin; $i <= $end; $i ++)
        {
            $url_args['page'] = $i;
            $in = $i + 1 - $base;
            if ($i == $pagination['current'])
            {
                $out .= "<li class=\"current\">{$in}</li>\n";
            }
            else
            {
                $url = url($udi, $url_args);
                $out .= "<li><a href=\"{$url}\">{$in}</a></li>\n";
            }
        }

        if ($pagination['last'] - $end > $slider)
        {
            $out .= "<li class=\"none\">...</li>\n";
            $end = $pagination['last'] - $slider;
        }

        for ($i = $end + 1; $i <= $pagination['last']; $i ++)
        {
            $url_args['page'] = $i;
            $in = $i + 1 - $base;
            $url = url($udi, $url_args);
            $out .= "<li><a href=\"{$url}\">{$in}</a></li>\n";
        }

        if ($pagination['current'] == $pagination['last'])
        {
            $out .= "<li class=\"disabled\">{$next_label} &#187;</li>\n";
        }
        else
        {
            $url_args['page'] = $pagination['next'];
            $url = url($udi, $url_args);
            $out .= "<li><a href=\"{$url}\">{$next_label} &#187;</a></li>\n";
        }

        $out .= "</ul>\n";

        if ($show_count)
        {
            $out .= "<p>&nbsp;共 {$pagination['record_count']} 个条目</p>\n";
        }

        $out .= "</div>\n";

        return $out;
    }
}
