SetReturnUrl Filter
===================

Keeps current URL in session for all or specified controller actions so we can
return to it if needed.


Installing and configuring
--------------------------
Unpack into your application `extensions` directory.

Configure application (`config/main.php`):
~~~
[php]
return array(
    'import'=>array(
        // …
        'ext.yiiext.filters.setReturnUrl.ESetReturnUrlFilter',
    ),
    // …
);
~~~

In controller implement `filters()` method:
~~~
[php]
function filters() {
    return array(
    	'accessControl',
    	…
        array(
            'ESetReturnUrlFilter',
            // Use for spcified actions (index and view):
            // 'ESetReturnUrlFilter + index, view',
        ),
    );
}
~~~

Usage
-----
~~~
[php]
$this->redirect(Yii::app()->user->returnUrl);
~~~