<?php
/**
 * EFormModelBehavior class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */
/**
 * EFormModelBehavior attach instance of EForm for model for easy create form.
 *
 * Also add helper to generate array of default text elements based of {@link CModel::getAttributes()}.
 *
 * @author Maxim Furtuna (Ekstazi)
 * @version 0.2
 * @package yiiext.components.form
 */
require_once(dirname(__FILE__).'/EForm.php');
class EFormModelBehavior extends CModelBehavior
{
	/**
	 * @var array initial configuration for form.
	 */
	public $config;
	/**
	 * @var EForm main object
	 */
	protected $_form;
    public $id;
    public $ajaxValidation=true;
	
	/**
	 * Get main form.
	 * @return EForm
	 */
	public function getForm()
	{
		if($this->_form===null)
		{
			if(!($owner instanceof CBaseController))
				$owner=Yii::app()->getController();
            if(empty($this->config)||!is_array($this->config)){
                $this->config=array(
                    'buttons'=>array(
                        'submit'=>array(
                            'type'=>'submit',
                            'label'=>Yii::t('yiiext','Save'),
                            'on'=>'insert,update',
                        ),
                        'reset'=>array(
                            'type'=>'reset',
                            'label'=>Yii::t('yiiext','Reset'),
                            'on'=>'insert,update',
                        ),
                        'search'=>array(
                            'type'=>'submit',
                            'label'=>Yii::t('yiiext','Search'),
                            'on'=>'search'
                        ),
                    )
                );
            }
			$this->_form=new EForm($this->config,$this->getOwner(),null);
			$this->_form->setElements($this->getFormElements());
            if(!isset($this->id))
                $this->id=sprintf('%x',crc32(serialize(array_keys($this->_form->getElements()->toArray()))));
            $this->_form->id=$this->id;
            $this->_form->activeForm=array_merge($this->_form->activeForm,array(
                'enableAjaxValidation'=>$this->ajaxValidation,
                'id'=>$this->id
            ));
		}
		return $this->_form;
	}
	/**
	 * Render form.
	 */
	public function render()
	{
		return $this->getform()->render();
	}
	/**
	 * Get array elements based of {@link CModel::getAttributes()}.
	 * Type of all elements will be text.
	 * @todo generate other types for inputs
	 * @return array
	 */
	public function getFormElements()
	{
		$model=$this->getOwner();
//        if($model->)
		if(method_exists($model,'getFormElements'))
			return $model->getFormElements();
		else
		{
			$elements=array();
			foreach($model->getAttributes() as $attribute=>$value)
				$elements[$attribute]=array(
					'type'=>'text',
				);
			return $elements;
		}
	}
    /**
     *
     * @return bool status of using ajax validation
     */
    public function getAjaxValidation()
    {
        return $this->getForm()->activeForm['enableAjaxValidation'];
    }

    /**
     * Performs check for ajax request and generate responce of validation status
     * @return true to allow use in if condition
     */
    protected function performAjax()
    {
        $ajaxVar=strcasecmp($this->getForm()->method,'get') ? $_POST['ajax'] : $_GET['ajax'];
        if($this->getAjaxValidation()&&isset($ajaxVar)&&$ajaxVar===$this->getForm()->activeForm['id']){
            $this->getForm()->loadData();
		// because of renderPartial method needs to clean output buffer
            ob_get_clean();
            echo CActiveForm::validate($this->getOwner(),null,false);
            Yii::app()->end();
        }
        return true;
    }
    /**
     * Performs check for submission, then process ajax request and
     * load attributes into model
     * @param string $button name of clicked button
     * @return boolean validation status
     */
    public function submitted($button='submit')
    {
        return $this->performAjax()&&$this->getForm()->submitted($button);
    }
    /**
     * Performs Performs check for submission, then process ajax request,
     * load attributes into model and save it
     * @param string $button name of clicked button
     * @return boolean saving status
     */
    public function saved($button='submit')
    {
        return $this->submitted($button)&&$this->getOwner()->save();
    }
    /**
     * Performs Performs check for submission, then process ajax request,
     * load attributes into model and validate it
     * @param string $button name of clicked button
     * @return boolean validation status
     */
    public function validated($button='submit')
    {
        return $this->submitted($button)&&$this->getOwner()->validate();
    }
}
