<?php

Yii::import('ext.yiiext.components.github-api.ESimpleGithub');

/**
 *
 */
class YiiextGithubApi extends ESimpleGithub
{
	public $coreRepos = array(
        'core',
        'yiiext.github.com',
        'yii',
		'extension-template',
    );

	protected function cachedRequest($url, $expectedStatus=200, $forceRequest=false)
	{
		if (($data = Yii::app()->cache->get($url)) === false || is_null($data) || $forceRequest) {
			$github = new ESimpleGithub();
			list($status, $data) = $github->request($url);
			if ($status != $expectedStatus) {
				throw new CHttpException(500, 'Github Request status code is not as expected. ' . $url);
			}
			Yii::app()->cache->set($url, $data, 12 * 3600);
		}
		return $data;
	}

	public function getRepos()
	{
		$repos = $this->cachedRequest('/orgs/yiiext/repos');
		foreach($repos as $id => $repo) {
			if (in_array($repo->name, $this->coreRepos)) {
				unset($repos[$id]);
			}
		}
		return $repos;
	}

	public function getRepoCount()
	{
		return count($this->getRepos());
	}

	public function getRepo($name)
	{
		if (!preg_match('/[A-z-]+/', $name)) {
			throw new CHttpException(404, 'Repo does not exist');
		}
		return $this->cachedRequest('/repos/yiiext/' . $name);
	}

	public function getRepoContributors($name)
	{
		return $this->cachedRequest('/repos/yiiext/' . $name . '/contributors');
	}

	public function getRepoTags($name)
	{
		return $this->cachedRequest('/repos/yiiext/' . $name . '/tags');
	}

	protected function getRepoTree($name)
	{
		$repo = $this->getRepo($name);
		// ignore empty repos
		if ($repo->size <= 0) {
			return array();
		}
		$master = $this->cachedRequest('/repos/yiiext/' . $name . '/git/refs/heads/' . $repo->master_branch);
		if ($master[0]->object->type != 'commit') {
			throw new CException('master branch of repo "' . $name . '" does not point to a commit.');
		}
		$commit = $this->cachedRequest('/repos/yiiext/' . $name . '/commits/' . $master[0]->object->sha);
		$tree = $this->cachedRequest('/repos/yiiext/' . $name . '/git/trees/' . $commit->commit->tree->sha);

		return $tree->tree;
	}

	public function getRepoReadmeFilenames($name)
	{
		$tree = $this->getRepoTree($name);

		$files = array();
		foreach($tree as $fileItem) {
			$m = array();
			if ($fileItem->mode != '120000' && strtolower($fileItem->path) == 'readme.md') {
				$files['en'] = $fileItem->path;
			}
			if (preg_match('/readme_(\w\w)\.(txt|md)/i', $fileItem->path, $m)) {
				$files[strtolower($m[1])] = $fileItem->path;
			}
		}
		return $files;
	}

	public function getRepoChangelogFilenames($name)
	{
		$tree = $this->getRepoTree($name);

		$files = array();
		foreach($tree as $fileItem) {
			$m = array();
			if (preg_match('/changelog\.(txt|md)/i', $fileItem->path, $m)) {
				$files['en'] = $fileItem->path;
			}
		}
		return $files;
	}

	public function getRepoWatchers($name)
	{
		return $this->cachedRequest('/repos/yiiext/' . $name . '/watchers');
	}

}
