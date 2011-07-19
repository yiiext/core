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
 * @version 0.4
 * @package yiiext.components.form
 */
require_once(dirname(__FILE__).'/EForm.php');
class EFormModelBehavior extends CModelBehavior
{
	/**
	 * @var array initial configuration for form.
	 */
	public $config=array();
	/**
	 * @var EForm main object
	 */
	protected $_form;
    protected $_coreButtonTypes=array(
        'htmlButton',       // a normal button generated using CHtml::htmlButton
        'htmlReset',        // a reset button generated using CHtml::htmlButton
        'htmlSubmit',       // a submit button generated using CHtml::htmlButton
        'submit',           // a submit button generated using CHtml::submitButton
        'button',           // a normal button generated using CHtml::button
        'image',            // an image button generated using CHtml::imageButton
        'reset',            // a reset button generated using CHtml::resetButton
        'link',             // a link button generated using CHtml::linkButton
    );
    private $_firstButton;
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
            $o=$this->getOwner();
            $this->config=array_merge($this->config,$this->getFormElements());
			$this->_form=new EForm($this->config,$o,null);
            if(!isset($this->id))
                $this->id=sprintf('%x',crc32(serialize(array_keys($this->_form->getElements()->toArray())).$o->scenario));
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
		return $this->getForm()->render();
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
		if(method_exists($model,'getFormElements'))
			$description=$model->getFormElements();
		else
		{
			$description=array();
			foreach($model->getAttributes() as $attribute=>$value)
				$description[$attribute]=array(
					'type'=>'text',
				);
		}
        $elements=array();
        $buttons=array();
        
        foreach($description as $element=>$config)
        {
            if(is_array($config))
            {
                if(!isset($config['type']))
                    $config['type']='text';

                if(preg_match('~^(button|element|input)\.(.+)$~i',$config['type'],$regs))
                {
                    $config['type']=$regs[2];
                    if(!strcasecmp($regs[1],'button'))
                        $buttons[$element]=$config;
                    else
                        $elements[$element]=$config;
                }elseif(in_array($config['type'],$this->_coreButtonTypes))
                {
                    $buttons[$element]=$config;
                }else
                    $elements[$element]=$config;
            }else
                $elements[$element]=$config;
        }
        
        if(empty($buttons))
        {
            $isBaseAr=($model instanceof CActiveRecord && in_array($model->scenario,array('insert','update','search')));
            $buttons=array(
                'submit'=>array(
                    'type'=>'submit',
                    'label'=>Yii::t('yiiext',$isBaseAr ? 'Save' : 'Submit'),
                    'on'=>$isBaseAr ? 'insert,update' : null,
                ),
                'reset'=>array(
                    'type'=>'reset',
                    'label'=>Yii::t('yiiext','Clear'),
                    'on'=>$isBaseAr ? 'insert,update' : null,
                ),
            );
            if($isBaseAr)
            {
                $buttons['search']=array(
                    'type'=>'submit',
                    'label'=>Yii::t('yiiext','Search'),
                    'on'=>'search'
                );
            }
        }
        reset($buttons);
        $this->_firstButton=key($buttons);
        return array(
            'elements'=>$elements,
            'buttons'=>$buttons,
        );
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
    public function submitted($button=null)
    {
        $form=$this->getForm();
        $button=isset($button) ? $button : $this->_firstButton;
        return $this->performAjax()&&$form->submitted($button);
    }
    /**
     * Performs Performs check for submission, then process ajax request,
     * load attributes into model and save it
     * @param string $button name of clicked button
     * @return boolean saving status
     */
    public function saved($button=null)
    {
        return $this->submitted($button)&&$this->getOwner()->save();
    }
    /**
     * Performs Performs check for submission, then process ajax request,
     * load attributes into model and validate it
     * @param string $button name of clicked button
     * @return boolean validation status
     */
    public function validated($button=null)
    {
        return $this->submitted($button)&&$this->getOwner()->validate();
    }
}
