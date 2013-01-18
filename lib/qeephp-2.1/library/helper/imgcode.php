<?php
// $Id: imgcode.php 2651 2009-08-30 17:14:42Z firzen $

/**
 * 定义 Helper_ImgCode 类、Helper_ImgCodeSimple 类和 Helper_ImgCodeTTF 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: imgcode.php 2651 2009-08-30 17:14:42Z firzen $
 * @package helper
 */

/**
 * Helper_ImgCode 类提供验证码生成和检验的接口
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: imgcode.php 2651 2009-08-30 17:14:42Z firzen $
 * @package helper
 */
class Helper_ImgCode
{
    /**
     * 验证码的配置
     *
     * @var array
     */
    static private $_config = array(
        // 用什么键名在 session 中保存验证码
        'imgcode_session_key' => '_IMGCODE',

        // 从 session 中读取的验证码
        'imgcode_session_value' => null,

        // 用什么键名在 session 中保存验证码过期时间
        'expired_session_key' => '_IMGCODE_EXPIRED',

        // 验证码过期时间
        'expired_session_value' => null,
    );

	/**
	 * 利用 GD 库产生验证码图像，并封装为 QView_Output 对象
     *
     * $style 参数值不同时，$options 参数包含的选项也不同。
     *
     * 用法：
     * @code php
     * // 控制器文件
     * class Controller_Default
     * {
     *     function actionImgcode()
     *     {
     *         // 在控制器中用下列代码返回一个图像验证码
     *         return Helper_ImgCode::create(6, 900, 'simple');
     *     }
     * }
     *
     * // 模板文件中使用下列代码引用一个图像验证码
     * <img src="<?php echo url('default/imgcode'); ?>" border="0" />
     * @endcode
     *
     * @param int $length 验证码的长度
     * @param int $lefttime 验证码的有效期
     * @param string $style 验证码的样式
     * @param array $options 具体验证码样式的选项
     *
     * @return QView_Output 包含验证码图像的输出对象
	 */
    static function create($length = 4, $lefttime = 900,
                           $style = 'simple', array $options = null)
    {
        $class_name = 'Helper_ImgCode' . ucfirst(strtolower(preg_replace('/[^a-z0-9_]+/i', '', $style)));
        $options = (array)$options;
        $options['code_length'] = $length;
        $imgcode_obj = new $class_name($options);

        $code = $imgcode_obj->generateCode();
        self::_writeImgcodeToSession($code, $lefttime);
        return $imgcode_obj->generateImage($code);
    }

    /**
     * 比较输入的验证码是否和 session 中保存的验证码一致（不区分大小写）
     *
     * 用法：
     * @code php
     * // 控制器文件
     * class Controller_Default
     * {
     *     function actionLogin()
     *     {
     *         if (Helper_ImgCode::isValid($this->_context->imgcode))
     *         {
     *             .... 比对通过
     *         }
     *
     *         ....
     *     }
     * }
     * @endcode
     *
     * @param string $code 要比对的验证码
     * @param boolean $clean_session 是否在比对通过后清理 session 中保存的验证码
     * @param boolean $case_sensitive 是否区分大小写
     *
     * @return boolean 比对结果
     */
    static function isValid($code, $clean_session = false, $case_sensitive = false)
    {
        $code_in_session = self::_readImgcodeFromSession();

        if (strlen($code_in_session) == 0 || strlen($code) == 0)
        {
            return false;
        }
        if ($case_sensitive)
        {
            $ret = (string)$code_in_session == (string)$code;
        }
        else
        {
            $ret = strtolower($code_in_session) == strtolower($code);
        }

        if ($ret && $clean_session)
        {
            self::_cleanImgcodeFromSession();
        }

        return $ret;
    }

