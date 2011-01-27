<?php
/**
 * Quicky view renderer
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @link http://code.google.com/p/yiiext/
 * @link http://code.google.com/p/quicky/
 *
 * @version 0.9.2
 */
class EQuickyViewRenderer extends CApplicationComponent implements IViewRenderer {
    public $fileExtension='.tpl';
    public $filePermission=0755;
    public $pluginsDir = null;
    public $configDir = null;

    private $quicky;

    /**
     * Component initialization
     */
    function init(){
        Yii::import('application.vendors.*');
        require_once('Quicky/Quicky.class.php');

        $this->quicky = new Quicky();

        $this->quicky->template_dir = '';
        $compileDir = Yii::app()->getRuntimePath().'/quicky/compiled/';

        // create compiled directory if not exists
        if(!file_exists($compileDir)){
            mkdir($compileDir, $this->filePermission, true);
        }

        $this->quicky->compile_dir = $compileDir;


        $this->quicky->plugins_dir[] = Yii::getPathOfAlias('application.extensions.Quicky.plugins');
        if(!empty($this->pluginsDir)){
            $this->quicky->plugins_dir[] = Yii::getPathOfAlias($this->pluginsDir);
        }

        if(!empty($this->configDir)){
            $this->quicky->config_dir = Yii::getPathOfAlias($this->configDir);
        }

        $this->quicky->assign("TIME",sprintf('%0.5f',Yii::getLogger()->getExecutionTime()));
        $this->quicky->assign("MEMORY",round(Yii::getLogger()->getMemoryUsage()/(1024*1024),2)." MB");
        $this->quicky->assign('Yii', Yii::app());
    }

    /**
	 * Renders a view file.
	 * This method is required by {@link IViewRenderer}.
	 * @param CBaseController the controller or widget who is rendering the view file.
	 * @param string the view file path
	 * @param mixed the data to be passed to the view
	 * @param boolean whether the rendering result should be returned
	 * @return mixed the rendering result, or null if the rendering result is not needed.
	 */
	public function renderFile($context,$sourceFile,$data,$return) {
        // current controller properties will be accessible as {this.property}
        $data['this'] = $context;

        // check if view file exists
        if(!is_file($sourceFile) || ($file=realpath($sourceFile))===false)
            throw new CException(Yii::t('yiiext','View file "{file}" does not exist.', array('{file}'=>$sourceFile)));

        //assign data
        foreach($data as $name => $value){
            $this->quicky->assign($name, $value);
        }

        //render or return
		if($return)
        	return $this->quicky->fetch($sourceFile);
		else
			$this->quicky->display($sourceFile);
	}
}
