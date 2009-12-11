#summary Allows model to work with custom fields on the fly (EAV pattern)

== Installing and configuring ==
----------------
==== Create a table that will store EAV-attributes ====
SQL dump:
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

==== Attach behaviour to your model ====
{{{
function behaviors() {
    return array(
        'eavAttr' => array(
            'class' => 'ext.CEavBehavior.CEavBehavior',
            // Table that stores attributes (required)
            'tableName' => 'eavAttr',
            // model id column
            // Default is 'entity'
            'entityField' => 'entity',
            // attribute name column
            // Default is 'attribute'
            'attributeField' => 'attribute',
            // attribute value column
            // Default is 'value'
            'valueField' => 'value',
            // Model FK name
            // By default taken from primaryKey
            'modelTableFk' => primaryKey,
            // Array of allowed attributes
            // All attributes are allowed if not specified
            // Empty by default
            'safeAttributes' => array(),
            // Attribute prefix. Useful when storing attributes for multiple models in a single table
            // Empty by default
            'attributesPrefix' => '',
        )
    );
}
}}}

== Methods ==
-------
==== getEavAttributes($attributes) ====
Get attribute values indexed by attributes name.

{{{
$user = User::model()->findByPk(1);
$user->getEavAttributes(array('attribute1', 'attribute2'));
}}}

==== getEavAttribute($attribute) ====
Get attribute value.

{{{
$user = User::model()->findByPk(1);
$user->getEavAttribute('attribute1');
}}}

==== setEavAttribute($attribute, $value) ====
Set attribute value.

{{{
$user = User::model()->findByPk(1);
$user->setEavAttribute('attribute1', 'value1');
}}}

==== checkEavAttribute($attribute) ====
Check if attribute name is valid.

{{{
$user = User::model()->findByPk(1);
echo $user->checkEavAttribute('attribute1') ? 'Yes' : 'No';
}}}

==== findByEavAttribute($attributes, $condition = '', $params = array()) ====
Find a single model by attribute set.

{{{
$users = User::model()->findByEavAttributes(array(
   'seacrh_attribute1' => array('value1', 'value2'),
   'seacrh_attrubute2' => 'value3'
));
echo $users->name;
}}}

==== findAllByEavAttributes($attributes, $condition = '', $params = array()) ====
Find all models by attribute set.

{{{
$users = User::model()->findAllByEavAttributes(array(
   'seacrh_attribute1' => array('value1', 'value2'),
   'seacrh_attrubute2' => 'value3'
));
echo $users->count();
}}}