<?php
/**
 * Routes
 */
return array(
	'extensions/<name:.+>/index.html' => 'site/repo',
	'extensions/<name:.+>/readme.<lang:\w\w>.html' => 'site/repoReadme',
	'extensions/<name:.+>/downloads.html' => 'site/repoDownloads',
	'extensions/<category:.+>_by_<user_sort:.+>.html' => 'site/index',
	'extensions/<category:.+>.html' => 'site/index',
	'index_by_<user_sort:.+>.html' => 'site/index',
	'<action:\w+>.html' => 'site/<action>',
);
