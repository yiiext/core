<?php
/**
 * CMongoQuery
 */
class CMongoQuery {
    /**
     * @var CMongoCriteria
     */
    protected $criteria;

    /**
     * @var CMongoCollection
     */
    protected $collection;

    function __construct(CMongoCollection $collection, CMongoCriteria $criteria){
        $this->criteria = $criteria===null ? new CMongoCriteria() : $criteria;
    }

    /**
     * @return CMongoModel
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
     * @return CMongoCursor
     */
    function find(){
        $cursor = $this->collection->getRawCollection()->find($this->criteria->query, $this->criteria->fields);
        return new CMongoCursor($this->collection, $cursor, $this->criteria);
    }

    /**
     * @param int $limit
     * @return CMongoCollection
     */
    function limit($limit){
        $this->criteria->limit = $limit;
        return $this;
    }

    /**
     * @param  $offset
     * @return CMongoCollection
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
