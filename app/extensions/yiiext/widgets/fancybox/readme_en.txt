Widget for jQuery Fancybox
===========================

Usage
-----
in a view `views/photos.php`:
~~~
[php]
$this->widget('ext.yiiext.widgets.fancybox.EFancyboxWidget',array(
	// Selector for generate fancybox.
	//'selector'=>'a[href$=\'.jpg\'],a[href$=\'.png\'],a[href$=\'.gif\']',
	// Enable "mouse-wheel" to navigate throught gallery items.
	// Defaults to false.
	// 'enableMouseWheel'=>false,
	// [fancybox options](http://fancybox.net/api/).
	'options'=>array(
		// 'padding'=>10,
		// 'margin'=>20,
		// 'enableEscapeButton'=>true,
		// 'onComplete'=>'js:function() {$("#fancybox-wrap").hover(function() {$("#fancybox-title").show();}, function() {$("#fancybox-title").hide();});}',
	),
));
~~~
