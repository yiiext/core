<?php
/**
 * EBreadCrumbsComponent class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
/**
 * BreadCrumbs Component displays a list of links indicating the position of the current page in the whole website.
 *
 * For example, breadcrumbs like "Home > Sample Post > Edit" means the user is viewing an edit page
 * for the "Sample Post". He can click on "Sample Post" to view that page, or he can click on "Home"
 * to return to the homepage.
 *
 * To use EBreadCrumbsComponent, one usually needs to add component in application. For example,
 *
 * <pre>
 * 'components'=>array(
 *     'breadCrumbs'=>array(
 *         'class'=>'ext.yiiext.components.breadCrumbs.EBreadCrumbsComponent',
 *         // {@link CBreadcrumbs} widget options.
 *         'widget'=>array(
 *             'separator'=>' &rsaquo; ',
 *         ),
 *     ),
 * ),
 * </pre>
 *
 * After component configured, add the links. For example,
 *
 * <pre>
 * Yii::app()->breadCrumbs['Sample Post']=array('post/view','id'=>12);
 * Yii::app()->breadCrumbs[]='Edit';
 * // or
 * Yii::app()->breadCrumbs->mergeWith(array(
 *     'Sample Post'=>array('post/view','id'=>12),
 *     'Edit',
 * ));
 * </pre>
 *
 * And finally, render breadcrumbs. Is better to be placed in a layout view.
 *
 * <pre>
 * Yii::app()->breadCrumbs->render();
 * </pre>
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.components.breadCrumbs
 * @see CBreadcrumbs
 */
class EBreadCrumbsComponent extends CMap implements IApplicationComponent
{
	/**
	 * @var array the behaviors that should be attached to this component.
	 * The behaviors will be attached to the component when {@link init} is called.
	 * Please refer to {@link CModel::behaviors} on how to specify the value of this property.
	 * @since 1.0.2
	 */
	public $behaviors=array();

	private $_initialized=false;
	private $_widgetOptions=array('class'=>'zii.widgets.CBreadcrumbs');
	private $_widget;

	/**
	 * Initializes the application component.
	 * This method is required by {@link IApplicationComponent} and is invoked by application.
	 * If you override this method, make sure to call the parent implementation
	 * so that the application component can be marked as initialized.
	 */
	public function init()
	{
		$this->attachBehaviors($this->behaviors);
		$this->_initialized=true;
	}
	/**
	 * @return boolean whether this application component has been initialized (ie, {@link init()} is invoked).
	 */
	public function getIsInitialized()
	{
		return $this->_initialized;
	}
	/**
	 * @return void
	 */
	public function render()
	{
		$this->getWidget()->links=$this->toArray();
		Yii::app()->getController()->endWidget('breadcrumbs');
	}
	/**
	 * Configure breadscrumbs widget.
	 * @param array|string $value
	 */
	public function setWidget($value)
	{
		$this->_widgetOptions=$value;
	}
	/**
	 * @return CWidget
	 */
	public function getWidget()
	{
		if($this->_widget===null)
		{
			$this->_widget=$this->createWidget();
		}
		return $this->_widget;
	}
	/**
	 * @return CWidget
	 */
	private function createWidget()
	{
		$options=is_array($this->_widgetOptions)?$this->_widgetOptions:array('class'=>$this->_widgetOptions);
		if(!empty($options['class']))
		{
			$class=$options['class'];
			unset($options['class']);
		}
		else
		{
			$class='zii.widgets.CBreadcrumbs';
		}
		return Yii::app()->getController()->beginWidget($class,$options);
	}
}