	/**
     * 清除 session 中的验证码信息
	 */
	static function clean()
    {
        self::_cleanImgcodeFromSession();
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

    /**
     * 写入验证码和验证码过期时间到 session
     *
     * @param string $code 要写入 session 的验证码
     * @param int $lefttime 验证码的有效期
     */
    private static function _writeImgcodeToSession($code, $lefttime)
    {
        if (isset($_SESSION))
        {
            $_SESSION[self::$_config['imgcode_session_key']] = $code;
            $_SESSION[self::$_config['expired_session_key']] = CURRENT_TIMESTAMP + intval($lefttime);
        }
    }

    /**
     * 从 session 取得验证码和验证码过期时间
     */
    private static function _readImgcodeFromSession()
    {
        if (!isset($_SESSION))
        {
            return false;
        }

        $key = self::$_config['imgcode_session_key'];
        $imgcode = isset($_SESSION[$key]) ? $_SESSION[$key] : '';
        $key = self::$_config['expired_session_key'];
        $expired = isset($_SESSION[$key]) ? $_SESSION[$key] : 0;

        if (CURRENT_TIMESTAMP >= $expired) return false;
        return $imgcode;
    }

    /**
     * 从 session 中清除验证码和验证码过期时间
     */
    private static function _cleanImgcodeFromSession()
    {
        if (isset($_SESSION))
        {
            $key = self::$_config['imgcode_session_key'];
            unset($_SESSION[$key]);
            $key = self::$_config['expired_session_key'];
            unset($_SESSION[$key]);
        }
    }
}

/**
 * Helper_ImgCodeSimple 实现了一个简单样式的验证码
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: imgcode.php 2651 2009-08-30 17:14:42Z firzen $
 * @package helper
 */
class Helper_ImgCodeSimple
{
    /**
     * 生成验证码需要的选项
     *
     * @var array
     */
    protected $_options = array(
        // 验证码类型
        'code_type'         => 2,

        // 验证码长度
        'code_length'       => 4,

        // 字符上下浮动像素
        'float_pixel'       => 6,

        // 图像类型
        'image_type'        => IMAGETYPE_JPEG,

        // 字体，如果为 0-5，则使用 GD 库内置的字体
        // 如果使用自定义字体，font 必须指定为字体文件的绝对路径
        'font'              => 5,

        // 字体颜色
        'color'             => '0xffffff',

        // 背景色
        'bgcolor'           => '0x666666',

        // 验证码图片边界颜色
        'bdcolor'           => '0x000000',

        // 内空
        'padding'           => 2,

        // 边界线
        'border'            => 1,
    );

    /**
     * 构造函数
     *
     * @param array $options 生成验证码的选项
     */
    function __construct(array $options)
    {
        $this->_options = array_merge($this->_options, $options);
    }

    /**
     * 生成验证码
     *
     * @return string
     */
    function generateCode()
    {
        $code_type = intval($this->_options['code_type']);
        $code_length = intval($this->_options['code_length']);
        if ($code_length <= 0) { $code_length = 4; }

        switch ($code_type)
        {
        case 0:
            $seed = '0123456789';
            break;
        case 1:
            $seed = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
        default:
            $seed = '346789ABCDEFGHJKLMNPQRTUVWXYabcdefghjklmnpqrtuvwxy';
        }
        $code = '';
        $len = strlen($seed) - 1;
        for ($i = 0; $i < $code_length; $i++)
        {
            $code .= substr($seed, mt_rand(0, $len), 1);
        }

        return $code;
    }

