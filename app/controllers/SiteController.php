<?php


class SiteController extends CController
{
    public $layout = '//layouts/website';

	/**
	 * @var YiiextGithubApi current api
	 */
	public $api = null;

	/**
	 * @var string current repos github url
	 */
    public $repoUrl = '';

    public $categories = array(
	    'all'=>'/^.*$/',
		'behaviors'=>'/^.*-behavior$/i',
	    'components'=>'/^.*-component$/i',
		'filters'=>'/^.*-filter$/i',
	    'modules'=>'/^.*-module$/i',
	    'renderers'=>'/^.*-renderer$/i',
	    'widgets'=>'/^(.*-widget|chart)$/i',
	    'others'=>'!'
    );

	public function init()
	{
		parent::init();
		$this->api = new YiiextGithubApi();
	}

    public function actionIndex($category='all')
    {
		if (!isset($this->categories[$category])) {
			throw new CHttpException(404, 'category does not exist.');
		}
		Yii::app()->clientScript->enableJavaScript = false;
		Yii::app()->language = 'en';

		$c = $this->categories;
		$repos = array_filter($this->api->getRepos(), function($var) use ($category, $c) {
		if ($c[$category] == '!') {
			foreach($c as $cat) {
				if ($cat != '/^.*$/' && $cat != '!' && preg_match($cat, $var->name)) {
					return false;
				}
			}
			return true;
		}
		return preg_match($c[$category], $var->name);
	});

	$this->render('index', array(
		'category'=>$category,
		'repos' => new CArrayDataProvider($repos, array(
			'id'=>'user',
			'sort'=>array(
			    'attributes'=>array(
			        'name', 'created_at', 'pushed_at', 'watchers'
				),
				'defaultOrder'=>'watchers DESC',
			),
			'pagination'=>false,
		)),
        ));
    }

	public function actionRepo($name)
	{
		$repo = $this->api->getRepo($name);
		$this->repoUrl = $repo->html_url;

		// redirect to readme if there is one
		$readmes = $this->api->getRepoReadmeFilenames($name);
		ksort($readmes);
		foreach($readmes as $lang => $readme) {
			$this->redirect(array('site/repoReadme', 'name'=>$name, 'lang'=>$lang));
		}

		$this->render('repo', array(
			'repo' => $repo,
			'changelog' => $this->getRepoChangelog($name),
			'readmeFiles' => $readmes,
		));
	}

	public function getRepoChangelog($name)
	{
		$github = new ESimpleGithub();
		$changelogs = $this->api->getRepoChangelogFilenames($name);
		if (isset($changelogs['en'])) {
			$changelog = $github->getFile('yiiext', $name, 'master', $changelogs['en']);
		}
		else {
			return '';
		}

		$markdownParser = new CMarkdownParser();
		$changelog = $markdownParser->transform($changelog);

		return str_replace(array('<h2>', '</h2>'), array('<h3>', '</h3>'),
			   str_replace(array('<h3>', '</h3>'), array('<h4>', '</h4>'), $changelog));
	}

	public function actionRepoReadme($name, $lang)
	{
		$repo = $this->api->getRepo($name);
		$this->repoUrl = $repo->html_url;

		$github = new ESimpleGithub();
		$readmes = $this->api->getRepoReadmeFilenames($name);
		if (isset($readmes[$lang])) {
			$readme = $github->getFile('yiiext', $name, 'master', $readmes[$lang]);
		}
		else {
			throw new CHttpException(404, 'Readme is not available in this language.');
		}

		$markdownParser = new CMarkdownParser();
		$readme = $markdownParser->transform(//preg_replace('/\[(php|sql|sh)\]/i', '[\1 showLineNumbers=1]',
			str_replace(
				array("~~~php"    , "~~~ php"     , "~~~sh"      , "~~~sql"      , "~~~html"      , "~~~ html"),
				array("~~~\n[php]", "~~~\n[php]\n", "~~~\n[sh]\n", "~~~\n[sql]\n", "~~~\n[html]\n", "~~~\n[html]\n"),
				str_replace('```', '~~~', $readme)
			//)
		));

		$this->render('repo', array(
			'repo' => $repo,
			'readmeFiles' => $this->api->getRepoReadmeFilenames($name),
			'readme' => $readme,
			'changelog' => $this->getRepoChangelog($name),
		));
	}


    public function actionFaq()
    {
        $this->render('faq');
    }

    public function actionContact()
    {
        $this->render('contact');
    }

	public function actionTestGithub()
	{
		//ob_end_flush();
		echo "hallo";
		Yii::import('ext.yiiext.components.github-api.models.*');
		Yii::import('ext.yiiext.components.github-api.EGithub');

//		$gists = EGithubGist::model()->findAll();
//		foreach($gists as $gist) {
//			echo "gist: " . $gist->id . '<br/>';
//		}

		$gist = EGithubGist::model()->find(/*1166792, */590895);
		echo "single gist: <pre>" . print_r($gist, true) . '</pre><br/>';

	}
}
