<?php

class EImageValidator extends CFileValidator{
	/**
	 * mime типы которые позволено загружать,
	 * если NULL проверка не производится
	 * @var string or array
	 */
	public $mime = null;
	
	/**
	 * Минимальная ширина картинки в пикселях, по стандарту 0
	 * @var unsigned int
	 */
	public $minWidth = 0;
	
	/**
	 * Максимальная ширина картинки в пикселях, по стандарту 500000
	 * @var unsigned int
	 */
	public $maxWidth = 500000;
	
	/**
	 * Минимальная высота картинки в пикселях, по стандарту 0
	 * @var unsigned int
	 */
	public $minHeight = 0;
	
	/**
	 * Максимальная высота картинки в пикселях, по стандарту 500000
	 * @var unsigned int
	 */
	public $maxHeight = 500000;
	
	/**
	 * Текст ошибки при слишком большой ширине картинки
	 * @var unsigned int
	 */
	public $tooLargWidth;
	
	/**
	 * Текст ошибки при слишком маленькой ширине картинки
	 * @var unsigned int
	 */
	public $tooSmallWidth;
	
	/**
	 * Текст ошибки при слишком большой высоте картинки
	 * @var unsigned int
	 */
	public $tooLargHeight;
	
	/**
	 * Текст ошибки при слишком маленькой высоте картинки
	 * @var unsigned int
	 */
	public $tooSmallHeight;
		
	/**
	 * Сообщение, если не подходит mime тип указаным
	 * @var string
	 */
	public $wrongMime;
	
	/**
	 * Сообщение, если это не картинка (не может быть обработано getimagesize)
	 * @var string
	 */
	public $notImage;
	
	/**
	 * Проверка подходит ли mime тип
	 * @param string $attribute
	 * @param CUploadedFile $file
	 */
	private function checkMime($attribute, $file)
	{
		if($this->mime === null){
			return true;
		}
		
		if(is_array($this->mime)){
			if(in_array($file->getType(), $this->mime)){
				return true;
			}else{
				return false;
			}
		}elseif(is_string($this->mime)){
			if($file->getType() == $this->mime){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CFileValidator::validateFile()
	 */
	protected function validateFile($object, $attribute, $file)
	{
		parent::validateFile($object, $attribute, $file);
	
		$size = getimagesize($file->getTempName());
		
		if($size === false){
			$message=$this->message ? $this->message : Yii::t('yii','This is not a picture');
			$this->addError($object,$attribute,$message,array('{mime}'=>(is_array($this->mime) ? implode(', ', $this->mime) : $this->mime)));
			return;
		}
		
		if(!$this->checkMime($attribute, $file)){
			$message=$this->wrongMime ? $this->wrongMime : Yii::t('yii','This mime type of the photo is not allowed, mime types: {mime}');
			$this->addError($object,$attribute,$message,array('{mime}'=>(is_array($this->mime) ? implode(', ', $this->mime) : $this->mime)));
		}
		
		if($size[0] < $this->minWidth){
			$message=$this->tooSmallWidth ? $this->tooSmallWidth : Yii::t('yii','Photo should be at least {width}px in width');
			$this->addError($object,$attribute,$message,array('{width}'=>$this->minWidth));
		}
		
		if($size[0] > $this->maxWidth){
			$message=$this->tooLargWidth ? $this->tooLargWidth : Yii::t('yii','Photo should be at max {width}px in width');
			$this->addError($object,$attribute,$message,array('{width}'=>$this->maxWidth));
		}
		
		if($size[1] < $this->minHeight){
			$message=$this->tooSmallHeight ? $this->tooSmallHeight : Yii::t('yii','Photo should be at least {height}px in height');
			$this->addError($object,$attribute,$message,array('{width}'=>$this->minHeight));
		}
		
		if($size[1] > $this->maxHeight){
			$message=$this->tooLargHeight ? $this->tooLargHeight : Yii::t('yii','Photo should be at max {height}px in height');
			$this->addError($object,$attribute,$message,array('{width}'=>$this->maxHeight));
		}
	}
}