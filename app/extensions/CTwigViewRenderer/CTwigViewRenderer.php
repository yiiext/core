<?php
/**
 * Twig renderer for Yii
 *
 * Download and extract all Twig files from
 * fabpot-Twig-______.zip\fabpot-Twig-______\lib\Twig\
 * under protected/vendors/Twig.
 *
 * Add the following to your config file 'components' section:
 *
 * 'viewRenderer'=>array(
 *     'class'=>'application.extensions.Twig.CTwigViewRenderer',
 *     //'fileExtension' => '.tpl',
 *  ),
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @link http://www.yiiframework.com/
 * @link http://www.twig-project.org/
 *
 * @version 0.9
 */
class CTwigViewRenderer extends CApplicationComponent implements IViewRenderer {
    public $fileExtension='.html';

    private $twig;

    /**
     * Component initialization
     */
    function init(){
        Yii::import('application.vendors.*');

        // need this since Yii autoload handler raises an error if class is not found
        spl_autoload_unregister(array('YiiBase','autoload'));

        // registering twig autoload handler
        require_once 'Twig/Autoloader.php';
        Twig_Autoloader::register();

        // adding back Yii autoload handler
        spl_autoload_register(array('YiiBase','autoload'));

        require_once Yii::getPathOfAlias('application.extensions.Twig').'/Twig_Loader_File.php';

        // setting cache path to application runtime directory
        $cache_path = Yii::app()->getRuntimePath().DIRECTORY_SEPARATOR.'views_twig'.DIRECTORY_SEPARATOR;

        // here we are using custom twig loader (see Twig_Loader_File.php)
        $loader = new Twig_Loader_File($cache_path);
        $this->twig = new Twig_Environment($loader);
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
        // current controller properties will be accessible as {{this.property}}
        $data['this'] = $context;

        // check if view file exists
        if(!is_file($sourceFile) || ($file=realpath($sourceFile))===false)
            throw new CException(Yii::t('yii','View file "{file}" does not exist.', array('{file}'=>$sourceFile)));

        $template = $this->twig->loadTemplate($sourceFile);

        return $template->render($data);
	}
}