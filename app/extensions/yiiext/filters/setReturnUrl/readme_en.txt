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
        array(
            'ESetReturnUrlFilter',
            // Use for spcified actions:
            // 'ESetReturnUrlFilter + index',
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