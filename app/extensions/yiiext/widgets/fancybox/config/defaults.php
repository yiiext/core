<?php
/**
 * EFancyboxWidget default configs.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @package yiiext.gallery.fancybox
 * @see http://fancybox.net/api
 */
return array(
	'padding'				=> 10,								//Space between FancyBox wrapper and content
	'margin'				=> 20,								//Space between viewport and FancyBox wrapper
	'opacity'				=> FALSE,							//When true, transparency of content is changed for elastic transitions
	'modal'					=> FALSE,							//When true, 'overlayShow' is set to 'true' and 'hideOnOverlayClick', 'hideOnContentClick', 'enableEscapeButton', 'showCloseButton' are set to 'false'
	'cyclic'				=> FALSE,							//When true, galleries will be cyclic, allowing you to keep pressing next/back.
	'scrolling'				=> 'auto',							//Set the overflow CSS property to create or hide scrollbars. Can be set to 'auto', 'yes', or 'no'
	'width'					=> 560,								//Width for content types 'iframe' and 'swf'. Also set for inline content if 'autoDimensions' is set to 'false'
	'height'				=> 340,								//Height for content types 'iframe' and 'swf'. Also set for inline content if 'autoDimensions' is set to 'false'
	'autoScale'				=> TRUE,							//If true, FancyBox is scaled to fit in viewport
	'autoDimensions'		=> TRUE,							//For inline and ajax views, resizes the view to the element recieves. Make sure it has dimensions otherwise this will give unexpected results
	'centerOnScroll'		=> FALSE,							//When true, FancyBox is centered while scrolling page
	'ajax'					=> array(),							//Ajax options. Note: 'error' and 'success' will be overwritten by FancyBox
	'swf'					=> array('wmode' => 'transparent'),	//Flashvars to put on the swf object
	'hideOnOverlayClick'	=> TRUE,							//Toggle if clicking the overlay should close FancyBox
	'hideOnContentClick'	=> FALSE,							//Toggle if clicking the content should close FancyBox
	'overlayShow'			=> TRUE,							//Toggle overlay
	'overlayOpacity'		=> 0.3,								//Opacity of the overlay (from 0 to 1; default - 0.3)
	'overlayColor'			=> '#666',							//Color of the overlay
	'titleShow'				=> TRUE,							//Toggle title
	'titlePosition'			=> 'outside',						//The position of title. Can be set to 'outside', 'inside' or 'over'
	'transitionIn'			=> 'fade',							//The transition type. Can be set to 'elastic', 'fade' or 'none'
	'transitionOut'			=> 'fade',							//The transition type. Can be set to 'elastic', 'fade' or 'none'
	'speedIn'				=> 300,								//Speed of the fade and elastic transitions, in milliseconds
	'speedOut'				=> 300,								//Speed of the fade and elastic transitions, in milliseconds
	'changeSpeed'			=> 300,								//Speed of resizing when changing gallery items, in milliseconds
	'changeFade'			=> 'fast',							//Speed of the content fading while changing gallery items
	'easingIn'				=> 'swing',							//Easing used for elastic animations
	'easingOut'				=> 'swing',							//Easing used for elastic animations
	'showCloseButton'		=> TRUE,							//Toggle close button
	'showNavArrows'			=> TRUE,							//Toggle navigation arrows
	'enableEscapeButton'	=> TRUE,							//Toggle if pressing Esc button closes FancyBox

	'type'					=> NULL,							//Forces content type. Can be set to 'image', 'ajax', 'iframe', 'swf' or 'inline'
	'href'					=> NULL,							//Forces content source
	'title'					=> NULL,							//Forces title
	'content'				=> NULL,							//Forces content (can be any html data)
	'orig'					=> NULL,							//Sets object whos position and dimensions will be used by 'elastic' transition
	'index'					=> NULL,							//Custom start index of manually created gallery (since 1.3.1)

	/* JavaScripts vars */

	'titleFormat'			=> NULL,							//Callback to customize title area. You can set any html - custom image counter or even custom navigation

	'onStart'				=> NULL,							//Will be called right before attempting to load the content
	'onCancel'				=> NULL,							//Will be called after loading is canceled
	'onComplete'			=> NULL,							//Will be called once the content is displayed
	'onCleanup'				=> NULL,							//Will be called just before closing
	'onClosed'				=> NULL,							//Will be called once FancyBox is closed
);