    /**
     * 生成验证码图像，返回 QView_Output 对象
     *
     * $param string $code
     *
     * @return QView_Output
     */
    function generateImage($code)
    {
        // 根据选项确定绘图选项
        $padding = intval($this->_options['padding']);
        if ($padding < 0) { $padding = 0; }
        $color = $this->_options['color'];
        $bgcolor = $this->_options['bgcolor'];
        $border = $this->_options['border'];
        $bdcolor = $this->_options['bdcolor'];
        $float_pixel = intval($this->_options['float_pixel']);

		// 确定要使用的字体
        if (is_int($this->_options['font']))
        {
            $font = intval($this->_options['font']);
            if ($font < 0 || $font > 5)
            {
                $font = 5;
            }
        }
        else
        {
            $font = imageloadfont($this->_options['font']);
        }

		// 确定字体宽度和高度
		$font_width = imagefontwidth($font);
		$font_height = imagefontheight($font);

		// 确定图像的宽度和高度
        $width = $font_width * strlen($code) + $padding * 2 + $border * 2 + 1;
        $height = $font_height + $padding * 2 + $border * 2 + 1 + $float_pixel;

		// 创建图像
		$img = imagecreate($width, $height);

		// 绘制边框
        if ($border)
        {
            list($r, $g, $b) = Helper_ImgCode::hex2rgb($bdcolor);
			$color = imagecolorallocate($img, $r, $g, $b);
			imagefilledrectangle($img, 0, 0, $width, $height, $color);
		}

		// 绘制背景
		list($r, $g, $b) = Helper_ImgCode::hex2rgb($bgcolor);
		$color = imagecolorallocate($img, $r, $g, $b);
		imagefilledrectangle($img, $border, $border, $width - $border - 1, $height - $border - 1, $color);

		// 绘制文字
		list($r, $g, $b) = Helper_ImgCode::hex2rgb($color);
		$color = imagecolorallocate($img, $r, $g, $b);

		for ($i = 0, $max = strlen($code); $i < $max; $i++)
		{
    		imagestring($img, $font, $padding + $border + $font_width * $i, $padding + $border + mt_rand(0, $float_pixel), $code[$i], $color);
		}

        $filename = 'imgcode-' . mt_rand();
		ob_start();

		// 输出图像
        switch (strtolower($this->_options['image_type']))
        {
		case 'png':
			$filename .= '.png';
			$mime = image_type_to_mime_type(IMAGETYPE_PNG);
			imagepng($img);
			break;

		case 'gif':
			$filename .= '.gif';
			$mime = image_type_to_mime_type(IMAGETYPE_GIF);
			imagegif($img);
			break;

		case 'jpg':
		default:
			$filename .= '.jpg';
			$mime = image_type_to_mime_type(IMAGETYPE_JPEG);
			imagejpeg($img);
		}

		imagedestroy($img);
		unset($img);

		$output = new QView_Output($filename, $mime, ob_get_clean());
		$output
            ->contentDisposition('inline')
            ->enableClientCache(false);

		return $output;
	}
}

/**
 * Helper_ImgCodeTTF 使用 ttf 字体生成验证码
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: imgcode.php 2651 2009-08-30 17:14:42Z firzen $
 * @package helper
 */
class Helper_ImgCodeTTF
{
    /**
     * 生成验证码需要的选项
     *
     * @var array
     */
    protected $_options = array
    (
        // 验证码长度
        'code_length'       => 6,

        // 验证码图像宽度
        'width'             => 170,

        // 验证码图像高度
        'height'            => 90,

        // 字符上下浮动像素
        'float_pixel'       => 10,

        // 旋转角度
        'max_angle'         => 35,

        // 字体大小
        'font_size'         => 50,

        // 字体最低间距
        'font_space'        => 0,

        // 图像类型
        'image_type'        => IMAGETYPE_JPEG,

        // 字体，如果为 0-5，则使用 QeePHP 自带的字体
        // 如果使用自定义字体，font 必须指定为字体文件的绝对路径
        'font'              => 0,

        // 字体颜色
        'color'             => 'fff',

        // 背景色
        'bgcolor'           => '333',

        // 验证码图片边界颜色
        'bdcolor'           => 'ccc',

        // 内空
        'padding'           => 5,

        // 边界线
        'border'            => 1,
    );

    /**
     * 内置的字体列表
     *
     * @var array
     */
    static protected $_font_list = array
    (
        'alpha_thin.ttf',
    );

    /**
     * 构造函数
     *
     * @param array $options
     *   生成验证码的选项
     */
    function __construct(array $options)
    {
        $this->_options = array_merge($this->_options, $options);
    }

    /**
     * 生成验证码
     *
     * @return string
     */
    function generateCode()
    {
        $code_length = intval($this->_options['code_length']);
        if ($code_length <= 0) { $code_length = 4; }
        $seed = '346789ABCDEFGHJKLMN346789PQRTUVWXYabcdefghj346789klmnpqrt346789uvwxy';

        $code = '';
        $len = strlen($seed) - 1;
        for ($i = 0; $i < $code_length; $i++)
        {
            $code .= substr($seed, mt_rand(0, $len), 1);
        }

        return $code;
    }

