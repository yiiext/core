EBreadCrumbsComponent
===============

BreadCrumbs компонент создающий навигационную цепочку, представляющий собой путь по сайту от его «корня» до текущей страницы, на которой находится пользователь.

Например: "Главная страница → Тестовый пост → Редактирование"

Использование
-------------
Настраиваем компонент приложения:
~~~
[php]
// main.php - конфиг приложения
'components'=>array(
    'breadCrumbs'=>array(
        'class'=>'ext.yiiext.components.breadCrumbs.EBreadCrumbsComponent',
        // {@link CBreadcrumbs} widget options.
        'widget'=>array(
            'separator'=>' &rsaquo; ',
        ),
    ),
),
~~~

Добавляем "крошки", например в представлении:
~~~
[php]
Yii::app()->breadCrumbs['Sample Post']=array('post/view', 'id'=>12),
Yii::app()->breadCrumbs[]='Edit',
~~~

И наконец показываем цепочку:
~~~
[php]
// Оптимально генерировать цепочку в шаблоне.
Yii::app()->breadCrumbs->render();
~~~
