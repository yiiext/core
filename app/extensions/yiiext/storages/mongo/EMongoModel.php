<?php
//TODO: implement getters and setters
/**
 * EMongoModel
 *
 */
abstract class EMongoModel extends CModel {
    private $_id = null;
    private $collectionName = null;
    private $_attributes = array();

    abstract function getCollectionClass();

    /**
     * We use same attribute names as we actually do have
     *
     * @return array
     */
    function attributeNames(){
        return array_keys($this->_attributes);
    }

    /**
     * Creates Mongo object
     *
     * @param  $data
     * @return void
     */
    function __construct($data = array(), $collectionName = null) {
        if($collection) $this->setCollection($collection);

        if(!empty($data['_id'])){
            $this->setId($data['_id']);
            unset($data['_id']);
        }

        $this->_attributes = $data;        
    }

    /**
	 * PHP getter magic method.
	 * This method is overridden so that AR attributes can be accessed like properties.
	 * @param string property name
	 * @return mixed property value
	 * @see getAttribute
     *
     * //OK
	 */
	public function __get($name){
        if(property_exists($this,$name)) return $this->$name;
		return $this->getAttribute($name);
        // if we don't mind exception here, we can use following:
        //return parent::__get($name);
	}

    /**
	 * PHP setter magic method.
	 * This method is overridden so that AR attributes can be accessed like properties.
	 * @param string property name
	 * @param mixed property value
     *
     * //OK
	 */
	public function __set($name, $value){
        if(property_exists($this,$name)) $this->$name=$value;
        $this->setAttribute($name, $value);
	}

	/**
	 * Checks if a property value is null.
	 * This method overrides the parent implementation by checking
	 * if the named attribute is null or not.
	 * @param string the property name or the event name
	 * @return boolean whether the property value is null
     *
     * //OK
	 */
	public function __isset($name){
		if(isset($this->_attributes[$name])) return true;
        return parent::__isset($name);
	}

	/**
	 * Sets a component property to be null.
	 * This method overrides the parent implementation by clearing
	 * the specified attribute value.
	 * @param string the property name or the event name
     *
     * //OK
	 */
	public function __unset($name){
        if(isset($this->{$name})){
            unset($this->{$name});
        }
        else {
            unset($this->_attributes[$name]);
        }
    }

    /**
	 * Sets the named attribute value.
	 * You may also use $this->AttributeName to set the attribute value.
	 * @param string the attribute name
	 * @param mixed the attribute value.
	 * @return boolean whether the attribute exists and the assignment is conducted successfully
	 * @see hasAttribute
     *
     * //OK
	 */
	public function setAttribute($name,$value){
		$this->_attributes[$name]=$value;
	}

    /**
	 * Returns the named attribute value.
	 * If this is a new record and the attribute is not set before,
	 * the default column value will be returned.
	 * If this record is the result of a query and the attribute is not loaded,
	 * null will be returned.
	 * You may also use $this->AttributeName to obtain the attribute value.
	 * @param string the attribute name
	 * @return mixed the attribute value. Null if the attribute is not set or does not exist.
	 * @see hasAttribute
     *
     * //OK
	 */
	public function getAttribute($name){
		if(isset($this->_attributes[$name])) return $this->_attributes[$name];
	}

    public function toArray(){
        $array = $this->getAttributes();
        if($id = $this->getId()){
            $array['_id'] = $id;
        }
        return $array;
    }
   
    /**
     * @return string
     */
    public function getId(){
        return $this->_id;
    }

    /**
     * @param string $id
     * @return CMongoDocument
     */
    public function setId($id){
        $this->_id = $id;
        return $this;
    }

    /**
     * @return EMongoCollection
     */
    public function getCollection($collectionName = null){
        $collection = null;

        if($collectionName){
            $collection = Yii::app()->getComponent('mongo')->getCollection($collectionName);            
        }        

        if(!$collection && $this->getDefaultCollectionName()){
            $collection = Yii::app()->getComponent('mongo')->getCollection($this->getDefaultCollectionName());
        }

        return $collection;
    }

    /**
     * @param EMongoCollection $collection
     * @return CMongoDocument
     */
    public function setCollection(EMongoCollection $collection){
        $this->_collection = $collection;
        return $this;
    }

    /**
     * @return EMongoModel
     */
    public function save($collectionName = null){
        return $this->getCollection($collectionName)->save($this);
    }    

    public function getReference(){
        return $this->getCollection()->createReference($this);
    }    
}
