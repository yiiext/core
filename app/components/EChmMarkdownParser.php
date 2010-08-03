<?php
class EChmMarkdownParser extends CMarkdownParser {
	public function __construct() {
		$this->span_gamut += array(
				"doGuideLinks" => 0,
		);

		parent::__construct();
	}

	public function doGuideLinks($text) {
		return preg_replace_callback('~\[(.*?)\]\(/doc/guide/([^/]+)\)~', array($this, 'formatGuideLinks'), $text);
	}

	public function formatGuideLinks($match) {
		$text=$match[1];
		@list($url,$anchor)=explode('#',$match[2],2);
		$url=$url.'.html'.($anchor ? '#'.$anchor : '');
		return $this->hashPart("<a href=\"{$url}\">{$text}</a>");
	}
}