    /**
     * 生成验证码图像，返回 QView_Output 对象
     *
     * $param string $code
     *
     * @return QView_Output
     */
    function generateImage($code)
    {
        // 确定要使用的字体
        if (isset(self::$_font_list[$this->_options['font']]))
        {
            $font = Q_DIR . '/_resources/' . self::$_font_list[$this->_options['font']];
        }
        else
        {
            $font = $this->_options['font'];
        }

        // 确定图像的宽度和高度
        $border = $this->_options['border'];
        $float_pixel = intval($this->_options['float_pixel']);
        $padding = intval($this->_options['padding']);

        $width = intval($this->_options['width']) + $padding * 2 + $border * 2 + 1;
        $width *= 2;
        $height = intval($this->_options['height']) + $padding * 2 + $border * 2 + 1 + $float_pixel;

        // 创建图像
        $img = imagecreatetruecolor($width, $height);

        // 绘制背景
        list($r, $g, $b) = Helper_ImgCode::hex2rgb($this->_options['bgcolor']);
        imagefilledrectangle($img, $border, $border, $width - $border - 1, $height - $border - 1, imagecolorallocate($img, $r, $g, $b));

        // 绘制文字
        $max_angle = intval($this->_options['max_angle']);

        $x = $padding + $padding + $border;

        $bottom = $height - $padding + $border - $float_pixel * 2;
        $font_space = $this->_options['font_space'];
        $color_arr = Helper_ImgCode::hex2rgb($this->_options['color']);

        $arr = array();
        $font_size = $this->_options['font_size'];
        $min_r = $r + 50;
        $min_g = $g + 50;
        $min_b = $b + 50;
        for ($i = 0, $max = strlen($code); $i < $max; $i++)
        {
            $arr[$i]['font_size'] = (mt_rand(50, 150) / 100) * $font_size;
            $arr[$i]['angle'] = rand(0, $max_angle) - $max_angle / 2;
            list($r, $g, $b) = $color_arr;
            $r = ($r + rand(0, 2550)) % (255 - $min_r) + $min_r;
            $g = ($g + rand(0, 2550)) % (255 - $min_g) + $min_g;
            $b = ($b + rand(0, 2550)) % (255 - $min_b) + $min_b;
            $arr[$i]['color'] = imagecolorallocate($img, $r, $g, $b);
        }

        for ($i = 0; $i < $max; $i++)
        {
            $x += $font_space;
            $y = $bottom;

            list(,, $x) = imagettftext($img, $arr[$i]['font_size'], $arr[$i]['angle'], $x, $y, $arr[$i]['color'], $font, $code[$i]);
            imagecolordeallocate($img, $arr[$i]['color']);
        }

        $new_width = intval($this->_options['width']) + $padding * 2 + $border * 2 + 1;
        $img_output = imagecreatetruecolor($new_width, $height);
        imagecopyresampled($img_output, $img, 0, 0, 0, 0, $new_width, $height, $x + $padding + $border, $height);
        imagedestroy($img);

        // 绘制边框
        if ($border)
        {
            list($r, $g, $b) = Helper_ImgCode::hex2rgb($this->_options['bdcolor']);
            imagerectangle($img_output, 0, 0, $width, $height, imagecolorallocate($img_output, $r, $g, $b));
        }

        $filename = 'imgcode-' . mt_rand();
        ob_start();

        // 输出图像
        switch (strtolower($this->_options['image_type']))
        {
        case 'png':
            $filename .= '.png';
            $mime = image_type_to_mime_type(IMAGETYPE_PNG);
            imagepng($img_output);
            break;

        case 'gif':
            $filename .= '.gif';
            $mime = image_type_to_mime_type(IMAGETYPE_GIF);
            imagegif($img_output);
            break;

        case 'jpg':
        default:
            $filename .= '.jpg';
            $mime = image_type_to_mime_type(IMAGETYPE_JPEG);
            imagejpeg($img_output);
        }

        imagedestroy($img_output);

        $output = new QView_Output($filename, $mime, ob_get_clean());
        $output->enableClientCache(false);

        return $output;
    }
}

