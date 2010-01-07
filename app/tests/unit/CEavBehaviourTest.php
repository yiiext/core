<?php
/**
 * CEavBehaviourTest
 */
class CEavBehaviourTest extends CDbTestCase {
    public $fixtures = array(
        'contacts' => 'Contact',
    );
    public $statusMessage = 'OK';

    function setUp(){
        parent::setUp();
        Yii::app()->db->createCommand("truncate contactattr")->query();
    }
    
    private function prepareAttr(){
        $this->setUp();

        $contact = Contact::model()->findByPk(1);
        $contact->setEavAttributes(array(
            'phone' => array('+373 97 300587', '+373 97 587300'),
            'skype' => 'SlavaSkype',
        ))->save();

        $contact = Contact::model()->findByPk(2);
        $contact->setEavAttribute('skype', 'AlexandrSkype')->save();
    }

    function testGetEavAttribute(){
        $this->prepareAttr();
        
        $contact = Contact::model()->findByPk(2);
        $this->assertEquals('AlexandrSkype', $contact->getEavAttribute('skype'));
    }

    function testGetEavAttributes(){
        $this->prepareAttr();

        $contact = Contact::model()->findByPk(1);
        $this->assertEquals(array(
            'phone' => array('+373 97 300587', '+373 97 587300'),
            'skype' => 'SlavaSkype',
        ), $contact->getEavAttributes());
    }

    function testGetSpecifiedEavAttributes(){
        $this->prepareAttr();

        $contact = Contact::model()->findByPk(1);
        $this->assertEquals(array(
            'skype' => 'SlavaSkype',
        ), $contact->getEavAttributes(array('skype')));
    }

    function testSetEavAttribute(){
        $this->prepareAttr();

        $contact = Contact::model()->findByPk(2);
        $contact->setEavAttribute('phone', '+7 95 4567890');
        $contact->setEavAttribute('phone', '+7 95 0004567');
        $contact->save();

        $contact = Contact::model()->findByPk(2);
        $this->assertEquals('+7 95 0004567', $contact->getEavAttribute('phone'));
    }

    function testSetEavAttributes(){
        $this->prepareAttr();

        $contact = Contact::model()->findByPk(1);
        $contact->setEavAttributes(array(
            'phone' => '+373 97 587300',
            'skype' => 'SS',
        ))->save();

        $contact = Contact::model()->findByPk(1);
        $this->assertEquals(array(
            'phone' => '+373 97 587300',
            'skype' => 'SS',
        ), $contact->getEavAttributes());
    }

    function testCheckEavAttributes(){
        $this->prepareAttr();

        $contact = Contact::model()->findByPk(1);
        $this->assertTrue($contact->checkEavAttribute('skype'));
        $this->assertFalse($contact->checkEavAttribute('fax'));
    }

    function testAfterDelete(){
        $this->prepareAttr();

        $contact = Contact::model()->findByPk(1);
        $contact->delete();
        $count = Yii::app()->db->createCommand("select count(*) from `contactattr` where `entity` = 1")->queryScalar();
        $this->assertEquals(0, $count);
    }

    function testDeleteAllEavAttributes(){
        $this->prepareAttr();

        $contact = Contact::model()->findByPk(1)->deleteEavAttributes()->save();
        $count = Yii::app()->db->createCommand("select count(*) from `contactattr` where `entity` = 1")->queryScalar();
        $this->assertEquals(0, $count);
    }
    
    function testDeleteEavAttributes(){
        $this->prepareAttr();

        $contact = Contact::model()->findByPk(1)->deleteEavAttributes('phone')->save();
        $contact = Contact::model()->findByPk(1);
        $this->assertEquals('SlavaSkype', $contact->getEavAttribute('skype'));
        $this->assertNull($contact->getEavAttribute('phone'));
    }

    function testWithEavAttributes(){
        $this->prepareAttr();

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
        $contacts = Contact::model()->withEavAttributes(array('phone' => array('+373 97 300587', '+373 97 587300')))->findAll();
        $this->assertEquals(1, count($contacts));

        // Ищем контакты имеющие заданный телефон и заданный скайп
        $attr = array(
            //'phone' => '+373 97 300587',
            'phone' => '+373 97 587300',
            //'skype' => 'AlexandrSkype',
            'skype' => 'SlavaSkype',
        );
        $contacts = Contact::model()->withEavAttributes($attr)->findAll();
        $this->assertEquals(1, count($contacts));

        // Ищем контакты имеющие телефон и скайп
        $contacts = Contact::model()->withEavAttributes(array('phone', 'skype'))->findAll();
        $this->assertEquals(1, count($contacts));

            // Ищем контакты имеющие разрешенные атрибуты
            $contacts = Contact::model()->withEavAttributes()->findAll();
            $this->assertEquals(1, count($contacts));

            // Ищем контакты имеющие разрешенные атрибуты
            $contact = Contact::model()->findByPk(2);
            $contact->setEavAttribute('phone', '+7 95 0102030')->save();
            $contacts = Contact::model()->withEavAttributes()->findAll();
            $this->assertEquals(2, count($contacts));

    }

    function testWithEavAttributesCount(){
        $this->prepareAttr();

        // Количество контактов с заданным скайпом
        $contactsCount = Contact::model()->withEavAttributes(array('skype' => 'SlavaSkype'))->count();
        $this->assertEquals(1, $contactsCount);
        // Количество контактов с заданными телефонами
        $contactsCount = Contact::model()->withEavAttributes(array('phone' => array('+373 97 300587', '+373 97 587300')))->count();
        $this->assertEquals(1, $contactsCount);

        // Количество контактов с телефонами
        // Запрос возвращает две стройки для одной модели т.к. у нее два телефона :(        
        $contactsCount = Contact::model()->withEavAttributes(array('phone'));
        //var_dump($contactsCount->getCommandBuilder()->createCountCommand($contactsCount->getTableSchema(), $contactsCount->getDbCriteria())->text);
        $contactsCount = $contactsCount->count();
        $this->assertEquals(1, $contactsCount);
    }
    
}

