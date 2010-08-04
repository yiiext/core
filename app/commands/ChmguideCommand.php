<?php

Yii::import('application.components.EChmMarkdownParser');
Yii::import('ext.yiiext.components.chm.EChm');

class ChmguideCommand extends CConsoleCommand
{
	protected $language;
	protected $guidesPath='webroot.guide';
	protected $chmDir='webroot.files';

	protected $parser;
	protected $tmpPath;

	public function getHelp()
	{
		return "USAGE\n".
			"php {$this->getCommandRunner()->getScriptName()} {$this->name} <language> [<txt-guides-path>] [<output-directory>]\n\n".
			"DESCRIPTION\n".
			"This command can build chm Yii docs from markdown source.\n\n".
			"PARAMETERS\n".
			"<language> - required, the language to convert.\n".
			"<txt-guide-path> - optional, the path or alias of directory where markdown source is stored. Default \"{$this->guidesPath}\".\n".
			"<output-directory> - optional, the path or alias of directory where .chm will be generated. Default \"{$this->chmDir}\".\n";
	}
	/**
	 * Execute the action.
	 */
	public function run($args)
	{
		if(empty($args))
			die($this->getHelp());

		// Parse command arguments
		$this->language=$args[0];
		!isset($args[1]) OR $this->guidesPath=$args[1];
		!isset($args[2]) OR $this->chmDir=$args[2];

		// Guides path alias into path
		if(($guidesPath=Yii::getPathOfAlias($this->guidesPath))!==FALSE)
			$this->guidesPath=$guidesPath;

		// .chm dir alias into path
		if(($chmDir=Yii::getPathOfAlias($this->chmDir))!==FALSE)
			$this->chmDir=$chmDir;

		// Check guides path
		if(!is_dir($this->guidesPath))
			$this->usageError('The guides path is not avaible.');

		$sourcePath=$this->guidesPath.'/'.$this->language;

		// Check language exists
		if(!is_dir($sourcePath))
			$this->usageError('The translation into "'.$this->language.'" is not found.');

		// Check .chm dir
		if(!is_dir($this->chmDir))
			$this->ensureDirectory($this->chmDir);

		$this->chmDir=realpath($this->chmDir);
		$sourcePath=realpath($sourcePath);
		$chmPath=$this->chmDir.'/yii-guide-'.$this->language.'.chm';

		echo "      Processing: ".$sourcePath."\n";

		// save application config
		$name=Yii::app()->name;
		Yii::app()->name='The Definite Guide to Yii | Offline ['.$this->language.']';
		$charset=Yii::app()->charset;
		Yii::app()->charset='windows-1251';

		$this->parser=new EChmMarkdownParser();
		$this->tmpPath=Yii::getPathOfAlias('application.runtime.chmguide.'.md5($sourcePath));
		$this->ensureDirectory($this->tmpPath);
		$layout=dirname(__FILE__).'/ChmguideLayout.php';

		if(is_dir($sourcePath.'/images'))
		{
			echo "      Found source images directory. Copying...\n";
			CFileHelper::copyDirectory($this->guidesPath.'/source/images',$this->tmpPath,array(
				'fileTypes'=>array('jpg','jpeg','png','gif'),
				'level'=>0,
			));
			echo "      Found images directory. Copying...\n";
			CFileHelper::copyDirectory($sourcePath.'/images',$this->tmpPath,array(
				'fileTypes'=>array('jpg','jpeg','png','gif'),
				'level'=>0,
			));
		}

		echo "      Creating CHM-project.\n";
		$chm=new EChm(array(
			'binaryIndex'=>'No',
			'compatibility'=>'1.1 or later',
			'defaultFont'=>'Tahoma,10,0',
			'defaultTopic'=>'index.html',
			'displayCompileProgress'=>'No',
			'enhancedDecompilation'=>'Yes',
			'fullTextSearch'=>'Yes',
			'language'=>'0x419 Russian (Russia)',
			'title'=>Yii::app()->name,
			'path'=>$this->tmpPath.'/guide.hhp',
			'contentsFile'=>$this->tmpPath.'/guide.hhc',
			'indexFile'=>$this->tmpPath.'/guide.hhk',
			'compiledFile'=>$chmPath,
		));

		echo "      Adding a window to the project.\n";
		$chm->addWindow(array(
			'id'=>md5(Yii::app()->name.time()),
			'title'=>Yii::app()->name,
			'defaultFile'=>'index.html',
			'homeFile'=>'index.html',
			'navigationStyle'=>'0x63420',
			'position'=>'[50,50,950,700]',
			'navigationWidth'=>250,
		));

		echo "      Reading toc.txt\n";
		$toc=$sourcePath.'/toc.txt';
		$srcToc=file($toc);

		$expCategory="/\* (.+)/iu";
		$expItem="/- \[(.+)\]\((.+)\)/iu";
		$lastToc=NULL;
		$lastKey=NULL;
		echo "      Parsing toc.txt items\n";
		foreach($srcToc as $line)
		{
			if(preg_match($expCategory,$line,$matches))
			{
				$matches[1]=trim($matches[1]);
				echo "      Adding section [".iconv("UTF-8","CP866",$matches[1])."]\n";
				$lastToc=$chm->addTocItem(array(
					'name'=>$matches[1],
				));
				$lastKey=$chm->addIndexItem(array(
					'name'=>$matches[1],
				));
				continue;
			}
			if(preg_match($expItem,$line,$matches))
			{
				$matches[1]=trim($matches[1]);
				$matches[2]=trim($matches[2]);
				echo "      Adding item [".iconv("UTF-8","CP866",$matches[1])."]\n";
				$lastToc->addChild(array(
					'name'=>$matches[1],
					'local'=>$matches[2].'.html'
				));
				$lastKey->addChild(array(
					'name'=>$matches[1],
					'local'=>$matches[2].'.html'
				));
				// transform .txt to .html
				echo "      Adding file [".iconv("UTF-8","CP866",$matches[2])."]\n";
				$chm->addFile($this->makeFile($sourcePath.'/'.$matches[2].'.txt',$matches[1],$layout));
			}
		}

		//$chmPath=$this->tmpPath.'/yii-guide-'.$this->language.'.chm';
		echo "      Saving ".iconv("UTF-8","CP866",$chmPath)."\n";
		$chm->save();
		echo "      Process complete!\n";

		// restore application config
		Yii::app()->name=$name;
		Yii::app()->charset=$charset;
	}

	protected function makeFile($filePath,$title,$layout)
	{
		if(!file_exists($filePath))
		{
			echo "      File {$filePath} does not exist. Skipping.\n";
			return;
		}
		$filePath=realpath($filePath);

		echo "      Transform file [".iconv("UTF-8","CP866",$filePath)."]\n";
		$content=$this->parser->transform(file_get_contents($filePath));
		echo "      Render HTML [".iconv("UTF-8","CP866",$filePath)."]\n";
		$content=$this->renderFile($layout,array('content'=>$content,'title'=>$title),true);

		$htmlFileName=$this->tmpPath.'/'.str_replace('.txt','.html',basename($filePath));
		echo "      Save HTML file [".iconv("UTF-8","CP866",$htmlFileName)."]\n";
		file_put_contents($htmlFileName,iconv("UTF-8","CP1251",$content));
		return $htmlFileName;
	}
}
