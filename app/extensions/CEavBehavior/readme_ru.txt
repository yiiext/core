#summary Добавляет модели возможность работать с eav-моделью данных

== Установка и настройка ==
----------------
==== Создать таблицу для храниениея EAV-аттрибутов. ====
SQL для таблицы:
{{{
CREATE TABLE IF NOT EXISTS `eavAttr` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity` bigint(20) unsigned NOT NULL,
  `attribute` varchar(250) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ikEntity` (`entity`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
}}}

==== Подключить поведение к модели: ====
{{{
function behaviors() {
    return array(
        'eavAttr' => array(
            'class' => 'ext.CEavBehavior.CEavBehavior',
            // Имя таблицы для аттрибутов (обязательное свойство)
            'tableName' => 'eavAttr',
            // Имя столбца где хранится ид объекта.
            // По умолчанию 'entity'
            'entityField' => 'entity',
            // Имя столбца где хранится имя атрибута.
            // По умолчанию 'attribute'
            'attributeField' => 'attribute',
            // Имя столбца где хранится значение атрибута.
            // По умолчанию 'value'
            'valueField' => 'value',
            // Имя внешнего ключа модели.
            // По умолчанию берется primaryKey из свойста таблицы
            'modelTableFk' => primaryKey,
            // Массив разрешенных атрибутов, если не указано разрешаются любые атрибуты
            // По умолчанию не указано.
            'safeAttributes' => array(),
            // Префикс для атрибутов. Если для разных моделей используется одна таблица.
            // По умолчанию не указано.
            'attributesPrefix' => '',
        )
    );
}
}}}

== Методы ==
-------
==== getEavAttributes($attributes) ====
Возвращает массив значений атрибутов, индексированные именем атрибута

{{{
$user = User::model()->findByPk(1);
$user->getEavAttributes(array('attribute1', 'attribute2'));
}}}

==== getEavAttribute($attribute) ====
Возвращает значение атрибута

{{{
$user = User::model()->findByPk(1);
$user->getEavAttribute('attribute1');
}}}

==== setEavAttribute($attribute, $value) ====
Устанавливает значение атрибута

{{{
$user = User::model()->findByPk(1);
$user->setEavAttribute('attribute1', 'value1');
}}}

==== checkEavAttribute($attribute) ====
Проверяет если атрибут разрешен

{{{
$user = User::model()->findByPk(1);
echo $user->checkEavAttribute('attribute1') ? 'Yes' : 'No';
}}}

==== findByEavAttribute($attributes, $condition = '', $params = array()) ====
Ищет первую модель с заданными атрибутами

{{{
$users = User::model()->findByEavAttributes(array(
   'seacrh_attribute1' => array('value1', 'value2'),
   'seacrh_attrubute2' => 'value3'
));
echo $users->name;
}}}

==== findAllByEavAttributes($attributes, $condition = '', $params = array()) ====
Ищет модели с заданными атрибутами

{{{
$users = User::model()->findAllByEavAttributes(array(
   'seacrh_attribute1' => array('value1', 'value2'),
   'seacrh_attrubute2' => 'value3'
));
echo $users->count();
}}}