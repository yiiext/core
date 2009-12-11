#summary Добавляет возможность удалять в корзину и восстанавливать модели

== Установка и настройка ==
----------------
==== Подготовка модели: ====
В модели должен быть выделен атрибут для статуса удаления.

==== Подключить поведение к модели: ====
{{{
function behaviors() {
    return array(
        'trash' => array(
            'class' => 'ext.CTrashBinBehavior.CTrashBinBehavior',
            // Имя столбца где хранится статус удаления (обязательное свойство)
            'trashFlagField' => 'trash',
            // Значение которое устанавливается при удалении в поле $trashFlagField
            // По умолчанию 1
            'removedFlag' => '1',
            // Значение которое устанавливается при восстановлении в поле $trashFlagField
            // По умолчанию 0
            'restoredFlag' => '0',
        )
    );
}
}}}

== Методы ==
-------
==== remove() ====
Удаляем модель в корзину

{{{
$user = User::model()->findByPk(1);
$user->remove();
}}}

==== restore() ====
Восстанавливаем модель из корзины

{{{
// Так как при поиске удаленные модели игнорируются,
// нужно на время поиска выключить поведение
User::model()->disableBehavior('trash');

$user = User::model()->findByPk(1);
$user->restore();

// Включаем снова поведение.
User::model()->enableBehavior('trash');
}}}

==== isRemoved() ====
Проверяем удалена ли модель.

{{{
User::model()->disableBehavior('trash');
$users = User::model()->findAll();
foreach ($users as $user) {
  echo $user->isRemoved() ? 'status=removed' : 'status=normal';
}
User::model()->enableBehavior('trash');
}}}

== Подсказка ==
-------
При включенном поведении при поиске игнорируются модели со статусом удаления, поэтому если нужно найти модели включая модели из корзины, нужно выключить на время поиска поведение.