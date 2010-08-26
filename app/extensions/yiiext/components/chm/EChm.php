<?php
/**
 * Chm class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */

require_once(dirname(__FILE__).'/EChmFile.php');
require_once(dirname(__FILE__).'/EChmHhcFile.php');
require_once(dirname(__FILE__).'/EChmHhkFile.php');
require_once(dirname(__FILE__).'/EChmHhcItem.php');
require_once(dirname(__FILE__).'/EChmHhkItem.php');
require_once(dirname(__FILE__).'/EChmHhpFile.php');
require_once(dirname(__FILE__).'/EChmWindow.php');

/**
 * Chm.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.components.chm
 */
class EChm extends CComponent
{
	protected $toc;
	protected $index;
	protected $project;

	public function __construct($projectOptions=array(),$tocOptions=array(),$indexOptions=array())
	{
		$this->project=new EChmHhpFile($projectOptions);
		$this->toc=new EChmHhcFile($tocOptions);
		$this->index=new EChmHhkFile($indexOptions);
	}

	public function save()
	{
		$name=basename($this->project->compiledFile,'.chm');
		$dir=dirname($this->project->compiledFile);

		if(!is_dir($dir))
			throw new CException(Yii::t('yiiext','Directory "{directory} not available.".',array('{directory}'=>$dir)));

		if($this->project->contentsFile===NULL)
			$this->project->contentsFile=$dir.'/'.$name.'.hhc';

		if($this->project->indexFile===NULL)
			$this->project->indexFile=$dir.'/'.$name.'.hhk';

		if($this->project->path===NULL)
			$this->project->path=$dir.'/'.$name.'.hhp';

		$windows=$this->project->getWindows();
		if($this->project->defaultWindow===NULL || !key_exists($this->project->defaultWindow,$windows))
		{
			reset($windows);
			$this->project->defaultWindow=current($windows)->id;
		}

		$this->toc->save($this->project->contentsFile);
		$this->index->save($this->project->indexFile);
		$this->project->save($this->project->path);

		// generate project
		$exeFile=dirname(__FILE__).'/vendors/hhc.exe';
		exec('"'.$exeFile.'" "'.$this->project->path.'"', $output, $return_var);

		if(!file_exists($this->project->compiledFile))
			throw new CException(Yii::t('yiiext','Cannot save .chm file "{file}".'."\nReturn: $return_var\nOutput:\n".implode("\n", $output),array('{file}'=>$this->project->compiledFile)));

		return $this->project->compiledFile;
	}
	public function addWindow($config)
	{
		$window=$this->project->addWindow($config);
		//$window->tocPath=$this->project->contentsFile;
		//$window->indexPath='guide.hhk';
		return $window;
	}
	public function addFile($path)
	{
		return $this->project->addFile($path);
	}
	public function addTocItem($config)
	{
		return $this->toc->addItem($config);
	}
	public function addIndexItem($config)
	{
		return $this->index->addItem($config);
	}
}
