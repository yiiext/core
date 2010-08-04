<?php

Yii::import('application.components.EChmMarkdownParser');
Yii::import('ext.yiiext.components.chm.EChm');

class ChmCommand extends CConsoleCommand
{
	protected $type;
	protected $language;
	protected $guidesPath='webroot.guide';
	protected $chmDir='webroot.files';

	protected $parser;
	protected $tmpPath;
	
	public function getHelp()
	{
		return "USAGE\n".
			"php {$this->getCommandRunner()->getScriptName()} {$this->name} <type> <language> [<txt-guides-path>] [<output-directory>]\n\n".
			"DESCRIPTION\n".
			"This command can build chm Yii docs from markdown source.\n\n".
			"PARAMETERS\n".
			"<type> - required, the document type, can be guide or cookbook.\n".
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
		$this->type=$args[0];
		$this->language=$args[1];
		!isset($args[2]) OR $this->guidesPath=$args[2];
		!isset($args[3]) OR $this->chmDir=$args[3];

		// Check guides path
		if($this->type!='guide' && $this->type!='cookbook')
			$this->usageError('The type is required.');

		// Guides path alias into path
		if(($guidesPath=Yii::getPathOfAlias($this->guidesPath))!==FALSE)
			$this->guidesPath=$guidesPath;
		
		// .chm dir alias into path
		if(($chmDir=Yii::getPathOfAlias($this->chmDir))!==FALSE)
			$this->chmDir=$chmDir;

		// Check guides path
		if(!is_dir($this->guidesPath))
			$this->usageError('The guides path ['.$this->guidesPath.'] is not avaible.');

		$sourcePath=$this->guidesPath.'/'.$this->language;

		// Check language exists
		if(!is_dir($sourcePath))
			$this->usageError('The translation into "'.$this->language.'" is not found.');

		// Check .chm dir
		if(!is_dir($this->chmDir))
			$this->ensureDirectory($this->chmDir);

		$this->chmDir=realpath($this->chmDir);
		$sourcePath=realpath($sourcePath);
		$chmPath=$this->chmDir.'/yii-'.$this->type.'-'.$this->language.'.chm';

		echo " Processing: ".$sourcePath."\n";
		
		// save application config
		$name=Yii::app()->name;
		Yii::app()->name=($this->type=='guide'?'The Definite Guide to Yii':'The Yii Cookbook').' | Offline ['.$this->language.']';
		$charset=Yii::app()->charset;
		Yii::app()->charset='windows-1251';

		$this->parser=new EChmMarkdownParser($this->type);
		$this->tmpPath=Yii::getPathOfAlias('application.runtime.chm'.$this->type);
		$this->ensureDirectory($this->tmpPath);

		echo " Creating CHM-project.\n";
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
		
		echo " Adding a window to the project.\n";
		$chm->addWindow(array(
			'id'=>md5(Yii::app()->name.time()),
			'title'=>Yii::app()->name,
			'defaultFile'=>'index.html',
			'homeFile'=>'index.html',
			'navigationStyle'=>'0x63420',
			'position'=>'[50,50,950,700]',
			'navigationWidth'=>250,
		));

		$layout=dirname(__FILE__).'/chm/main.php';
		echo " Coping assets to tmpdir.\n";
		$files=CFileHelper::findFiles(dirname(__FILE__).'/chm',array(
			'exclude'=>array('main.php'),
			'level'=>0,
		));
		foreach($files as $file)
		{
			copy($file,$this->tmpPath.'/'.basename($file));
			$chm->addFile($this->tmpPath.'/'.basename($file));
		}

		$this->copyImagesToTmp($this->guidesPath.'/sources/images');
		$this->copyImagesToTmp($sourcePath.'/images');

		echo " Reading toc.txt\n";
		$toc=$sourcePath.'/toc.txt';
		$srcToc=file($toc);

		$expCategory="/\* (.+)/iu";
		$expItem="/- \[(.+)\]\((.+)\)/iu";
		$lastToc=NULL;
		$lastKey=NULL;
		echo " Parsing toc.txt items\n";
		foreach($srcToc as $line)
		{
			if(preg_match($expCategory,$line,$matches))
			{
				$matches[1]=trim($matches[1]);
				echo " Adding section [".iconv("UTF-8","CP866",$matches[1])."]\n";
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
				echo " Adding item [".iconv("UTF-8","CP866",$matches[1])."]\n";
				$lastToc->addChild(array(
					'name'=>$matches[1],
					'local'=>$matches[2].'.html'
				));
				$lastKey->addChild(array(
					'name'=>$matches[1],
					'local'=>$matches[2].'.html'
				));
				// transform .txt to .html
				echo " Adding file [".iconv("UTF-8","CP866",$matches[2])."]\n";
				$chm->addFile($this->transformFile($sourcePath.'/'.$matches[2].'.txt',$matches[1],$layout));
			}
		}

		echo " Saving ".iconv("UTF-8","CP866",$chmPath)."\n";
		$chm->save();

		echo " Deleting temporary files\n";
		self::deleteTmpFiles($this->tmpPath);

		echo " Process complete!\n";

		// restore application config
		Yii::app()->name=$name;
		Yii::app()->charset=$charset;
	}
	protected function copyImagesToTmp($path)
	{
		if(is_dir($path))
		{
			echo " Copying images to tmpdir [".$path."].\n";
			CFileHelper::copyDirectory($path,$this->tmpPath,array(
				'fileTypes'=>array('jpg','jpeg','png','gif','css'),
				'level'=>0,
			));
		}
	}
	protected function transformFile($filePath,$title,$layout)
	{
		if(!file_exists($filePath))
		{
			echo " File {$filePath} does not exist. Skipping.\n";
			return;
		}
		$filePath=realpath($filePath);

		echo " Transform file [".iconv("UTF-8","CP866",$filePath)."]\n";
		$content=$this->parser->transform(file_get_contents($filePath));
		echo " Render HTML [".iconv("UTF-8","CP866",$filePath)."]\n";
		$content=$this->renderFile($layout,array('content'=>$content,'title'=>$title),true);

		$htmlFileName=$this->tmpPath.'/'.str_replace('.txt','.html',basename($filePath));
		echo " Save HTML file [".iconv("UTF-8","CP866",$htmlFileName)."]\n";
		file_put_contents($htmlFileName,iconv("UTF-8","CP1251",$content));
		return $htmlFileName;
	}
	public static function deleteTmpFiles($path)
	{
		if(is_file($path))
		{
			echo " Delete file [".iconv("UTF-8","CP866",$path)."]\n";
			return unlink($path);
		}

		if(is_dir($path))
		{
			$files=glob(rtrim($path,'/').'/*');
			foreach($files as $index=>$filePath)
				self::deleteTmpFiles($filePath);

			echo " Delete directory [".iconv("UTF-8","CP866",$path)."]\n";
			return rmdir($path);
		}
	}
}
