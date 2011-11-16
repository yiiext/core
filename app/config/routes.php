<?php
/**
 * Routes
 */
return array(
	'extensions/<name:.+>/index.html' => 'site/repo',
	'extensions/<name:.+>/readme.<lang:\w\w>.html' => 'site/repoReadme',
	'extensions/<name:.+>/downloads.html' => 'site/repoDownloads',
	'<action:\w+>.html' => 'site/<action>',
);
