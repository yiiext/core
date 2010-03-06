<?php
class Book extends CActiveRecord implements IECartPosition {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    function getId(){
        return 'Book'.$this->id;
    }
    
    function getPrice(){
        return $this->price;
    }
}