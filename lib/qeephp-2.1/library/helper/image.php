<?php
// $Id: image.php 1937 2009-01-05 19:09:40Z dualface $

/**
 * 定义 Helper_Image 类和 Helper_ImageGD 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: image.php 1937 2009-01-05 19:09:40Z dualface $
 * @package helper
 */

/**
 * Helper_Image 类封装了针对图像的操作
 *
 * 开发者不能直接构造该类的实例，而是应该用 Helper_Image::createFromFile()
 * 静态方法创建一个 Image 类的实例。
 *
 * 操作大图片时，请确保 php 能够分配足够的内存。
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: image.php 1937 2009-01-05 19:09:40Z dualface $
 * @package helper
 */
abstract class Helper_Image
{
    /**
     * 从指定文件创建 Helper_ImageGD 对象
     *
     * 用法：
     * @code php
     * $image = Helper_Image::createFromFile('1.jpg');
     * $image->resize($width, $height);
     * $image->saveAsJpeg('2.jpg');
     * @endcode
     *
     * 对于上传的文件，由于其临时文件名中并没有包含扩展名。
     * 因此需要采用下面的方法创建 Image 对象：
     *
     * @code php
     * $ext = pathinfo($_FILES['postfile']['name'], PATHINFO_EXTENSION);
     * $image = Image::createFromFile($_FILES['postfile']['tmp_name'], $ext);
     * @endcode
     *
     * @param string $filename 图像文件的完整路径
     * @param string $fileext 指定扩展名
     *
     * @return Helper_ImageGD 从文件创建的 Helper_ImageGD 对象
     * @throw Q_NotImplementedException
     */
    static function createFromFile($filename, $fileext)
    {
        $fileext = trim(strtolower($fileext), '.');
        $ext2functions = array(
            'jpg'  => 'imagecreatefromjpeg',
            'jpeg' => 'imagecreatefromjpeg',
            'png'  => 'imagecreatefrompng',
            'gif'  => 'imagecreatefromgif'
        );

        if (!isset($ext2functions[$fileext]))
        {
        	throw new Q_NotImplementedException(__('imagecreateform' . $fileext));
        }

        $handle = call_user_func($ext2functions[$fileext], $filename);
        return new Helper_ImageGD($handle);
    }

	/**
	 * 将 16 进制颜色值转换为 rgb 值
     *
     * 用法：
     * @code php
     * $color = '#369';
     * list($r, $g, $b) = Helper_Image::hex2rgb($color);
     * echo "red: {$r}, green: {$g}, blue: {$b}";
     * @endcode
     *
     * @param string $color 颜色值
     * @param string $default 使用无效颜色值时返回的默认颜色
	 *
	 * @return array 由 RGB 三色组成的数组
	 */
	static function hex2rgb($color, $default = 'ffffff')
	{
        $hex = trim($color, '#&Hh');
        $len = strlen($hex);
        if ($len == 3)
        {
            $hex = "{$hex[0]}{$hex[0]}{$hex[1]}{$hex[1]}{$hex[2]}{$hex[2]}";
        }
        elseif ($len < 6)
        {
            $hex = $default;
        }
        $dec = hexdec($hex);
        return array(($dec >> 16) & 0xff, ($dec >> 8) & 0xff, $dec & 0xff);
	}
}

/**
 * Helper_ImageGD 类封装了一个 gd 句柄，用于对图像进行操作
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: image.php 1937 2009-01-05 19:09:40Z dualface $
 * @package helper
 */
class Helper_ImageGD
{
    /**
     * GD 资源句柄
     *
     * @var resource
     */
    protected $_handle = null;

    /**
     * 构造函数
     *
     * @param resource $handle GD 资源句柄
     */
    function __construct($handle)
    {
        $this->_handle = $handle;
    }

    /**
     * 析构函数
     */
    function __destruct()
    {
    	$this->destroy();
    }

    /**
     * 快速缩放图像到指定大小（质量较差）
     *
     * @param int $width 新的宽度
     * @param int $height 新的高度
     *
     * @return Helper_ImageGD 返回 Helper_ImageGD 对象本身，实现连贯接口
     */
    function resize($width, $height)
    {
        if (is_null($this->_handle)) return $this;

        $dest = imagecreatetruecolor($width, $height);
        imagecopyresized($dest, $this->_handle, 0, 0, 0, 0,
            $width, $height,
            imagesx($this->_handle), imagesy($this->_handle));
        imagedestroy($this->_handle);
        $this->_handle = $dest;
        return $this;
    }

    /**
     * 缩放图像到指定大小（质量较好，速度比 resize() 慢）
     *
     * @param int $width 新的宽度
     * @param int $height 新的高度
     *
     * @return Helper_ImageGD 返回 Helper_ImageGD 对象本身，实现连贯接口
     */
    function resampled($width, $height)
    {
        if (is_null($this->_handle)) return $this;
        $dest = imagecreatetruecolor($width, $height);
        imagecopyresampled($dest, $this->_handle, 0, 0, 0, 0,
            $width, $height,
            imagesx($this->_handle), imagesy($this->_handle));
        imagedestroy($this->_handle);
        $this->_handle = $dest;
        return $this;
    }

