<?php
//TODO: support http://www.mongodb.org/display/DOCS/Advanced+Queries
/** 
 * EMongoCriteria represents a query criteria, such as fields, ordering by, limit/offset.
 */
class EMongoCriteria {
    const SORT_ASC = 1;
    const SORT_DESC = -1;
    const NO_LIMIT = -1;
    const OFFSET_BEGIN = -1;

	/**
	 * @var fields array Array of the columns being selected. 
	 * All fields by default.
	 */
	public $fields = array();
	
	/**
	 * @var query array
	 * For example, array('status' => 'published', 'price' => array('$gt' => 50)).
	 */
	public $query = array();
    
	/**
	 * @var integer maximum number of records to be returned. If less than 0, it means no limit.
	 */
	public $limit = self::NO_LIMIT;

	/**
	 * @var integer zero-based offset from where the records are to be returned. If less than 0, it means starting from the beginning.
	 */
	public $offset = self::OFFSET_BEGIN;
    
	/**
	 * @var array|string how to sort the query results
	 */
	public $order = '';
		
	/**
	 * Constructor.
	 * @param array criteria initial property values (indexed by property name)
	 */
	public function __construct($data=array()){
		foreach($data as $name => $value) {
            $this->{$name} = $value;
        }
	}

    private function wrapId($id){
        return new MongoId($id);
    }

    private function wrapIdsInConstraint($constraintPart){
                    
    }

    /**
     * @param array $field
     * @param mixed $constraint
     * @return EMongoCriteria
     */
    function addConstraint($field, $constraint){
        if($field=='_id'){
            if(!is_array($constraint)){
                // equals
                $value = $this->wrapId($value);
            }
            else {                
                array_map(array($this, 'wrapIdsInConstraint'), $constraint);
            }
        }
        return $this;
    }

    function equals($field, $value){
        $this->query[] = array($field => $value);
        return $this;
    }

    function lowerThan($field, $value){
        $this->query[] = array($field => array('$lt' => $value));
        return $this;
    }

    function greaterThan($field, $value){
        $this->query[] = array($field => array('$gt' => $value));
        return $this;
    }

    function between($field, $from, $to){
        $this->query[] = array($field => array('$lt' => $from, '$gt' => $to));
        return $this;
    }

    function notEquals($field, $value){
        $this->query[] = array($field => array('$ne' => $value));
        return $this;
    }

    function in($field, array $values){
        $this->query[] = array($field => array('$in' => $values));
        return $this;        
    }

    function notIn($field, array $values){
        $this->query[] = array($field => array('$nin' => $values));
        return $this;
    }

	/**
	 * Merges with another criteria.
     * 
	 * In general, the merging makes the resulting criteria more restrictive.
     *
	 * Also, the criteria passed as the parameter takes precedence in case
	 * two options cannot be merged (e.g. LIMIT, OFFSET).
     * 
	 * @param $criteria EMongoCriteria|array the criteria to be merged with.
	 */
	public function mergeWith($criteria){		
		if(is_array($criteria)) $criteria=new self($criteria);
        
		if($this->fields!==$criteria->fields){
            // if existing fields are empty, use new fields
			if(empty($this->fields)){
                $this->select=$criteria->select;
            }                
			else {
				$this->select=array_merge($this->select, array_diff($criteria->select, $this->select));
			}
		}

		if($this->query!==$criteria->query){
			if(empty($this->query)){
				$this->query=$criteria->query;
            }
			else{
                $this->query=array_merge($this->query, array_diff($criteria->query, $this->query));
            }
		}
		
		if($criteria->limit>0) {
			$this->limit=$criteria->limit;
        }

		if($criteria->offset>=0) {
			$this->offset=$criteria->offset;
        }

		if(!empty($criteria->order)){
			if(empty($this->order)){
                $this->order=$criteria->order;
            }
			else {
                $this->order=$criteria->order.', '.$this->order;   
            }
		}		
	}
}
