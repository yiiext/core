<?php
/**
 * EMongoDB
 *
 * @version 0.1
 * @author Alexander Makarov <sam@rmcreative.ru>
 */
class EMongoDB extends CApplicationComponent {
    /**
     * @var string host:port 
     *
     * Correct syntax is:
     * mongodb://[username:password@]host1[:port1][,host2[:port2:],...]
     *
     * @example mongodb://localhost:27017
     */
    public $connectionString;    

    /**
	 * @var boolean whether the Mongo connection should be automatically established when
	 * the component is being initialized. Defaults to true. Note, this property is only
	 * effective when the EMongoDB object is used as an application component.
	 */
	public $autoConnect = true;

    /**
     * @var false|string false for non-persistent connection, string for persistent connection id to use
     */
    public $persistentConnection = false;

    /**
     * @var string name of the Mongo database to use
     */
    public $db = null;

    /**
     * @var Mongo
     */
    private $_mongo;

    private $_collections = array();

    public function __destruct(){
        if(!$this->persistentConnection){
            $this->close();
        }
    }

    /**
	 * Opens MongoDB connection if it is currently not open
	 * @throws CException if connection fails
	 */
	public function connect(){
		if($this->_mongo===null){
			try	{
				Yii::trace('Opening MongoDB connection', 'ext.MongoDb.EMongoDB');
				$this->_mongo=$this->getMongoInstance();
                if(!$this->autoConnect){
                    $this->_mongo->connect();
                }
			}
			catch(MongoConnectionException $e){                
				throw new CMongoDBException(Yii::t(
                        'yiiext',
                        'EMongoDB failed to open connection: {error}',
					    array('{error}' => $e->getMessage())
                ));
			}
		}
	}

	/**
	 * Closes the currently active Mongo connection.
	 * It does nothing if the connection is already closed.
	 */
	protected function close(){
        if($this->_mongo!==null){
            $this->_mongo->close();
            $this->_mongo=null;
            Yii::trace('Closing MongoDB connection', 'ext.MongoDb.EMongoDB');
        }
	}

	/**
	 * @return Mongo the Mongo instance
	 */
	public function getMongoInstance(){
        if($this->_mongo===null){
            if(empty($this->connectionString)){
				throw new CMongoDBException(Yii::t('yiiext', 'EMongoDB.connectionString cannot be empty.'));
            }
            $this->_mongo = new Mongo($this->connectionString, array(
                "connect" => $this->autoConnect,
                "persist" => $this->persistentConnection                
            ));
        }
		return $this->_mongo;
	}
    
    public function drop(){
        $this->getDb()->drop();
    }

    /**
     * Returns MongoDB instance
     *
     * @param $dbName
     * @param $className
     *
     * @throws CMongoDBException
     * @return EMongoCollection
     */
    public function getCollection($collectionName, $className = 'EMongoCollection'){
        if(!isset($this->_collections[$collectionName])){
            if(empty($this->db)){
                throw new EMongoException(Yii::t('yiiext', 'EMongoDB.db can not be empty.'));
            }

            $this->_collections[$collectionName] = new $className($collectionName, $this);            
        }

        return $this->_collections[$collectionName];
    }

    /**
     * @throws EMongoException
     * @param  $collectionName
     * @return MongoCollection
     */
    public function getRawCollection($collectionName){
        try {
            return $this->_mongo->selectCollection($collectionName);
        }
        catch (InvalidArgumentException $e){
            throw new EMongoException(Yii::t('yiiext', 'EMongoDB.db is invalid.'));
        }
    }

    /**
     * @return MongoDB
     */
    public function getDb(){
        return $this->getMongoInstance()->selectDB($this->db);
    }

    /**
     * Execute low level mongo command
     *
     * @param array $data
     * @return mixed
     */
    public function command($data){
        return $this->getMongoInstance()->selectDB($this->db)->command($data);
    }
}