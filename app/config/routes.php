<?php
/**
 * Routes
 */
return array(
	'index.html' => 'site/index',
	'extensions/<name:.+>/index.html' => 'site/repo',
	'extensions/<name:.+>/readme.<lang:\w\w>.html' => 'site/repoReadme',
	'extensions/<name:.+>/downloads.html' => 'site/repoDownloads',
);