    /**
     * 调整图像大小，但不进行缩放操作
     *
     * 用法：
     * @code php
     * $image->resizeCanvas($width, $height, 'top-left');
     * @endcode
     *
     * $pos 参数指定了调整图像大小时，图像内容按照什么位置对齐。
     * $pos 参数的可用值有：
     *
     * -   left: 左对齐
     * -   right: 右对齐
     * -   center: 中心对齐
     * -   top: 顶部对齐
     * -   bottom: 底部对齐
     * -   top-left, left-top: 左上角对齐
     * -   top-right, right-top: 右上角对齐
     * -   bottom-left, left-bottom: 左下角对齐
     * -   bottom-right, right-bottom: 右下角对齐
     *
     * 如果指定了无效的 $pos 参数，则等同于指定 center。
     *
     * @param int $width 新的高度
     * @param int $height 新的宽度
     * @param string $pos 调整时图像位置的变化
     * @param string $bgcolor 空白部分的默认颜色
     *
     * @return Helper_ImageGD 返回 Helper_ImageGD 对象本身，实现连贯接口
     */
    function resizeCanvas($width, $height, $pos = 'center', $bgcolor = '0xffffff')
    {
        if (is_null($this->_handle)) return $this;

        $dest = imagecreatetruecolor($width, $height);
        $sx = imagesx($this->_handle);
        $sy = imagesy($this->_handle);

        // 根据 pos 属性来决定如何定位原始图片
        switch (strtolower($pos))
        {
        case 'left':
            $ox = 0;
            $oy = ($height - $sy) / 2;
            break;
        case 'right':
            $ox = $width - $sx;
            $oy = ($height - $sy) / 2;
            break;
        case 'top':
            $ox = ($width - $sx) / 2;
            $oy = 0;
            break;
        case 'bottom':
            $ox = ($width - $sx) / 2;
            $oy = $height - $sy;
            break;
        case 'top-left':
        case 'left-top':
            $ox = $oy = 0;
            break;
        case 'top-right':
        case 'right-top':
            $ox = $width - $sx;
            $oy = 0;
            break;
        case 'bottom-left':
        case 'left-bottom':
            $ox = 0;
            $oy = $height - $sy;
            break;
        case 'bottom-right':
        case 'right-bottom':
            $ox = $width - $sx;
            $oy = $height - $sy;
            break;
        default:
            $ox = ($width - $sx) / 2;
            $oy = ($height - $sy) / 2;
        }

        list ($r, $g, $b) = Helper_Image::hex2rgb($bgcolor, '0xffffff');
        $bgcolor = imagecolorallocate($dest, $r, $g, $b);
        imagefilledrectangle($dest, 0, 0, $width, $height, $bgcolor);
        imagecolordeallocate($dest, $bgcolor);

        imagecopy($dest, $this->_handle, $ox, $oy, 0, 0, $sx, $sy);
        imagedestroy($this->_handle);
        $this->_handle = $dest;

        return $this;
    }

