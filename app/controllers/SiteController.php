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

	public function init()
	{
		parent::init();
		$this->api = new YiiextGithubApi();
	}

    public function actionIndex()
    {
        $this->render('index', array(
	        'repos' => $this->api->getRepos(),
        ));
    }

	public function actionRepo($name)
	{
		$repo = $this->api->getRepo($name);
		$this->repoUrl = $repo->html_url;
		$this->render('repo', array(
			'repo' => $repo,
			'tags' => $this->api->getRepoTags($name),
			'readmeFiles' => $this->api->getRepoReadmeFilenames($name),
		));
	}

	public function actionRepoReadme($name, $lang)
	{
		$repo = $this->api->getRepo($name);

		$github = new ESimpleGithub();
		$readmes = $this->api->getRepoReadmeFilenames($name);
		if (isset($readmes[$lang])) {
			$readme = $github->getFile('yiiext', $name, 'master', $readmes[$lang]);
		}
		else {
			throw new CHttpException(404, 'Readme is not available in this language.');
		}

		$markdownParser = new CMarkdownParser();
		$readme = $markdownParser->transform($readme);

		$this->render('repo', array(
			'repo' => $repo,
			'tags' => $this->api->getRepoTags($name),
			'readmeFiles' => $this->api->getRepoReadmeFilenames($name),
			'readme' => $readme
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