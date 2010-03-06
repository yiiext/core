<?php
/**
 * EMongoQuery
 */
class EMongoQuery {
    /**
     * @var EMongoCriteria
     */
    protected $criteria;

    /**
     * @var EMongoCollection
     */
    protected $collection;

    function __construct(EMongoCollection $collection, EMongoCriteria $criteria){
        $this->criteria = $criteria===null ? new EMongoCriteria() : $criteria;
    }

    /**
     * @return EMongoModel
     */
    function findOne(){
        $data = $this->collection->getRawCollection()->findOne($this->criteria->query, $this->criteria->fields);

        $modelClass = $this->collection->getModelClass();
        return new $modelClass($data);
    }

    /**
     * @return int
     */
    public function count(){
        return $this->collection->getRawCollection()->count($this->criteria->query);
    }

    /**
     * @return EMongoCursor
     */
    function find(){
        $cursor = $this->collection->getRawCollection()->find($this->criteria->query, $this->criteria->fields);
        return new EMongoCursor($this->collection, $cursor, $this->criteria);
    }

    /**
     * @param int $limit
     * @return EMongoCollection
     */
    function limit($limit){
        $this->criteria->limit = $limit;
        return $this;
    }

    /**
     * @param  $offset
     * @return EMongoCollection
     */
    function offset($offset){
        $this->criteria->offset = $offset;
        return $this;
    }

    function order($order){
        $this->criteria->order = $order;
        return $this;
    }

    function fields(){

    }    

    function group($keys, $initial, $reduce, $condition){

    }
}