    /**
     * 在保持图像长宽比的情况下将图像裁减到指定大小
     *
     * crop() 在缩放图像时，可以保持图像的长宽比，从而保证图像不会拉高或压扁。
     *
     * crop() 默认情况下会按照 $width 和 $height 参数计算出最大缩放比例，
     * 保持裁减后的图像能够最大程度的充满图片。
     *
     * 例如源图的大小是 800 x 600，而指定的 $width 和 $height 是 200 和 100。
     * 那么源图会被首先缩小为 200 x 150 尺寸，然后裁减掉多余的 50 像素高度。
     *
     * 用法：
     * @code php
     * $image->crop($width, $height);
     * @endcode
     *
     * 如果希望最终生成图片始终包含完整图像内容，那么应该指定 $options 参数。
     * 该参数可用值有：
     *
     * -   fullimage: 是否保持完整图像
     * -   pos: 缩放时的对齐方式
     * -   bgcolor: 缩放时多余部分的背景色
     * -   enlarge: 是否允许放大
     * -   reduce: 是否允许缩小
     *
     * 其中 $options['pos'] 参数的可用值有：
     *
     * -   left: 左对齐
     * -   right: 右对齐
     * -   center: 中心对齐
     * -   top: 顶部对齐
     * -   bottom: 底部对齐
     * -   top-left, left-top: 左上角对齐
     * -   top-right, right-top: 右上角对齐
     * -   bottom-left, left-bottom: 左下角对齐
     * -   bottom-right, right-bottom: 右下角对齐
     *
     * 如果指定了无效的 $pos 参数，则等同于指定 center。
     *
     * $options 中的每一个选项都可以单独指定，例如在允许裁减的情况下将图像放到新图片的右下角。
     *
     * @code php
     * $image->crop($width, $height, array('pos' => 'right-bottom'));
     * @endcode
     *
     * @param int $width 新的宽度
     * @param int $height 新的高度
     * @param array $options 裁减选项
     *
     * @return Helper_ImageGD 返回 Helper_ImageGD 对象本身，实现连贯接口
     */
    function crop($width, $height, $options = array())
    {
        if (is_null($this->_handle)) return $this;

        $default_options = array(
            'fullimage' => false,
            'pos'       => 'center',
            'bgcolor'   => '0xfff',
            'enlarge'   => false,
            'reduce'    => true,
        );
        $options = array_merge($default_options, $options);

        // 创建目标图像
        $dest = imagecreatetruecolor($width, $height);
        // 填充背景色
        list ($r, $g, $b) = Helper_Image::hex2rgb($options['bgcolor'], '0xffffff');
        $bgcolor = imagecolorallocate($dest, $r, $g, $b);
        imagefilledrectangle($dest, 0, 0, $width, $height, $bgcolor);
        imagecolordeallocate($dest, $bgcolor);

        // 根据源图计算长宽比
        $full_w = imagesx($this->_handle);
        $full_h = imagesy($this->_handle);
        $ratio_w = doubleval($width) / doubleval($full_w);
        $ratio_h = doubleval($height) / doubleval($full_h);

        if ($options['fullimage'])
        {
            // 如果要保持完整图像，则选择最小的比率
            $ratio = $ratio_w < $ratio_h ? $ratio_w : $ratio_h;
        }
        else
        {
            // 否则选择最大的比率
            $ratio = $ratio_w > $ratio_h ? $ratio_w : $ratio_h;
        }

        if (!$options['enlarge'] && $ratio > 1) $ratio = 1;
        if (!$options['reduce'] && $ratio < 1) $ratio = 1;

        // 计算目标区域的宽高、位置
        $dst_w = $full_w * $ratio;
        $dst_h = $full_h * $ratio;

        // 根据 pos 属性来决定如何定位
        switch (strtolower($options['pos']))
        {
        case 'left':
            $dst_x = 0;
            $dst_y = ($height - $dst_h) / 2;
            break;
        case 'right':
            $dst_x = $width - $dst_w;
            $dst_y = ($height - $dst_h) / 2;
            break;
        case 'top':
            $dst_x = ($width - $dst_w) / 2;
            $dst_y = 0;
            break;
        case 'bottom':
            $dst_x = ($width - $dst_w) / 2;
            $dst_y = $height - $dst_h;
            break;
        case 'top-left':
        case 'left-top':
            $dst_x = $dst_y = 0;
            break;
        case 'top-right':
        case 'right-top':
            $dst_x = $width - $dst_w;
            $dst_y = 0;
            break;
        case 'bottom-left':
        case 'left-bottom':
            $dst_x = 0;
            $dst_y = $height - $dst_h;
            break;
        case 'bottom-right':
        case 'right-bottom':
            $dst_x = $width - $dst_w;
            $dst_y = $height - $dst_h;
            break;
        case 'center':
        default:
            $dst_x = ($width - $dst_w) / 2;
            $dst_y = ($height - $dst_h) / 2;
        }

        imagecopyresampled($dest,  $this->_handle, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $full_w, $full_h);
        imagedestroy($this->_handle);
        $this->_handle = $dest;

        return $this;
    }

    /**
     * 保存为 JPEG 文件
     *
     * @param string $filename 保存文件名
     * @param int $quality 品质参数，默认为 80
     *
     * @return Helper_ImageGD 返回 Helper_ImageGD 对象本身，实现连贯接口
     */
    function saveAsJpeg($filename, $quality = 80)
    {
        imagejpeg($this->_handle, $filename, $quality);
    }

    /**
     * 保存为 PNG 文件
     *
     * @param string $filename 保存文件名
     *
     * @return Helper_ImageGD 返回 Helper_ImageGD 对象本身，实现连贯接口
     */
    function saveAsPng($filename)
    {
        imagepng($this->_handle, $filename);
    }

    /**
     * 保存为 GIF 文件
     *
     * @param string $filename 保存文件名
     *
     * @return Helper_ImageGD 返回 Helper_ImageGD 对象本身，实现连贯接口
     */
    function saveAsGif($filename)
    {
        imagegif($this->_handle, $filename);
    }

    /**
     * 销毁内存中的图像
     *
     * @return Helper_ImageGD 返回 Helper_ImageGD 对象本身，实现连贯接口
     */
    function destroy()
    {
    	if (!$this->_handle)
    	{
            @imagedestroy($this->_handle);
    	}
        $this->_handle = null;
        return $this;
    }
}

