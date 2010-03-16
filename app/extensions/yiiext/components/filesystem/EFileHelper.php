<?php
/**
 * EFileHelper class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */
class EFileHelper extends CFileHelper {
	/**
	 * @static
	 * @access public
	 * @param string $path
	 * @param boolean $addSlash
	 * @param string $slash
	 * @return string|false
	 */
	public static function realPath($path, $addSlash = FALSE, $slash = '/') {
		$path = realpath($path);
		if ($path === FALSE) {
			return FALSE;
		}
		$path = str_replace(array('/', '\\'), $slash, $path);
		if ($addSlash === TRUE) {
			$path = rtrim($path, $slash) . $slash;
		}
		return $path;
	}
	/**
	 * Takes a numeric value representing a file's permissions and returns
	 * a three character string representing the file's octal permissions.
	 *
	 * @static
	 * @access public
	 * @param int $filePermissions
	 * @return string
	 */
	public static function permissionsToOctal($filePermissions) {
		return substr(sprintf('%o', $filePermissions), -3);
	}
	/**
	 * Takes a numeric value representing a file's permissions and returns
	 * three character string representing the file's octal permissions.
	 *
	 * @static
	 * @access public
	 * @param int $fileName
	 * @return string
	 */
	public static function octalPermissions($fileName) {
		return self::permissionsToOctal(fileperms($fileName));
	}
	/**
	 * Returns standard symbolic notation representing that value.
	 *
	 * @access public
	 * @param int $filePermissions
	 * @return string
	 */
	public static function permissionsToSymbolic($filePermissions) {
		if (($filePermissions & 0xC000) == 0xC000) {
			$symbolic = 's'; // Socket
		}
		elseif (($filePermissions & 0xA000) == 0xA000) {
			$symbolic = 'l'; // Symbolic Link
		}
		elseif (($filePermissions & 0x8000) == 0x8000) {
			$symbolic = '-'; // Regular
		}
		elseif (($filePermissions & 0x6000) == 0x6000) {
			$symbolic = 'b'; // Block special
		}
		elseif (($filePermissions & 0x4000) == 0x4000) {
			$symbolic = 'd'; // Directory
		}
		elseif (($filePermissions & 0x2000) == 0x2000) {
			$symbolic = 'c'; // Character special
		}
		elseif (($filePermissions & 0x1000) == 0x1000) {
			$symbolic = 'p'; // FIFO pipe
		}
		else {
			$symbolic = 'u'; // Unknown
		}

		// owner
		$symbolic .= (($filePermissions & 0x0100) ? 'r' : '-');
		$symbolic .= (($filePermissions & 0x0080) ? 'w' : '-');
		$symbolic .= (($filePermissions & 0x0040) ? (($filePermissions & 0x0800) ? 's' : 'x' ) : (($filePermissions & 0x0800) ? 'S' : '-'));
		// group
		$symbolic .= (($filePermissions & 0x0020) ? 'r' : '-');
		$symbolic .= (($filePermissions & 0x0010) ? 'w' : '-');
		$symbolic .= (($filePermissions & 0x0008) ? (($filePermissions & 0x0400) ? 's' : 'x' ) : (($filePermissions & 0x0400) ? 'S' : '-'));
		// world
		$symbolic .= (($filePermissions & 0x0004) ? 'r' : '-');
		$symbolic .= (($filePermissions & 0x0002) ? 'w' : '-');
		$symbolic .= (($filePermissions & 0x0001) ? (($filePermissions & 0x0200) ? 't' : 'x' ) : (($filePermissions & 0x0200) ? 'T' : '-'));

		return $symbolic;
	}
	/**
	 * Takes a numeric value representing a file's permissions and returns
	 * standard symbolic notation representing that value.
	 *
	 * @access public
	 * @param int $fileName
	 * @return string
	 */
	public static function symbolicPermissions($fileName) {
		return self::permissionsToSymbolic(fileperms($fileName));
	}
	/**
	 * Takes file extension string in lower case.
	 *
	 * @static
	 * @access public
	 * @param string
	 * @return string
	 */
	public static function fileExtension($fileName) {
		return strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
	}
	/**
	 * Get file size. Extend 2GB filesize limit to 4GB.
	 *
	 * @static
	 * @access public
	 * @param string $fileName
	 * @return string
	 */
	public static function fileSize($fileName) {
		if (is_dir($fileName)) {
			$result = explode("\t", exec("du -hs " . $fileName), 2);
			$fileSize = ($result[1] == $fileName ? $result[0] : NULL);
		}
		else {
			$fileSize = filesize($fileName);
		}
		return sprintf("%u", $fileSize);
	}
	/**
	 * Takes a humanized value of size.
	 *
	 * @static
	 * @access public
	 * @param int $fileSize
	 * @param array $options
	 * @return string
	 * @links http://en.wikipedia.org/wiki/Units_of_information
	 */
	public static function humanizeSize($fileSize, $options = array()) {
		isset($options['unit']) || $options['unit'] = 'byte';
		isset($options['standard']) || $options['standard'] = 'si';
		isset($options['width']) || $options['width'] = 'narrow';
		isset($options['prefix']) || $options['prefix'] = ' ';
		isset($options['round']) || $options['round'] = 2;
		$options['units'] = array(
			'byte' => array(
				'singular' => array(
					'narrow' => 'B',
					'wide' => 'byte'
				),
				'plural' => array(
					'narrow' => 'B',
					'wide' => 'bytes'
				),
			),
			'bit' => array(
				'singular' => array(
					'narrow' => 'bit',
					'wide' => 'bit'
				),
				'plural' => array(
					'narrow' => 'bits',
					'wide' => 'bits'
				),
			),
		);
		$options['standards'] = array(
			'si' => array(
				'narrow' => array("", "k", "M", "G", "T", "P", "E", "Z", "Y"),
				'wide' => array("", "kilo", "mega", "giga", "tera", "peta", "exa", "zetta", "yotta"),
			),
			'iec' => array(
				'narrow' => array("", "Ki", "Mi", "Gi", "Ti", "Pi", "Ei", "Zi", "Yi"),
				'wide' => array("", "kibi", "mebi", "gibi", "tebi", "pebi", "exbi", "zebi", "yobi"),
			),
		);

		switch($options['unit'] = strtolower($options['unit'])) {
			case 'bit':
			case 'bits':
			case 'b':
				$options['unit'] = 'bit';
				break;
			default:
				$options['unit'] = 'byte';
		}
		switch($options['standard'] = strtolower($options['standard'])) {
			case 'i':
			case 'iec':
				$options['standard'] = 'iec';
				break;
			default:
				$options['standard'] = 'si';
		}
		switch($options['width'] = strtolower($options['width'])) {
			case 'w':
			case 'wide':
				$options['width'] = 'wide';
				break;
			default:
				$options['width'] = 'narrow';
		}
		$factor = ($options['standard'] == 'si') ? 1000 : 1024;

		$i = 0;
		if ($options['unit'] == 'bit') {
			$fileSize *= 8;
		}
		while($fileSize > $factor) {
			$fileSize /= $factor;
			$i++;
		}
		$fileSize = round($fileSize, $options['round']);
		$n = $fileSize == 0 || $fileSize == 1 ? 'singular' : 'plural';
		$humanizeSize = $fileSize . $options['prefix'] . $options['standards'][$options['standard']][$options['width']][$i] . $options['units'][$options['unit']][$n][$options['width']];
		return $humanizeSize;
	}
	/**
	 * Takes a humanized value of last date and time in application locale.
	 *
	 * @static
	 * @access public
	 * @param int $time
	 * @param string $format
	 * @return string
	 */
	public static function humanizeTime($time, $format = '') {
		if (empty($format) === TRUE) {
			return Yii::app()->dateFormatter->formatDateTime($time);
		}
		return Yii::app()->dateFormatter->format($format, $time);
	}

	public static function removeDirectory($dir, $removeSubFiles = TRUE) {
		d(glob($dir), 10);
		foreach (glob($dir) as $file) {
			if (is_dir($file)) {
				self::removeDirectory("{$file}/.*");
				//rmdir($file);
			}
			else {
				//unlink($file);
			}
		}
	}
}
