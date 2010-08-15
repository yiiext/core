<?php

Yii::import('application.components.EChmMarkdownParser');
Yii::import('ext.yiiext.components.chm.EChm');

class ChmCommand extends CConsoleCommand
{
	protected $type;
	protected $language;
	protected $sourcePath='webroot.guide';
	protected $outputPath='webroot.files';
	protected $defaultTopic='index';

	protected $parser;
	protected $tmpPath;

	public function getHelp()
	{
		return "USAGE\n".
			"php {$this->getCommandRunner()->getScriptName()} {$this->name} <type> <language> [<markdown-sources-path>] [<output-path>] [<default-topic>]\n\n".
			"DESCRIPTION\n".
			"This command can build chm Yii docs from markdown source.\n\n".
			"ARGUMENTS\n".
			"<type> - required, the document type, can be guide or cookbook.\n".
			"<language> - required, the language to convert.\n".
			"<markdown-sources-path> - optional, the path or alias of directory where markdown source is stored. Default is \"webroot.guide\".\n".
			"<output-path> - optional, the path or alias of directory where .chm will be generated. Default is \"webroot.files\".\n".
			"<default-topic> - optional, index page for the guide. Default is \"index\".\n";
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
		if(isset($args[2]))
			$this->sourcePath=$args[2];

		if(isset($args[3]))
			$this->outputPath=$args[3];

		if(isset($args[4]))
			$this->defaultTopic = $args[4];

		// Check type
		if(empty($this->type))
			$this->usageError('The type is required.');

		// Guides path alias into path
		if($guidesPath=Yii::getPathOfAlias($this->sourcePath))
			$this->sourcePath=$guidesPath;

		// .chm dir alias into path
		if($outputPath=Yii::getPathOfAlias($this->outputPath))
			$this->outputPath=$outputPath;

		// Check guides path
		if(!is_dir($this->sourcePath))
			$this->usageError('The path ['.$this->sourcePath.'] is not available.');

		$sourcePath=$this->sourcePath.'/'.$this->language;

		// Check id language exists
		if(!is_dir($sourcePath))
			$this->usageError('Can\'t find '.$this->language.' translation.');

		// Check if output dir exists
		if(!is_dir($this->outputPath))
			$this->ensureDirectory($this->outputPath);

		$this->outputPath=realpath($this->outputPath);
		$sourcePath=realpath($sourcePath);
		$outputPath=$this->outputPath.'/yii-'.$this->type.'-'.$this->language.'.chm';

		echo " Processing: ".$sourcePath."\n";

		//getting doc title
		$f = fopen($sourcePath.'/'.$this->defaultTopic.'.txt', 'r');
		$title = trim(fgets($f));
		fclose($f);

		// save application config
		$charset=Yii::app()->charset;
		Yii::app()->charset='windows-1251';
		$title .= ' - Yii';

		$this->parser=new EChmMarkdownParser($this->type);
		$this->tmpPath=Yii::getPathOfAlias('application.runtime.chm'.$this->type);
		$this->ensureDirectory($this->tmpPath);

		echo " Creating CHM-project.\n";
		$chm=new EChm(array(
			'binaryIndex'=>'No',
			'compatibility'=>'1.1 or later',
			'defaultFont'=>'Tahoma,10,0',
			'defaultTopic'=>$this->defaultTopic.'.html',
			'displayCompileProgress'=>'No',
			'enhancedDecompilation'=>'Yes',
			'fullTextSearch'=>'Yes',
			'language'=>'0x419 Russian (Russia)',
			'title'=>$title,
			'path'=>$this->tmpPath.'/guide.hhp',
			'contentsFile'=>$this->tmpPath.'/guide.hhc',
			'indexFile'=>$this->tmpPath.'/guide.hhk',
			'compiledFile'=>$outputPath,
		));

		echo " Adding a window to the project.\n";
		$chm->addWindow(array(
			'id'=>md5($title.time()),
			'title'=>$title,
			'defaultFile'=>$this->defaultTopic.'.html',
			'homeFile'=>$this->defaultTopic.'.html',
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

		$this->copyImagesToTmp($this->sourcePath.'/source/images');
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

		echo " Saving ".iconv("UTF-8","CP866",$outputPath)."\n";
		$chm->save();

		echo " Deleting temporary files\n";
		self::deleteTmpFiles($this->tmpPath);

		echo " Process complete!\n";

		// restore application config
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
		$content=$this->parser->transform($this->encodeEntities(file_get_contents($filePath)));
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

	protected function encodeEntities($text){
		$replaces = array(
			'→' => '&rarr;',
			'←' => '&larr;',
		);
		return str_replace(array_keys($replaces), array_values($replaces), $text);
	}
}
