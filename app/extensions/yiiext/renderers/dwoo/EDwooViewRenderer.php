<?php
/**
 * Dwoo view renderer
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @link http://code.google.com/p/yiiext/
 * @link http://dwoo.org/
 *
 * @version 0.9.2
 */
class EDwooViewRenderer extends CApplicationComponent implements IViewRenderer {
    public $fileExtension='.tpl';
    public $filePermission=0755;
    public $pluginsDir = null;

    /**
     * @var Dwoo
     */
    private $dwoo;

    /**
     * Component initialization
     */
    function init(){
        Yii::import('application.vendors.*');

        // need this since Yii autoload handler raises an error if class is not found
        spl_autoload_unregister(array('YiiBase','autoload'));

        // registering Dwoo autoload handler
        require_once 'Dwoo/dwooAutoload.php';

        // adding back Yii autoload handler
        spl_autoload_register(array('YiiBase','autoload'));

        // compiled templates directory
        $compileDir = Yii::app()->getRuntimePath().'/dwoo/compiled/';

        // create compiled directory if not exists
        if(!file_exists($compileDir)){
            mkdir($compileDir, $this->filePermission, true);
        }

        // cached templates directory
        $cacheDir = Yii::app()->getRuntimePath().'/dwoo/cache/';

        // create compiled directory if not exists
        if(!file_exists($cacheDir)){
            mkdir($cacheDir, $this->filePermission, true);
        }

        $this->dwoo = new Dwoo($compileDir, $cacheDir);

        $loader = $this->dwoo->getLoader();

        // adding extension plugin directory
        $loader->addDirectory(Yii::getPathOfAlias('application.extensions.Dwoo.plugins'));

        // adding config plugin directory if specified
        if(!empty($this->pluginsDir)){
            $loader->addDirectory(Yii::getPathOfAlias($this->pluginsDir));
        }
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
        $data['Yii'] = Yii::app();
        $data["TIME"] = sprintf('%0.5f',Yii::getLogger()->getExecutionTime());
        $data["MEMORY"] = round(Yii::getLogger()->getMemoryUsage()/(1024*1024),2)." MB";

        // check if view file exists
        if(!is_file($sourceFile) || ($file=realpath($sourceFile))===false)
            throw new CException(Yii::t('yiiext','View file "{file}" does not exist.', array('{file}'=>$sourceFile)));

        //render or return
		if($return)
        	return $this->dwoo->get($sourceFile, $data);
		else
			$this->dwoo->get($sourceFile, $data, null, true);
	}
}
