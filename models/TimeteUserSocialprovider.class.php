<?php
require_once __DIR__ . '/../apis/db2php/Db2PhpEntity.class.php';
require_once __DIR__ . '/../apis/db2php/Db2PhpEntityBase.class.php';
require_once __DIR__ . '/../apis/db2php/Db2PhpEntityModificationTracking.class.php';
require_once __DIR__ . '/../apis/db2php/DFCInterface.class.php';
require_once __DIR__ . '/../apis/db2php/DFCAggregate.class.php';
require_once __DIR__ . '/../apis/db2php/DFC.class.php';
require_once __DIR__ . '/../apis/db2php/DSC.class.php';


/**
 * 
 *
 * @version 1.107
 * @package entity
 */
class TimeteUserSocialprovider extends Db2PhpEntityBase implements Db2PhpEntityModificationTracking {
	private static $CLASS_NAME='TimeteUserSocialprovider';
	const SQL_IDENTIFIER_QUOTE='`';
	const SQL_TABLE_NAME='timete_user_socialprovider';
	const SQL_INSERT='INSERT INTO `timete_user_socialprovider` (`user_id`,`oauth_uid`,`oauth_provider`,`oauth_token`,`oauth_token_secret`,`status`,`social_event_sync`,`lastFetch`,`fetchFailCount`,`lastStatusID`,`id`) VALUES (?,?,?,?,?,?,?,?,?,?,?)';
	const SQL_INSERT_AUTOINCREMENT='INSERT INTO `timete_user_socialprovider` (`user_id`,`oauth_uid`,`oauth_provider`,`oauth_token`,`oauth_token_secret`,`status`,`social_event_sync`,`lastFetch`,`fetchFailCount`,`lastStatusID`) VALUES (?,?,?,?,?,?,?,?,?,?)';
	const SQL_UPDATE='UPDATE `timete_user_socialprovider` SET `user_id`=?,`oauth_uid`=?,`oauth_provider`=?,`oauth_token`=?,`oauth_token_secret`=?,`status`=?,`social_event_sync`=?,`lastFetch`=?,`fetchFailCount`=?,`lastStatusID`=?,`id`=? WHERE `id`=?';
	const SQL_SELECT_PK='SELECT * FROM `timete_user_socialprovider` WHERE `id`=?';
	const SQL_DELETE_PK='DELETE FROM `timete_user_socialprovider` WHERE `id`=?';
	const FIELD_USER_ID=-1230580558;
	const FIELD_OAUTH_UID=857516171;
	const FIELD_OAUTH_PROVIDER=-404386154;
	const FIELD_OAUTH_TOKEN=-561415308;
	const FIELD_OAUTH_TOKEN_SECRET=324650459;
	const FIELD_STATUS=180947183;
	const FIELD_SOCIAL_EVENT_SYNC=-2073858027;
	const FIELD_LASTFETCH=178682791;
	const FIELD_FETCHFAILCOUNT=1365328628;
	const FIELD_LASTSTATUSID=-1538823040;
	const FIELD_ID=298929304;
	private static $PRIMARY_KEYS=array(self::FIELD_ID);
	private static $AUTOINCREMENT_FIELDS=array(self::FIELD_ID);
	private static $FIELD_NAMES=array(
		self::FIELD_USER_ID=>'user_id',
		self::FIELD_OAUTH_UID=>'oauth_uid',
		self::FIELD_OAUTH_PROVIDER=>'oauth_provider',
		self::FIELD_OAUTH_TOKEN=>'oauth_token',
		self::FIELD_OAUTH_TOKEN_SECRET=>'oauth_token_secret',
		self::FIELD_STATUS=>'status',
		self::FIELD_SOCIAL_EVENT_SYNC=>'social_event_sync',
		self::FIELD_LASTFETCH=>'lastFetch',
		self::FIELD_FETCHFAILCOUNT=>'fetchFailCount',
		self::FIELD_LASTSTATUSID=>'lastStatusID',
		self::FIELD_ID=>'id');
	private static $PROPERTY_NAMES=array(
		self::FIELD_USER_ID=>'userId',
		self::FIELD_OAUTH_UID=>'oauthUid',
		self::FIELD_OAUTH_PROVIDER=>'oauthProvider',
		self::FIELD_OAUTH_TOKEN=>'oauthToken',
		self::FIELD_OAUTH_TOKEN_SECRET=>'oauthTokenSecret',
		self::FIELD_STATUS=>'status',
		self::FIELD_SOCIAL_EVENT_SYNC=>'socialEventSync',
		self::FIELD_LASTFETCH=>'lastFetch',
		self::FIELD_FETCHFAILCOUNT=>'fetchFailCount',
		self::FIELD_LASTSTATUSID=>'lastStatusID',
		self::FIELD_ID=>'id');
	private static $PROPERTY_TYPES=array(
		self::FIELD_USER_ID=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_OAUTH_UID=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_OAUTH_PROVIDER=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_OAUTH_TOKEN=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_OAUTH_TOKEN_SECRET=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_STATUS=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_SOCIAL_EVENT_SYNC=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_LASTFETCH=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_FETCHFAILCOUNT=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_LASTSTATUSID=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_ID=>Db2PhpEntity::PHP_TYPE_INT);
	private static $FIELD_TYPES=array(
		self::FIELD_USER_ID=>array(Db2PhpEntity::JDBC_TYPE_INTEGER,10,0,false),
		self::FIELD_OAUTH_UID=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,200,0,false),
		self::FIELD_OAUTH_PROVIDER=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,50,0,false),
		self::FIELD_OAUTH_TOKEN=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,2000,0,false),
		self::FIELD_OAUTH_TOKEN_SECRET=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,255,0,true),
		self::FIELD_STATUS=>array(Db2PhpEntity::JDBC_TYPE_INTEGER,10,0,false),
		self::FIELD_SOCIAL_EVENT_SYNC=>array(Db2PhpEntity::JDBC_TYPE_TINYINT,3,0,false),
		self::FIELD_LASTFETCH=>array(Db2PhpEntity::JDBC_TYPE_INTEGER,10,0,true),
		self::FIELD_FETCHFAILCOUNT=>array(Db2PhpEntity::JDBC_TYPE_TINYINT,3,0,true),
		self::FIELD_LASTSTATUSID=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,50,0,true),
		self::FIELD_ID=>array(Db2PhpEntity::JDBC_TYPE_INTEGER,10,0,false));
	private static $DEFAULT_VALUES=array(
		self::FIELD_USER_ID=>0,
		self::FIELD_OAUTH_UID=>'',
		self::FIELD_OAUTH_PROVIDER=>'',
		self::FIELD_OAUTH_TOKEN=>'',
		self::FIELD_OAUTH_TOKEN_SECRET=>null,
		self::FIELD_STATUS=>0,
		self::FIELD_SOCIAL_EVENT_SYNC=>0,
		self::FIELD_LASTFETCH=>null,
		self::FIELD_FETCHFAILCOUNT=>null,
		self::FIELD_LASTSTATUSID=>null,
		self::FIELD_ID=>null);
	private $userId;
	private $oauthUid;
	private $oauthProvider;
	private $oauthToken;
	private $oauthTokenSecret;
	private $status;
	private $socialEventSync;
	private $lastFetch;
	private $fetchFailCount;
	private $lastStatusID;
	private $id;

	/**
	 * set value for user_id 
	 *
	 * type:INT,size:10,default:null,index
	 *
	 * @param mixed $userId
	 * @return TimeteUserSocialprovider
	 */
	public function &setUserId($userId) {
		$this->notifyChanged(self::FIELD_USER_ID,$this->userId,$userId);
		$this->userId=$userId;
		return $this;
	}

	/**
	 * get value for user_id 
	 *
	 * type:INT,size:10,default:null,index
	 *
	 * @return mixed
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * set value for oauth_uid 
	 *
	 * type:VARCHAR,size:200,default:null,index
	 *
	 * @param mixed $oauthUid
	 * @return TimeteUserSocialprovider
	 */
	public function &setOauthUid($oauthUid) {
		$this->notifyChanged(self::FIELD_OAUTH_UID,$this->oauthUid,$oauthUid);
		$this->oauthUid=$oauthUid;
		return $this;
	}

	/**
	 * get value for oauth_uid 
	 *
	 * type:VARCHAR,size:200,default:null,index
	 *
	 * @return mixed
	 */
	public function getOauthUid() {
		return $this->oauthUid;
	}

	/**
	 * set value for oauth_provider 
	 *
	 * type:VARCHAR,size:50,default:null,index
	 *
	 * @param mixed $oauthProvider
	 * @return TimeteUserSocialprovider
	 */
	public function &setOauthProvider($oauthProvider) {
		$this->notifyChanged(self::FIELD_OAUTH_PROVIDER,$this->oauthProvider,$oauthProvider);
		$this->oauthProvider=$oauthProvider;
		return $this;
	}

	/**
	 * get value for oauth_provider 
	 *
	 * type:VARCHAR,size:50,default:null,index
	 *
	 * @return mixed
	 */
	public function getOauthProvider() {
		return $this->oauthProvider;
	}

	/**
	 * set value for oauth_token 
	 *
	 * type:VARCHAR,size:2000,default:null
	 *
	 * @param mixed $oauthToken
	 * @return TimeteUserSocialprovider
	 */
	public function &setOauthToken($oauthToken) {
		$this->notifyChanged(self::FIELD_OAUTH_TOKEN,$this->oauthToken,$oauthToken);
		$this->oauthToken=$oauthToken;
		return $this;
	}

	/**
	 * get value for oauth_token 
	 *
	 * type:VARCHAR,size:2000,default:null
	 *
	 * @return mixed
	 */
	public function getOauthToken() {
		return $this->oauthToken;
	}

	/**
	 * set value for oauth_token_secret 
	 *
	 * type:VARCHAR,size:255,default:null,nullable
	 *
	 * @param mixed $oauthTokenSecret
	 * @return TimeteUserSocialprovider
	 */
	public function &setOauthTokenSecret($oauthTokenSecret) {
		$this->notifyChanged(self::FIELD_OAUTH_TOKEN_SECRET,$this->oauthTokenSecret,$oauthTokenSecret);
		$this->oauthTokenSecret=$oauthTokenSecret;
		return $this;
	}

	/**
	 * get value for oauth_token_secret 
	 *
	 * type:VARCHAR,size:255,default:null,nullable
	 *
	 * @return mixed
	 */
	public function getOauthTokenSecret() {
		return $this->oauthTokenSecret;
	}

	/**
	 * set value for status 
	 *
	 * type:INT,size:10,default:null
	 *
	 * @param mixed $status
	 * @return TimeteUserSocialprovider
	 */
	public function &setStatus($status) {
		$this->notifyChanged(self::FIELD_STATUS,$this->status,$status);
		$this->status=$status;
		return $this;
	}

	/**
	 * get value for status 
	 *
	 * type:INT,size:10,default:null
	 *
	 * @return mixed
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * set value for social_event_sync 0: don't sync events 1: sync event
	 *
	 * type:TINYINT UNSIGNED,size:3,default:0
	 *
	 * @param mixed $socialEventSync
	 * @return TimeteUserSocialprovider
	 */
	public function &setSocialEventSync($socialEventSync) {
		$this->notifyChanged(self::FIELD_SOCIAL_EVENT_SYNC,$this->socialEventSync,$socialEventSync);
		$this->socialEventSync=$socialEventSync;
		return $this;
	}

	/**
	 * get value for social_event_sync 0: don't sync events 1: sync event
	 *
	 * type:TINYINT UNSIGNED,size:3,default:0
	 *
	 * @return mixed
	 */
	public function getSocialEventSync() {
		return $this->socialEventSync;
	}

	/**
	 * set value for lastFetch 
	 *
	 * type:INT UNSIGNED,size:10,default:null,index,nullable
	 *
	 * @param mixed $lastFetch
	 * @return TimeteUserSocialprovider
	 */
	public function &setLastFetch($lastFetch) {
		$this->notifyChanged(self::FIELD_LASTFETCH,$this->lastFetch,$lastFetch);
		$this->lastFetch=$lastFetch;
		return $this;
	}

	/**
	 * get value for lastFetch 
	 *
	 * type:INT UNSIGNED,size:10,default:null,index,nullable
	 *
	 * @return mixed
	 */
	public function getLastFetch() {
		return $this->lastFetch;
	}

	/**
	 * set value for fetchFailCount 
	 *
	 * type:TINYINT UNSIGNED,size:3,default:null,index,nullable
	 *
	 * @param mixed $fetchFailCount
	 * @return TimeteUserSocialprovider
	 */
	public function &setFetchFailCount($fetchFailCount) {
		$this->notifyChanged(self::FIELD_FETCHFAILCOUNT,$this->fetchFailCount,$fetchFailCount);
		$this->fetchFailCount=$fetchFailCount;
		return $this;
	}

	/**
	 * get value for fetchFailCount 
	 *
	 * type:TINYINT UNSIGNED,size:3,default:null,index,nullable
	 *
	 * @return mixed
	 */
	public function getFetchFailCount() {
		return $this->fetchFailCount;
	}

	/**
	 * set value for lastStatusID 
	 *
	 * type:VARCHAR,size:50,default:null,index,nullable
	 *
	 * @param mixed $lastStatusID
	 * @return TimeteUserSocialprovider
	 */
	public function &setLastStatusID($lastStatusID) {
		$this->notifyChanged(self::FIELD_LASTSTATUSID,$this->lastStatusID,$lastStatusID);
		$this->lastStatusID=$lastStatusID;
		return $this;
	}

	/**
	 * get value for lastStatusID 
	 *
	 * type:VARCHAR,size:50,default:null,index,nullable
	 *
	 * @return mixed
	 */
	public function getLastStatusID() {
		return $this->lastStatusID;
	}

	/**
	 * set value for id 
	 *
	 * type:INT UNSIGNED,size:10,default:null,primary,unique,autoincrement
	 *
	 * @param mixed $id
	 * @return TimeteUserSocialprovider
	 */
	public function &setId($id) {
		$this->notifyChanged(self::FIELD_ID,$this->id,$id);
		$this->id=$id;
		return $this;
	}

	/**
	 * get value for id 
	 *
	 * type:INT UNSIGNED,size:10,default:null,primary,unique,autoincrement
	 *
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get table name
	 *
	 * @return string
	 */
	public static function getTableName() {
		return self::SQL_TABLE_NAME;
	}

	/**
	 * Get array with field id as index and field name as value
	 *
	 * @return array
	 */
	public static function getFieldNames() {
		return self::$FIELD_NAMES;
	}

	/**
	 * Get array with field id as index and property name as value
	 *
	 * @return array
	 */
	public static function getPropertyNames() {
		return self::$PROPERTY_NAMES;
	}

	/**
	 * get the field name for the passed field id.
	 *
	 * @param int $fieldId
	 * @param bool $fullyQualifiedName true if field name should be qualified by table name
	 * @return string field name for the passed field id, null if the field doesn't exist
	 */
	public static function getFieldNameByFieldId($fieldId, $fullyQualifiedName=true) {
		if (!array_key_exists($fieldId, self::$FIELD_NAMES)) {
			return null;
		}
		$fieldName=self::SQL_IDENTIFIER_QUOTE . self::$FIELD_NAMES[$fieldId] . self::SQL_IDENTIFIER_QUOTE;
		if ($fullyQualifiedName) {
			return self::SQL_IDENTIFIER_QUOTE . self::SQL_TABLE_NAME . self::SQL_IDENTIFIER_QUOTE . '.' . $fieldName;
		}
		return $fieldName;
	}

	/**
	 * Get array with field ids of identifiers
	 *
	 * @return array
	 */
	public static function getIdentifierFields() {
		return self::$PRIMARY_KEYS;
	}

	/**
	 * Get array with field ids of autoincrement fields
	 *
	 * @return array
	 */
	public static function getAutoincrementFields() {
		return self::$AUTOINCREMENT_FIELDS;
	}

	/**
	 * Get array with field id as index and property type as value
	 *
	 * @return array
	 */
	public static function getPropertyTypes() {
		return self::$PROPERTY_TYPES;
	}

	/**
	 * Get array with field id as index and field type as value
	 *
	 * @return array
	 */
	public static function getFieldTypes() {
		return self::$FIELD_TYPES;
	}

	/**
	 * Assign default values according to table
	 * 
	 */
	public function assignDefaultValues() {
		$this->assignByArray(self::$DEFAULT_VALUES);
	}


	/**
	 * return hash with the field name as index and the field value as value.
	 *
	 * @return array
	 */
	public function toHash() {
		$array=$this->toArray();
		$hash=array();
		foreach ($array as $fieldId=>$value) {
			$hash[self::$FIELD_NAMES[$fieldId]]=$value;
		}
		return $hash;
	}

	/**
	 * return array with the field id as index and the field value as value.
	 *
	 * @return array
	 */
	public function toArray() {
		return array(
			self::FIELD_USER_ID=>$this->getUserId(),
			self::FIELD_OAUTH_UID=>$this->getOauthUid(),
			self::FIELD_OAUTH_PROVIDER=>$this->getOauthProvider(),
			self::FIELD_OAUTH_TOKEN=>$this->getOauthToken(),
			self::FIELD_OAUTH_TOKEN_SECRET=>$this->getOauthTokenSecret(),
			self::FIELD_STATUS=>$this->getStatus(),
			self::FIELD_SOCIAL_EVENT_SYNC=>$this->getSocialEventSync(),
			self::FIELD_LASTFETCH=>$this->getLastFetch(),
			self::FIELD_FETCHFAILCOUNT=>$this->getFetchFailCount(),
			self::FIELD_LASTSTATUSID=>$this->getLastStatusID(),
			self::FIELD_ID=>$this->getId());
	}


	/**
	 * return array with the field id as index and the field value as value for the identifier fields.
	 *
	 * @return array
	 */
	public function getPrimaryKeyValues() {
		return array(
			self::FIELD_ID=>$this->getId());
	}

	/**
	 * cached statements
	 *
	 * @var array<string,array<string,PDOStatement>>
	 */
	private static $stmts=array();
	private static $cacheStatements=true;
	
	/**
	 * prepare passed string as statement or return cached if enabled and available
	 *
	 * @param PDO $db
	 * @param string $statement
	 * @return PDOStatement
	 */
	protected static function prepareStatement(PDO $db, $statement) {
		if(self::isCacheStatements()) {
			if (in_array($statement, array(self::SQL_INSERT, self::SQL_INSERT_AUTOINCREMENT, self::SQL_UPDATE, self::SQL_SELECT_PK, self::SQL_DELETE_PK))) {
				$dbInstanceId=spl_object_hash($db);
				if (empty(self::$stmts[$statement][$dbInstanceId])) {
					self::$stmts[$statement][$dbInstanceId]=$db->prepare($statement);
				}
				return self::$stmts[$statement][$dbInstanceId];
			}
		}
		return $db->prepare($statement);
	}

	/**
	 * Enable statement cache
	 *
	 * @param bool $cache
	 */
	public static function setCacheStatements($cache) {
		self::$cacheStatements=true==$cache;
	}

	/**
	 * Check if statement cache is enabled
	 *
	 * @return bool
	 */
	public static function isCacheStatements() {
		return self::$cacheStatements;
	}
	
	/**
	 * check if this instance exists in the database
	 *
	 * @param PDO $db
	 * @return bool
	 */
	public function existsInDatabase(PDO $db) {
		$filter=array();
		foreach ($this->getPrimaryKeyValues() as $fieldId=>$value) {
			$filter[]=new DFC($fieldId, $value, DFC::EXACT_NULLSAFE);
		}
		return 0!=count(self::findByFilter($db, $filter, true));
	}
	
	/**
	 * Update to database if exists, otherwise insert
	 *
	 * @param PDO $db
	 * @return mixed
	 */
	public function updateInsertToDatabase(PDO $db) {
		if ($this->existsInDatabase($db)) {
			return $this->updateToDatabase($db);
		} else {
			return $this->insertIntoDatabase($db);
		}
	}

	/**
	 * Query by Example.
	 *
	 * Match by attributes of passed example instance and return matched rows as an array of TimeteUserSocialprovider instances
	 *
	 * @param PDO $db a PDO Database instance
	 * @param TimeteUserSocialprovider $example an example instance defining the conditions. All non-null properties will be considered a constraint, null values will be ignored.
	 * @param boolean $and true if conditions should be and'ed, false if they should be or'ed
	 * @param array $sort array of DSC instances
	 * @return TimeteUserSocialprovider[]
	 */
	public static function findByExample(PDO $db,TimeteUserSocialprovider $example, $and=true, $sort=null) {
		$exampleValues=$example->toArray();
		$filter=array();
		foreach ($exampleValues as $fieldId=>$value) {
			if (null!==$value) {
				$filter[$fieldId]=$value;
			}
		}
		return self::findByFilter($db, $filter, $and, $sort);
	}

	/**
	 * Query by filter.
	 *
	 * The filter can be either an hash with the field id as index and the value as filter value,
	 * or a array of DFC instances.
	 *
	 * Will return matched rows as an array of TimeteUserSocialprovider instances.
	 *
	 * @param PDO $db a PDO Database instance
	 * @param array $filter array of DFC instances defining the conditions
	 * @param boolean $and true if conditions should be and'ed, false if they should be or'ed
	 * @param array $sort array of DSC instances
	 * @return TimeteUserSocialprovider[]
	 */
	public static function findByFilter(PDO $db, $filter, $and=true, $sort=null) {
		if (!($filter instanceof DFCInterface)) {
			$filter=new DFCAggregate($filter, $and);
		}
		$sql='SELECT * FROM `timete_user_socialprovider`'
		. self::buildSqlWhere($filter, $and, false, true)
		. self::buildSqlOrderBy($sort);

		$stmt=self::prepareStatement($db, $sql);
		self::bindValuesForFilter($stmt, $filter);
		return self::fromStatement($stmt);
	}

	/**
	 * Will execute the passed statement and return the result as an array of TimeteUserSocialprovider instances
	 *
	 * @param PDOStatement $stmt
	 * @return TimeteUserSocialprovider[]
	 */
	public static function fromStatement(PDOStatement $stmt) {
		$affected=$stmt->execute();
		if (false===$affected) {
			$stmt->closeCursor();
			throw new Exception($stmt->errorCode() . ':' . var_export($stmt->errorInfo(), true), 0);
		}
		return self::fromExecutedStatement($stmt);
	}

	/**
	 * returns the result as an array of TimeteUserSocialprovider instances without executing the passed statement
	 *
	 * @param PDOStatement $stmt
	 * @return TimeteUserSocialprovider[]
	 */
	public static function fromExecutedStatement(PDOStatement $stmt) {
		$resultInstances=array();
		while($result=$stmt->fetch(PDO::FETCH_ASSOC)) {
			$o=new TimeteUserSocialprovider();
			$o->assignByHash($result);
			$o->notifyPristine();
			$resultInstances[]=$o;
		}
		$stmt->closeCursor();
		return $resultInstances;
	}

	/**
	 * Get sql WHERE part from filter.
	 *
	 * @param array $filter
	 * @param bool $and
	 * @param bool $fullyQualifiedNames true if field names should be qualified by table name
	 * @param bool $prependWhere true if WHERE should be prepended to conditions
	 * @return string
	 */
	public static function buildSqlWhere($filter, $and, $fullyQualifiedNames=true, $prependWhere=false) {
		if (!($filter instanceof DFCInterface)) {
			$filter=new DFCAggregate($filter, $and);
		}
		return $filter->buildSqlWhere(new self::$CLASS_NAME, $fullyQualifiedNames, $prependWhere);
	}

	/**
	 * get sql ORDER BY part from DSCs
	 *
	 * @param array $sort array of DSC instances
	 * @return string
	 */
	protected static function buildSqlOrderBy($sort) {
		return DSC::buildSqlOrderBy(new self::$CLASS_NAME, $sort);
	}

	/**
	 * bind values from filter to statement
	 *
	 * @param PDOStatement $stmt
	 * @param DFCInterface $filter
	 */
	public static function bindValuesForFilter(PDOStatement &$stmt, DFCInterface $filter) {
		$filter->bindValuesForFilter(new self::$CLASS_NAME, $stmt);
	}

	/**
	 * Execute select query and return matched rows as an array of TimeteUserSocialprovider instances.
	 *
	 * The query should of course be on the table for this entity class and return all fields.
	 *
	 * @param PDO $db a PDO Database instance
	 * @param string $sql
	 * @return TimeteUserSocialprovider[]
	 */
	public static function findBySql(PDO $db, $sql) {
		$stmt=$db->query($sql);
		return self::fromExecutedStatement($stmt);
	}

	/**
	 * Delete rows matching the filter
	 *
	 * The filter can be either an hash with the field id as index and the value as filter value,
	 * or a array of DFC instances.
	 *
	 * @param PDO $db
	 * @param array $filter
	 * @param bool $and
	 * @return mixed
	 */
	public static function deleteByFilter(PDO $db, $filter, $and=true) {
		if (!($filter instanceof DFCInterface)) {
			$filter=new DFCAggregate($filter, $and);
		}
		if (0==count($filter)) {
			throw new InvalidArgumentException('refusing to delete without filter'); // just comment out this line if you are brave
		}
		$sql='DELETE FROM `timete_user_socialprovider`'
		. self::buildSqlWhere($filter, $and, false, true);
		$stmt=self::prepareStatement($db, $sql);
		self::bindValuesForFilter($stmt, $filter);
		$affected=$stmt->execute();
		if (false===$affected) {
			$stmt->closeCursor();
			throw new Exception($stmt->errorCode() . ':' . var_export($stmt->errorInfo(), true), 0);
		}
		$stmt->closeCursor();
		return $affected;
	}

	/**
	 * Assign values from array with the field id as index and the value as value
	 *
	 * @param array $array
	 */
	public function assignByArray($array) {
		$result=array();
		foreach ($array as $fieldId=>$value) {
			$result[self::$FIELD_NAMES[$fieldId]]=$value;
		}
		$this->assignByHash($result);
	}

	/**
	 * Assign values from hash where the indexes match the tables field names
	 *
	 * @param array $result
	 */
	public function assignByHash($result) {
		$this->setUserId($result['user_id']);
		$this->setOauthUid($result['oauth_uid']);
		$this->setOauthProvider($result['oauth_provider']);
		$this->setOauthToken($result['oauth_token']);
		$this->setOauthTokenSecret($result['oauth_token_secret']);
		$this->setStatus($result['status']);
		$this->setSocialEventSync($result['social_event_sync']);
		$this->setLastFetch($result['lastFetch']);
		$this->setFetchFailCount($result['fetchFailCount']);
		$this->setLastStatusID($result['lastStatusID']);
		$this->setId($result['id']);
	}

	/**
	 * Get element instance by it's primary key(s).
	 * Will return null if no row was matched.
	 *
	 * @param PDO $db
	 * @return TimeteUserSocialprovider
	 */
	public static function findById(PDO $db,$id) {
		$stmt=self::prepareStatement($db,self::SQL_SELECT_PK);
		$stmt->bindValue(1,$id);
		$affected=$stmt->execute();
		if (false===$affected) {
			$stmt->closeCursor();
			throw new Exception($stmt->errorCode() . ':' . var_export($stmt->errorInfo(), true), 0);
		}
		$result=$stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if(!$result) {
			return null;
		}
		$o=new TimeteUserSocialprovider();
		$o->assignByHash($result);
		$o->notifyPristine();
		return $o;
	}

	/**
	 * Bind all values to statement
	 *
	 * @param PDOStatement $stmt
	 */
	protected function bindValues(PDOStatement &$stmt) {
		$stmt->bindValue(1,$this->getUserId());
		$stmt->bindValue(2,$this->getOauthUid());
		$stmt->bindValue(3,$this->getOauthProvider());
		$stmt->bindValue(4,$this->getOauthToken());
		$stmt->bindValue(5,$this->getOauthTokenSecret());
		$stmt->bindValue(6,$this->getStatus());
		$stmt->bindValue(7,$this->getSocialEventSync());
		$stmt->bindValue(8,$this->getLastFetch());
		$stmt->bindValue(9,$this->getFetchFailCount());
		$stmt->bindValue(10,$this->getLastStatusID());
		$stmt->bindValue(11,$this->getId());
	}


	/**
	 * Insert this instance into the database
	 *
	 * @param PDO $db
	 * @return mixed
	 */
	public function insertIntoDatabase(PDO $db) {
		if (null===$this->getId()) {
			$stmt=self::prepareStatement($db,self::SQL_INSERT_AUTOINCREMENT);
			$stmt->bindValue(1,$this->getUserId());
			$stmt->bindValue(2,$this->getOauthUid());
			$stmt->bindValue(3,$this->getOauthProvider());
			$stmt->bindValue(4,$this->getOauthToken());
			$stmt->bindValue(5,$this->getOauthTokenSecret());
			$stmt->bindValue(6,$this->getStatus());
			$stmt->bindValue(7,$this->getSocialEventSync());
			$stmt->bindValue(8,$this->getLastFetch());
			$stmt->bindValue(9,$this->getFetchFailCount());
			$stmt->bindValue(10,$this->getLastStatusID());
		} else {
			$stmt=self::prepareStatement($db,self::SQL_INSERT);
			$this->bindValues($stmt);
		}
		$affected=$stmt->execute();
		if (false===$affected) {
			$stmt->closeCursor();
			throw new Exception($stmt->errorCode() . ':' . var_export($stmt->errorInfo(), true), 0);
		}
		$lastInsertId=$db->lastInsertId();
		if (false!==$lastInsertId) {
			$this->setId($lastInsertId);
		}
		$stmt->closeCursor();
		$this->notifyPristine();
		return $affected;
	}


	/**
	 * Update this instance into the database
	 *
	 * @param PDO $db
	 * @return mixed
	 */
	public function updateToDatabase(PDO $db) {
		$stmt=self::prepareStatement($db,self::SQL_UPDATE);
		$this->bindValues($stmt);
		$stmt->bindValue(12,$this->getId());
		$affected=$stmt->execute();
		if (false===$affected) {
			$stmt->closeCursor();
			throw new Exception($stmt->errorCode() . ':' . var_export($stmt->errorInfo(), true), 0);
		}
		$stmt->closeCursor();
		$this->notifyPristine();
		return $affected;
	}


	/**
	 * Delete this instance from the database
	 *
	 * @param PDO $db
	 * @return mixed
	 */
	public function deleteFromDatabase(PDO $db) {
		$stmt=self::prepareStatement($db,self::SQL_DELETE_PK);
		$stmt->bindValue(1,$this->getId());
		$affected=$stmt->execute();
		if (false===$affected) {
			$stmt->closeCursor();
			throw new Exception($stmt->errorCode() . ':' . var_export($stmt->errorInfo(), true), 0);
		}
		$stmt->closeCursor();
		return $affected;
	}


	/**
	 * get element as DOM Document
	 *
	 * @return DOMDocument
	 */
	public function toDOM() {
		return self::hashToDomDocument($this->toHash(), 'TimeteUserSocialprovider');
	}

	/**
	 * get single TimeteUserSocialprovider instance from a DOMElement
	 *
	 * @param DOMElement $node
	 * @return TimeteUserSocialprovider
	 */
	public static function fromDOMElement(DOMElement $node) {
		$o=new TimeteUserSocialprovider();
		$o->assignByHash(self::domNodeToHash($node, self::$FIELD_NAMES, self::$DEFAULT_VALUES, self::$FIELD_TYPES));
			$o->notifyPristine();
		return $o;
	}

	/**
	 * get all instances of TimeteUserSocialprovider from the passed DOMDocument
	 *
	 * @param DOMDocument $doc
	 * @return TimeteUserSocialprovider[]
	 */
	public static function fromDOMDocument(DOMDocument $doc) {
		$instances=array();
		foreach ($doc->getElementsByTagName('TimeteUserSocialprovider') as $node) {
			$instances[]=self::fromDOMElement($node);
		}
		return $instances;
	}

}
?>