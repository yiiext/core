<?php
/**
 * EChmHhpFile class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */

/**
 * EChmHhpFile.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.components.chm
 */
class EChmHhpFile extends EChmFile
{
	public $path;
	public $autoIndex;
	public $autoToc;
	public $binaryIndex;
	public $binaryToc;
	public $citation;
	public $compress;
	public $copyright;
	public $compatibility;
	public $compiledFile;
	public $contentsFile;
	public $createChiFile;
	public $defaultFont;
	public $defaultWindow;
	public $defaultTopic;
	public $displayCompileNotes;
	public $displayCompileProgress;
	public $enhancedDecompilation;
	public $errorLogFile;
	public $flat;
	public $fullTextSearchStopListFile;
	public $fullTextSearch;
	public $ignore;
	public $indexFile;
	public $language;
	public $prefix;
	public $sampleStagingPath;
	public $sampleListFile;
	public $tmpDir;
	public $title;
	public $customTab;

	protected $_windows=array();
	protected $_files=array();

	public function __construct($config=NULL)
	{
		if(!isset($config['compiledFile']))
			throw new CException(Yii::t('yiiext','HHP CompiledFile is required.'));

		parent::__construct($config);
	}
	public function addFile($path)
	{
		return $this->_files[]=$path;
	}
	public function addWindow($config=NULL)
	{
		$window=new EChmWindow($this,$config);
		return $this->_windows[$window->id]=$window;
	}
	public function getFiles()
	{
		return $this->_files;
	}
	public function getWindows()
	{
		return $this->_windows;
	}
	public static function encodeIni($section,$lines)
	{
		$ini='['.$section.']'."\n";
		if(is_string($lines))
			$ini.=$lines;
		else if(is_array($lines))
			foreach($lines as $key=>$value)
				$ini.=$key.'='.$value."\n";

		return $ini;
	}
	public function render()
	{
		$windows=$this->getWindows();
		$strWindows='';
		foreach($windows as $window)
			$strWindows.=$window->toString()."\n";

		$text=self::encodeIni('OPTIONS',$this->getParams());
		$text.=self::encodeIni('WINDOWS',$strWindows);
		$text.=self::encodeIni('FILES',implode("\n",$this->getFiles()));
		return $text;
	}
}
