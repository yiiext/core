<?php

Yii::import('system.cli.commands.MigrateCommand');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'EDbMigration.php');

/**
 * EMigrateCommand manages the database migrations.
 *
 * This class is an extension to yiis db migration command.
 *
 * It adds the following features:
 *  - module support
 *    you can create migrations in different modules
 *    so you are able to disable modules and also having their
 *    database tables removed/never set up
 *    yiic migrate down 1000 --module=examplemodule
 *
 *  - module dependencies (planned, not yet implemented)
 *
 * @link http://www.yiiframework.com/doc/guide/1.1/en/database.migration
 * @author Carsten Brandt <mail@cebe.cc>
 * @version 0.1.0
 */
class EMigrateCommand extends MigrateCommand
{
	/**
	 * @var array list of all modules
	 * array(
	 *      'modname' => 'application.modules.modname.db.migrations',
	 * )
	 */
	public $modulePaths = array();

	/**
	 * @var array list of disabled modules
	 * array(
	 *      'examplemodule1',
	 *      'examplemodule2',
	 *      ...
	 * )
	 */
	public $disabledModules = array();

	/**
	 * @var string|null the current module(s) to use for current command (comma separated list)
	 * defaults to null which means all modules are used
	 * examples:
	 * --module=core
	 * --module=core,user,admin
	 */
	public $module;

	/**
	 * @var string the application core is handled as a module named 'core' by default
	 */
	public $applicationModuleName = 'core';

	protected $migrationModuleMap = array();

	private $_currentAction;

	/**
	 * prepare paths before any action
	 *
	 * @param $action
	 * @param $params
	 * @return bool
	 */
	public function beforeAction($action, $params)
	{
		$this->_currentAction = $action;

		Yii::import($this->migrationPath . '.*');
		if ($return = parent::beforeAction($action, $params)) {

			echo "extended with EMigrateCommand by cebe <mail@cebe.cc>\n\n";

			if ($action == 'create' && !is_null($this->module)) {
				$this->usageError('create command can not be called with --module parameter!');
			}

			// add a pseudo-module 'core'
			$this->modulePaths[$this->applicationModuleName] = $this->migrationPath;

			// remove disabled modules
			$disabledModules = array();
			foreach($this->modulePaths as $module => $pathAlias) {
				if (in_array($module, $this->disabledModules)) {
					unset($this->modulePaths[$module]);
				}
			}
			if (!empty($disabledModules)) {
				echo "The following modules are disabled: " . implode(', ', $disabledModules) . "\n\n";
			}
print_r($this->module);
			// only add modules that are desired by command
			$modules = false;
			if (!is_null($this->module)) {
				$modules = explode(',', $this->module);
			}
			print_r($modules);

			// @todo: error if specified module does not exist

			// initialize modules
			foreach($this->modulePaths as $module => $pathAlias) {
				if ($modules === false || in_array($module, $modules)) {
					// nothing to do for application core module
					if ($module == $this->applicationModuleName) {
						continue;
					}
					$path = Yii::getPathOfAlias($pathAlias);
					if($path === false || !is_dir($path))
						die('Error: The migration directory does not exist: ' . $pathAlias . "\n");
					$this->modulePaths[$module] = $path;
					Yii::import($pathAlias . '.*');
				} else {
					unset($this->modulePaths[$module]);
				}
			}
		}
		return $return;
	}


	public function actionCreate($args)
	{
		// if module is given adjust path
		if(count($args)==2) {
			$this->migrationPath = $this->modulePaths[$args[0]];
			$args = array($args[1]);
		} else {
			$this->migrationPath = $this->modulePaths[$this->applicationModuleName];
		}

		parent::actionCreate($args);
	}

/*	protected function migrateUp($class)
	{
		if($class===self::BASE_MIGRATION)
			return;

		echo "*** applying $class\n";
		$start=microtime(true);
		$migration=$this->instantiateMigration($class);
		$time=microtime(true)-$start;
		if($migration->up()!==false)
		{
			$this->getDbConnection()->createCommand()->insert($this->migrationTable, array(
				'version'=>$class,
				'apply_time'=>time(),
			));
			echo "*** applied $class (time: ".sprintf("%.3f",$time)."s)\n\n";
		}
		else
		{
			echo "*** failed to apply $class (time: ".sprintf("%.3f",$time)."s)\n\n";
			return false;
		}
	}

	protected function migrateDown($class)
	{
		if($class===self::BASE_MIGRATION)
			return;

		echo "*** reverting $class\n";
		$start=microtime(true);
		$migration=$this->instantiateMigration($class);
		$time=microtime(true)-$start;
		if($migration->down()!==false)
		{
			$db=$this->getDbConnection();
			$db->createCommand()->delete($this->migrationTable, $db->quoteColumnName('version').'=:version', array(':version'=>$class));
			echo "*** reverted $class (time: ".sprintf("%.3f",$time)."s)\n\n";
		}
		else
		{
			echo "*** failed to revert $class (time: ".sprintf("%.3f",$time)."s)\n\n";
			return false;
		}
	}*/

	protected function instantiateMigration($class)
	{
		if ($class instanceof EDbMigration) {
			return $class;
		}
		require_once($class.'.php');
		$migration=new $class;
		$migration->setDbConnection($this->getDbConnection());
		return $migration;
	}

	protected function getNewMigrations()
	{
		$modules = $this->modulePaths;

		$migrations = array();
		foreach($modules as $module => $path) {
			$this->migrationPath = $path;
			foreach(parent::getNewMigrations() as $migration) {
				$migrations[$migration] = new $migration();
				$migrations[$migration]->module = $module;
			}
		}

		ksort($migrations);
		return $migrations;
	}

	protected function getMigrationHistory($limit)
	{
		$history = parent::getMigrationHistory($limit);
		if ($this->_currentAction != 'history') {
			// need to remove module information
			$fixedHistory = array();
			foreach($history as $migration => $time) {
				if (($pos = strpos($migration, ': ')) !== false) {
					$fixedHistory[substr($migration, $pos + 2)] = $time;
				}
			}
			return $fixedHistory;
		}
		return $history;
	}

	public function getHelp()
	{
		return parent::getHelp() . <<<EOD

EXTENDED USAGE EXAMPLES (with modules)
  for every action except create you can specify the modules to use
  with the parameter --module=<modulenames>
  where <modulenames> is a comma seperated list of module names (or a single name)

 * yiic migrate create modulename create_user_table
   Creates a new migration named 'create_user_table' in module 'modulename'.

  all other commands work exactly as described above.

EOD;
	}

}
