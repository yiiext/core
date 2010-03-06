<?php
/**
 * CMongoIterator
 *
 * Iterates over query results, wraps result rows into objects of class specified
 * in EMongoCollection::documentClass
 *
 */
class EMongoCursor implements Countable, Iterator, ArrayAccess, SeekableIterator {
    /**
     * @var MongoCursor
     */
    private $cursor;

    /**
     * @var CachingIterator
     */
    private $cachedCursor = null;

    private $_array = array();

    private $documentClass;

    /**
     * @var EMongoCollection
     */
    private $collection;

    function __construct(EMongoCollection $collection, MongoCursor $cursor, EMongoCriteria $criteria){
        $this->collection = $collection;
        $this->cursor = $cursor;

        $this->documentClass = $collection->getDocumentClass();

        // init cursor with criteria
        $this->cursor->limit($criteria->limit);
        $this->cursor->skip($criteria->offset);
        $this->cursor->sort($criteria->order);
    }

    public function seek($position){
		try {
			$this->cursor->skip($position);
			$this->cachedCursor = null;
		} catch(MongoCursorException $e) {
			$this->rewind();
			while ($position-- > 0) $this->next();
		}
	}

    public function offsetExists($offset) {
		return $this->getCachedCursor()->offsetExists($offset);
	}
    
	public function offsetGet($offset) {
		if (count($this->_array) <= $offset) {
			$p_count = 1 + floor($offset - count($this->_array)) / 4;
			$it = new LimitIterator($this, count($this->_array), $p_count * 4);
			$this->_array = array_merge($this->_array, iterator_to_array($it, false));
		}

		return $this->_array[$offset];
	}
	public function offsetSet($offset, $value) {
		throw new EMongoException('EMongoCursor is read-only.');
	}
	public function offsetUnset($offset) {
		throw new EMongoException('EMongoCursor is read-only.');
	}

    public function count() {
		return $this->cursor->count();
	}

	public function current() {
        return new $this->documentClass($this->getCachedCursor()->current(), $this->collection);
	}
    
 	public function key() {
 		return $this->cachedCursor()->key();
 	}

 	public function next() {
 		$this->cachedCursor()->next();
 	}

 	public function rewind() {
 		$this->cachedCursor()->rewind();
 	}

	public function valid() {
		return $this->cachedCursor()->valid();
	}

    private function getCachedCursor(){
		if (!$this->cachedCursor){
            $this->cachedCursor = new CachingIterator($this->cursor);
        }

    	return $this->cachedCursor;
    }
}
