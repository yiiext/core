<?php
/**
 * Improved error handler for Yii
 *
 * @author Alexander Makarov
 * @version 1.1
 */
class EImprovedErrorHandler extends CErrorHandler
{
	private $_error;

	/**
	 * Handles the exception.
	 * @param Exception the exception captured
	 */
	protected function handleException($exception)
	{
		$app=Yii::app();
		if($app instanceof CWebApplication)
		{
			if(($trace=$this->getExactTrace($exception))===null)
			{
				$fileName=$exception->getFile();
				$errorLine=$exception->getLine();
			}
			else
			{
				$fileName=$trace['file'];
				$errorLine=$trace['line'];
			}

			$trace = $exception->getTrace();

			foreach($trace as $i=>$t)
			{
				if(!isset($t['file']))
					$trace[$i]['file']='unknown';

				if(!isset($t['line']))
					$trace[$i]['line']=0;

				if(!isset($t['function']))
					$trace[$i]['function']='unknown';

				unset($trace[$i]['object']);
			}

			$this->_error=$data=array(
				'code'=>($exception instanceof CHttpException)?$exception->statusCode:500,
				'type'=>get_class($exception),
				'message'=>$exception->getMessage(),
				'file'=>$fileName,
				'line'=>$errorLine,
				'trace'=>$trace,
				'source'=>$this->getSourceLines($fileName,$errorLine),
			);

			if(!headers_sent())
				header("HTTP/1.0 {$data['code']} ".get_class($exception));
			if($exception instanceof CHttpException || !YII_DEBUG)
				$this->render('error',$data);
			else
				$this->render('exception',$data);
		}
		else
			$app->displayException($exception);
	}

	/**
	 * Handles the PHP error.
	 * @param CErrorEvent the PHP error event
	 */
	protected function handleError($event)
	{
		$trace=debug_backtrace();
		// skip the first 3 stacks as they do not tell the error position
		if(count($trace)>3)
			$trace=array_slice($trace,3);

		foreach($trace as $i=>$t)
		{
			if(!isset($t['file']))
				$trace[$i]['file']='unknown';

			if(!isset($t['line']))
				$trace[$i]['line']=0;

			if(!isset($t['function']))
				$trace[$i]['function']='unknown';

			unset($trace[$i]['object']);
		}

		$app=Yii::app();
		if($app instanceof CWebApplication)
		{
			$this->_error=$data=array(
				'code'=>500,
				'type'=>'PHP Error',
				'message'=>$event->message,
				'file'=>$event->file,
				'line'=>$event->line,
				'trace'=>$trace,
				'source'=>$this->getSourceLines($event->file,$event->line),
			);
			if(!headers_sent())
				header("HTTP/1.0 500 PHP Error");
			if(YII_DEBUG)
				$this->render('exception',$data);
			else
				$this->render('error',$data);
		}
		else
			$app->displayError($event->code,$event->message,$event->file,$event->line);
	}

	/**
	 * Determines which view file should be used.
	 * @param string view name (either 'exception' or 'error')
	 * @param integer HTTP status code
	 * @return string view file path
	 */
	protected function getViewFile($view,$code)
	{
		$viewPaths=array(
			Yii::app()->getTheme()===null ? null :  Yii::app()->getTheme()->getSystemViewPath(),
			Yii::app() instanceof CWebApplication ? Yii::app()->getSystemViewPath() : null,
			dirname(__FILE__).'/views',
			YII_PATH.DIRECTORY_SEPARATOR.'views',
		);

		foreach($viewPaths as $i=>$viewPath)
		{
			if($viewPath!==null)
			{
				 $viewFile=$this->getViewFileInternal($viewPath,$view,$code,$i===2?'en_us':null);
				 if(is_file($viewFile))
				 	 return $viewFile;
			}
		}
	}

	/**
	 * Returns the source lines around the error line.
	 * At most {@link maxSourceLines} lines will be returned.
	 * @param string source file path
	 * @param integer the error line number
	 * @return array source lines around the error line, indxed by line numbers
	 */
	protected function getSourceLines($file,$line)
	{
		// determine the max number of lines to display
		$maxLines=$this->maxSourceLines;
		if($maxLines<1)
			$maxLines=1;
		else if($maxLines>100)
			$maxLines=100;

		$line--;	// adjust line number to 0-based from 1-based
		if($line<0 || ($lines=@file($file))===false || ($lineCount=count($lines))<=$line)
			return array();

		$halfLines=(int)($maxLines/2);
		$beginLine=$line-$halfLines>0?$line-$halfLines:0;
		$endLine=$line+$halfLines<$lineCount?$line+$halfLines:$lineCount-1;

		$sourceLines=array();
		for($i=$beginLine;$i<=$endLine;++$i)
			$sourceLines[$i+1]=$lines[$i];
		return $sourceLines;
	}

	protected function argumentsToString($args){
		foreach($args as $key => $value){
			if(is_object($value)){
				if($value instanceof Iterator){
					$args[$key] = get_class($value).'('.$this->argumentsToString($value).')';
				}
				else {
					$args[$key] = get_class($value);
				}
			}
			else if(is_string($value)){
				$args[$key] = '"'.$value.'"';
			}
			else if(is_array($value)){
				$args[$key] = 'array('.$this->argumentsToString($value).')';
			}
			else if(is_resource($value)) {
				$args[$key] = 'resource';
			}
		}

		return implode(', ', $args);
	}
}
