<?php
/**
 * EavTest
 */
class EavTest extends CDbTestCase {
    public $fixtures = array(
        'contacts' => 'Contact',
    );

    protected function setUp(){
        parent::setUp(); //return;

        Yii::app()->db->createCommand("TRUNCATE `contactattr`")->query();
        Yii::app()->db->createCommand("INSERT INTO `contactattr`(`entity`, `attribute`, `value`) VALUES ('1', 'phone', '+373 1');")->query();
        Yii::app()->db->createCommand("INSERT INTO `contactattr`(`entity`, `attribute`, `value`) VALUES ('1', 'phone', '+373 2');")->query();
        Yii::app()->db->createCommand("INSERT INTO `contactattr`(`entity`, `attribute`, `value`) VALUES ('1', 'skype', 'SlavaSkype');")->query();
        Yii::app()->db->createCommand("INSERT INTO `contactattr`(`entity`, `attribute`, `value`) VALUES ('2', 'skype', 'AlexandrSkype');")->query();
    }

    function testAfterDelete(){
        $this->setUp();
        $contact = Contact::model()->findByPk(1);
        $contact->delete();
        $count = Yii::app()->db->createCommand("SELECT COUNT(*) FROM `contactattr` WHERE `entity` = 1")->queryScalar();
        $this->assertEquals(0, $count);
    }

    public function testGetAllEavAttributes() {
        $this->setUp();
        $contact = Contact::model()->findByPk(1);
        $this->assertEquals(array(
            'phone' => array('+373 1', '+373 2'),
            'skype' => 'SlavaSkype',
        ), $contact->getEavAttributes());
    }

    public function testGetEavAttributes() {
        $this->setUp();
        $contact = Contact::model()->findByPk(1);
        $this->assertEquals(array(
            'phone' => array('+373 1', '+373 2'),
        ), $contact->getEavAttributes(array('phone')));
    }

    public function testGetEavAttribute() {
        $this->setUp();
        $contact = Contact::model()->findByPk(2);
        $this->assertEquals('AlexandrSkype', $contact->getEavAttribute('skype'));
    }

    public function testSetEavAttributes() {
        $this->setUp();
        $contact = Contact::model()->findByPk(2)->setEavAttributes(array('skype' => 'AS', 'phone' => '+7 1'))->save();

        $contact = Contact::model()->findByPk(2);
        $this->assertEquals(array('skype' => 'AS', 'phone' => '+7 1'), $contact->getEavAttributes(array('skype', 'phone')));
    }

    public function testSetEavAttribute() {
        $this->setUp();
        $contact = Contact::model()->findByPk(2)->setEavAttribute('skype', 'AS')->save();

        $contact = Contact::model()->findByPk(2);
        $this->assertEquals('AS', $contact->getEavAttribute('skype'));
    }

    public function testSetAndSaveEavAttribute() {
        $this->setUp();
        $contact = Contact::model()->findByPk(2)->setEavAttribute('skype', 'AS', TRUE);

        $contact = Contact::model()->findByPk(2);
        $this->assertEquals('AS', $contact->getEavAttribute('skype'));
    }

    function testDeleteAllEavAttributes(){
        $this->setUp();
        $contact = Contact::model()->findByPk(1)->deleteEavAttributes(NULL, TRUE);
        $count = Yii::app()->db->createCommand("SELECT COUNT(*) FROM `contactattr` WHERE `entity` = 1")->queryScalar();
        $this->assertEquals(0, $count);
      }

    function testDeleteEavAttributes(){
        $this->setUp();
        $contact = Contact::model()->findByPk(1);
        $contact->deleteEavAttributes(array('phone'), TRUE);
        $this->assertEquals('SlavaSkype', $contact->getEavAttribute('skype'));
        $this->assertNull($contact->getEavAttribute('phone'));
    }
    
    function testWithEavAttributes(){
        $this->setUp();

        // Все контакты
        $contactCount = Contact::model()->count();
        $this->assertEquals(2, $contactCount);

        // Ищем контакты имеющие заданный скайп
        $contacts = Contact::model()->withEavAttributes(array('skype' => 'SlavaSkype'))->findAll();
        $this->assertEquals(1, count($contacts));

        // Ищем контакты имеющие заданные скайп-аккаунты
        $contacts = Contact::model()->withEavAttributes(array('skype' => array('SlavaSkype', 'AlexandrSkype')))->findAll();
        $this->assertEquals(0, count($contacts));

        // Ищем контакты имеющие заданные телефоны
        $contacts = Contact::model()->withEavAttributes(array('phone' => array('+373 1', '+373 2')))->findAll();
        $this->assertEquals(1, count($contacts));

        // Ищем контакты имеющие заданный телефон и заданный скайп
        $attr = array(
            //'phone' => '+373 1',
            'phone' => '+373 2',
            //'skype' => 'AlexandrSkype',
            'skype' => 'SlavaSkype',
            );
        $contacts = Contact::model()->withEavAttributes($attr)->findAll();
        $this->assertEquals(1, count($contacts));

        // Ищем контакты имеющие телефон и скайп
        $contacts = Contact::model()->withEavAttributes(array('phone', 'skype'))->findAll();
        $this->assertEquals(1, count($contacts));

        // Ищем контакты имеющие все разрешенные атрибуты
        $contacts = Contact::model()->withEavAttributes()->findAll();
        $this->assertEquals(1, count($contacts));
    }

    function testWithEavAttributesCount(){
        $this->setUp();

        // Количество контактов с заданным скайпом
        $contactsCount = Contact::model()->withEavAttributes(array('skype' => 'SlavaSkype'))->count();
        $this->assertEquals(1, $contactsCount);

        // Количество контактов с заданными телефонами
        $contactsCount = Contact::model()->withEavAttributes(array('phone' => array('+373 1', '+373 2')))->count();
        $this->assertEquals(1, $contactsCount);

        // Количество контактов с телефонами
        // Запрос возвращает две стройки для одной модели т.к. у нее два телефона :(
        $contactsCount = Contact::model()->withEavAttributes(array('phone'));
        //var_dump($contactsCount->getCommandBuilder()->createCountCommand($contactsCount->getTableSchema(), $contactsCount->getDbCriteria())->text);
        $contactsCount = $contactsCount->count();
        $this->assertEquals(1, $contactsCount);
    }

}
/**
 * @bug Contact::model()->withEavAttributes(array('phone')); возвращает две стройки для одной модели т.к. у нее два телефона.
 * @bug При одинаковый полях в таблицах модели и атрибутов происходит колапс и ИД от атрибута присваивается модели.
 */