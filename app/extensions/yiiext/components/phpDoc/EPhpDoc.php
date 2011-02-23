<?php
/**
 * EPhpDoc class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
/**
 * PhpDoc Reader
 * 
 * The map of phpDoc documentation.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.components.phpDoc
 * @link http://phpdoc.org/
 * 
 * @var string $title the documentation title.
 * @var string $description the documentation description.
 * @var boolean $abstract
 * @var string $access the access, public or private.
 * @var string $author the author. Example: 'author name <author@email>'.
 * @var string $copyright
 * @var string $deprecated
 * @var string $deprec alias for deprecated.
 * @var string $example the path to example.
 * @var string $exception Javadoc-compatible, use as needed.
 * @var string $global the data for a global variable. Format: 'type $globalvarname' or 'type description'.
 * @var boolean $ignore
 * @var string $internal the private information for advanced developers only.
 * @var string $param the data for a function or method param. Format: 'type [$varname] description'.
 * @var string $return the data for a function or method returns. Format: 'type description'.
 * @var string $link the URL.
 * @var string $name
 * @var string $magic phpdoc.de compatibility.
 * @var string $package the package name.
 * @var string $see the name of another element that can be documented, produces a link to it in the documentation.
 * @var string $since the version or a date.
 * @var boolean $static
 * @var string $staticvar the data for a static variable. Format: 'type description'.
 * @var string $subpackage the sub package name, groupings inside of a project.
 * @var string $throws Javadoc-compatible, use as needed.
 * @var string $todo phpdoc.de compatibility.
 * @var string $var the data for a class variable. Format: 'type description'.
 * @var string $version the version.
 */
class EPhpDoc extends CMap
{
	/**
	* @param Reflector $class
	* @return CMap
	*/
	public function __construct($class)
	{
		$data=array('title'=>NULL,'description'=>NULL);
		if($class instanceof Reflector&&method_exists($class,'getDocComment'))
		{
			// Get the comment.
			if(preg_match('#^/\*\*(.*)\*/#s',$class->getDocComment(),$lines)!==false)
			{
				$lines=trim($lines[1]);
				// Get all the lines and strip the * from the first character.
				if(preg_match_all('#^\s*\*(.*)#m',$lines,$lines)!==false)
				{
			    	$description=array();
				    foreach($lines[1] as $line)
				    {
	    				// Parse the line.
	    				$line=trim($line);
					    if(!empty($line)&&strpos($line,'@')===0)
					    {
					    	// Get the parameter name and value.
					    	$param=explode(' ',substr($line,1),2);
					    	$data[$param[0]]=isset($param[1])?$param[1]:true;
					    }
					    else if(count($data)<=2)
					    {
					    	$description[]=$line;
        					// Store the first line in the title.
					        if(!empty($line)&&empty($data['title']))
					        {
					        	$data['title']=$line;
							}
					    }
					    // Store the line before params in the description.
					    $data['description']=trim(implode(PHP_EOL,$description));
				    }
				}
			}
		}
		parent::__construct($data,true);
	}
}
