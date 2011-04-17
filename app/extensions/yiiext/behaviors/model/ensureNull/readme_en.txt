Ensure NULL behavior
====================

Ensures no empty AR property value is written to DB if property's default is `NULL`.
Useful if you want to be sure all empty values will be saved as nulls.

Installation and configuration
------------------------------

Copy to your application `extensions` directory.
Define `behaviors()` method in your ActiveRecord mode as follows:
~~~
[php]
function behaviors() {
    return array(
        'ensureNull' => array(
            'class' => 'ext.yiiext.behaviors.model.ensureNull.EEnsureNullBehavior',
            // Uncomment if you don't want to ensure nulls on update
            // 'useOnUpdate' => false,
        )
    );
}
~~~
