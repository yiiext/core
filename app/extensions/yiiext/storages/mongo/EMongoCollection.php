<?php
/**
 * EMongoCollection
 */
abstract class EMongoCollection {
    const UNIQUE = 'unique';
    const DROP_DUPLICATES = 'dropdups';

    // update if exists, create if not
    const UPDATE_UPSERT = 'upsert';

    // update all matching documents
    const UPDATE_ALL = 'multiple';

    /**
     * @var MongoCollection
     */
    private $collection = null;

    /**
     * @abstract
     * @return string
     */
    abstract function getCollectionName();

    /**
     * @abstract
     * @return string
     */
    abstract function getModelClass();

    /**
     * @return void
     */
    function __construct(){        
        $this->getDB()->connect();        

        try {
            $this->collection = $this->getDB()->getRawCollection($this->getCollectionName());
        }
        catch (InvalidArgumentException $e){
            throw new EMongoException(Yii::t('yiiext', 'EMongoDB.db is invalid.'));
        }
    }

    /**
     * @param mixed $fields
     * @return void
     */
    public function ensureIndex($fields, $option = null){
        if (!is_array($fields)) $fields = array($fields => 1);

        $params = array();
        if (self::UNIQUE == $option || self::DROP_DUPLICATES == $option){
            $params[self::UNIQUE] = true;
        }

        if(self::DROP_DUPLICATES == $option){
            $params[self::DROP_DUPLICATES] = true;
        }

        return $this->collection->ensureIndex($fields, $params);
    }

    public function ensureUniqueIndex(array $fields, $dropDuplicates = false) {
        $this->ensureIndex($fields, $dropDuplicates ? self::DROP_DUPLICATES : self::UNIQUE);
        return $this;
    }

    public function dropIndex($fields){
        return $this->collection->deleteIndex($fields);
    }

    public function dropIndexes() {
        return $this->collection->deleteIndexes();
    }

    public function getIndexInfo(){
        return $this->collection->getIndexInfo();
    }

    /**
     * Saves object or document to this collection
     * id will be
     *
     * @param EMongoModel $object
     * @return EMongoModel
     */
    public function save(EMongoModel $object){
        $data = $object->toArray();

        // if saving existing document from another collection
        if($object->getId()!=null && $object->getCollection!=$this){
            // generate new id
            unset($data['_id']);            
        }

        $this->collection->save($data);
        
        return new EMongoModel($data, $this);
    }

    public function update($query, $values, $options) {
        return $this->collection->update($query, $values, $options);
    }    

    public function drop(){
        return $this->collection->drop();
    }

    /**
     * @return EMongoQuery
     */
    public function query(EMongoCriteria $criteria = null){
        return new EMongoQuery($this, $criteria);
    }    

    /**
     * @param CMongoObject[] $objects
     * @return EMongoCollection
     */
    function batchInsert($objects){
        $data = array();
        foreach($objects as $object){
            $data[] = $object->toArray();
        }
        $this->collection->batchInsert($data);        
        return $this;
    }

    function createReference(EMongoModel $model){
        return $this->collection->createDBRef($model->toArray());
    }

    function getByReference($reference, $className = 'EMongoModel'){
        //TODO: get collection from reference to get class
        echo '<pre>'.print_r($reference, true).'</pre>';
        die();
        return new $this->collection->getDBRef();
    }    

    public function clear(){
		return $this->collection->remove(array());
	}	

    public function removeById($id){
        $this->remove(array('_id' => new MongoId($id), true));
        return $this;        
    }

    public function getDistinctValues($field, $query){
        $values = $this->db->command(array("distinct" => $this->getName(), "key" => $field));
        return $values['values'];
    }

    public function remove($criteria, $justOne = false){
		$this->collection->remove($criteria, $justOne = false);
        return $this;
	}

    /**
     * @return EMongoDB
     */
    public function getDB(){
        return Yii::app()->getComponent('mongo');
    }

    /**
     * @return MongoCollection
     */
    public function getRawCollection(){
        return $this->collection;
    }
}
