<?php
require_once __DIR__ . '/../../../apis/db2php/Db2PhpEntity.class.php';
require_once __DIR__ . '/../../../apis/db2php/Db2PhpEntityBase.class.php';
require_once __DIR__ . '/../../../apis/db2php/Db2PhpEntityModificationTracking.class.php';
require_once __DIR__ . '/../../../apis/db2php/DFCInterface.class.php';
require_once __DIR__ . '/../../../apis/db2php/DFCAggregate.class.php';
require_once __DIR__ . '/../../../apis/db2php/DFC.class.php';
require_once __DIR__ . '/../../../apis/db2php/DSC.class.php';


/**
 * 
 *
 * @version 1.107
 * @package entity
 */
class TimeteMailFailReports extends Db2PhpEntityBase implements Db2PhpEntityModificationTracking {
	private static $CLASS_NAME='TimeteMailFailReports';
	const SQL_IDENTIFIER_QUOTE='`';
	const SQL_TABLE_NAME='timete_mail_fail_reports';
	const SQL_INSERT='INSERT INTO `timete_mail_fail_reports` (`id`,`user_id`,`to_email`,`content`,`triedAt`,`reason`) VALUES (?,?,?,?,?,?)';
	const SQL_INSERT_AUTOINCREMENT='INSERT INTO `timete_mail_fail_reports` (`user_id`,`to_email`,`content`,`triedAt`,`reason`) VALUES (?,?,?,?,?)';
	const SQL_UPDATE='UPDATE `timete_mail_fail_reports` SET `id`=?,`user_id`=?,`to_email`=?,`content`=?,`triedAt`=?,`reason`=? WHERE `id`=?';
	const SQL_SELECT_PK='SELECT * FROM `timete_mail_fail_reports` WHERE `id`=?';
	const SQL_DELETE_PK='DELETE FROM `timete_mail_fail_reports` WHERE `id`=?';
	const FIELD_ID=-514642812;
	const FIELD_USER_ID=-1959786426;
	const FIELD_TO_EMAIL=-1805065855;
	const FIELD_CONTENT=-861122896;
	const FIELD_TRIEDAT=1422358420;
	const FIELD_REASON=-2101816019;
	private static $PRIMARY_KEYS=array(self::FIELD_ID);
	private static $AUTOINCREMENT_FIELDS=array(self::FIELD_ID);
	private static $FIELD_NAMES=array(
		self::FIELD_ID=>'id',
		self::FIELD_USER_ID=>'user_id',
		self::FIELD_TO_EMAIL=>'to_email',
		self::FIELD_CONTENT=>'content',
		self::FIELD_TRIEDAT=>'triedAt',
		self::FIELD_REASON=>'reason');
	private static $PROPERTY_NAMES=array(
		self::FIELD_ID=>'id',
		self::FIELD_USER_ID=>'userId',
		self::FIELD_TO_EMAIL=>'toEmail',
		self::FIELD_CONTENT=>'content',
		self::FIELD_TRIEDAT=>'triedAt',
		self::FIELD_REASON=>'reason');
	private static $PROPERTY_TYPES=array(
		self::FIELD_ID=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_USER_ID=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_TO_EMAIL=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_CONTENT=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_TRIEDAT=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_REASON=>Db2PhpEntity::PHP_TYPE_STRING);
	private static $FIELD_TYPES=array(
		self::FIELD_ID=>array(Db2PhpEntity::JDBC_TYPE_INTEGER,10,0,false),
		self::FIELD_USER_ID=>array(Db2PhpEntity::JDBC_TYPE_INTEGER,10,0,false),
		self::FIELD_TO_EMAIL=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,255,0,true),
		self::FIELD_CONTENT=>array(Db2PhpEntity::JDBC_TYPE_LONGVARCHAR,65535,0,true),
		self::FIELD_TRIEDAT=>array(Db2PhpEntity::JDBC_TYPE_TIMESTAMP,19,0,true),
		self::FIELD_REASON=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,500,0,true));
	private static $DEFAULT_VALUES=array(
		self::FIELD_ID=>null,
		self::FIELD_USER_ID=>0,
		self::FIELD_TO_EMAIL=>null,
		self::FIELD_CONTENT=>null,
		self::FIELD_TRIEDAT=>null,
		self::FIELD_REASON=>null);
	private $id;
	private $userId;
	private $toEmail;
	private $content;
	private $triedAt;
	private $reason;

	/**
	 * set value for id 
	 *
	 * type:INT UNSIGNED,size:10,default:null,primary,unique,autoincrement
	 *
	 * @param mixed $id
	 * @return TimeteMailFailReports
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
	 * set value for user_id 
	 *
	 * type:INT UNSIGNED,size:10,default:null,index
	 *
	 * @param mixed $userId
	 * @return TimeteMailFailReports
	 */
	public function &setUserId($userId) {
		$this->notifyChanged(self::FIELD_USER_ID,$this->userId,$userId);
		$this->userId=$userId;
		return $this;
	}

	/**
	 * get value for user_id 
	 *
	 * type:INT UNSIGNED,size:10,default:null,index
	 *
	 * @return mixed
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * set value for to_email 
	 *
	 * type:VARCHAR,size:255,default:null,index,nullable
	 *
	 * @param mixed $toEmail
	 * @return TimeteMailFailReports
	 */
	public function &setToEmail($toEmail) {
		$this->notifyChanged(self::FIELD_TO_EMAIL,$this->toEmail,$toEmail);
		$this->toEmail=$toEmail;
		return $this;
	}

	/**
	 * get value for to_email 
	 *
	 * type:VARCHAR,size:255,default:null,index,nullable
	 *
	 * @return mixed
	 */
	public function getToEmail() {
		return $this->toEmail;
	}

	/**
	 * set value for content 
	 *
	 * type:TEXT,size:65535,default:null,nullable
	 *
	 * @param mixed $content
	 * @return TimeteMailFailReports
	 */
	public function &setContent($content) {
		$this->notifyChanged(self::FIELD_CONTENT,$this->content,$content);
		$this->content=$content;
		return $this;
	}

	/**
	 * get value for content 
	 *
	 * type:TEXT,size:65535,default:null,nullable
	 *
	 * @return mixed
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * set value for triedAt 
	 *
	 * type:DATETIME,size:19,default:null,index,nullable
	 *
	 * @param mixed $triedAt
	 * @return TimeteMailFailReports
	 */
	public function &setTriedAt($triedAt) {
		$this->notifyChanged(self::FIELD_TRIEDAT,$this->triedAt,$triedAt);
		$this->triedAt=$triedAt;
		return $this;
	}

	/**
	 * get value for triedAt 
	 *
	 * type:DATETIME,size:19,default:null,index,nullable
	 *
	 * @return mixed
	 */
	public function getTriedAt() {
		return $this->triedAt;
	}

	/**
	 * set value for reason 
	 *
	 * type:VARCHAR,size:500,default:null,index,nullable
	 *
	 * @param mixed $reason
	 * @return TimeteMailFailReports
	 */
	public function &setReason($reason) {
		$this->notifyChanged(self::FIELD_REASON,$this->reason,$reason);
		$this->reason=$reason;
		return $this;
	}

	/**
	 * get value for reason 
	 *
	 * type:VARCHAR,size:500,default:null,index,nullable
	 *
	 * @return mixed
	 */
	public function getReason() {
		return $this->reason;
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
			self::FIELD_ID=>$this->getId(),
			self::FIELD_USER_ID=>$this->getUserId(),
			self::FIELD_TO_EMAIL=>$this->getToEmail(),
			self::FIELD_CONTENT=>$this->getContent(),
			self::FIELD_TRIEDAT=>$this->getTriedAt(),
			self::FIELD_REASON=>$this->getReason());
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
	 * Match by attributes of passed example instance and return matched rows as an array of TimeteMailFailReports instances
	 *
	 * @param PDO $db a PDO Database instance
	 * @param TimeteMailFailReports $example an example instance defining the conditions. All non-null properties will be considered a constraint, null values will be ignored.
	 * @param boolean $and true if conditions should be and'ed, false if they should be or'ed
	 * @param array $sort array of DSC instances
	 * @return TimeteMailFailReports[]
	 */
	public static function findByExample(PDO $db,TimeteMailFailReports $example, $and=true, $sort=null) {
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
	 * Will return matched rows as an array of TimeteMailFailReports instances.
	 *
	 * @param PDO $db a PDO Database instance
	 * @param array $filter array of DFC instances defining the conditions
	 * @param boolean $and true if conditions should be and'ed, false if they should be or'ed
	 * @param array $sort array of DSC instances
	 * @return TimeteMailFailReports[]
	 */
	public static function findByFilter(PDO $db, $filter, $and=true, $sort=null) {
		if (!($filter instanceof DFCInterface)) {
			$filter=new DFCAggregate($filter, $and);
		}
		$sql='SELECT * FROM `timete_mail_fail_reports`'
		. self::buildSqlWhere($filter, $and, false, true)
		. self::buildSqlOrderBy($sort);

		$stmt=self::prepareStatement($db, $sql);
		self::bindValuesForFilter($stmt, $filter);
		return self::fromStatement($stmt);
	}

	/**
	 * Will execute the passed statement and return the result as an array of TimeteMailFailReports instances
	 *
	 * @param PDOStatement $stmt
	 * @return TimeteMailFailReports[]
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
	 * returns the result as an array of TimeteMailFailReports instances without executing the passed statement
	 *
	 * @param PDOStatement $stmt
	 * @return TimeteMailFailReports[]
	 */
	public static function fromExecutedStatement(PDOStatement $stmt) {
		$resultInstances=array();
		while($result=$stmt->fetch(PDO::FETCH_ASSOC)) {
			$o=new TimeteMailFailReports();
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
	 * Execute select query and return matched rows as an array of TimeteMailFailReports instances.
	 *
	 * The query should of course be on the table for this entity class and return all fields.
	 *
	 * @param PDO $db a PDO Database instance
	 * @param string $sql
	 * @return TimeteMailFailReports[]
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
		$sql='DELETE FROM `timete_mail_fail_reports`'
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
		$this->setId($result['id']);
		$this->setUserId($result['user_id']);
		$this->setToEmail($result['to_email']);
		$this->setContent($result['content']);
		$this->setTriedAt($result['triedAt']);
		$this->setReason($result['reason']);
	}

	/**
	 * Get element instance by it's primary key(s).
	 * Will return null if no row was matched.
	 *
	 * @param PDO $db
	 * @return TimeteMailFailReports
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
		$o=new TimeteMailFailReports();
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
		$stmt->bindValue(1,$this->getId());
		$stmt->bindValue(2,$this->getUserId());
		$stmt->bindValue(3,$this->getToEmail());
		$stmt->bindValue(4,$this->getContent());
		$stmt->bindValue(5,$this->getTriedAt());
		$stmt->bindValue(6,$this->getReason());
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
			$stmt->bindValue(2,$this->getToEmail());
			$stmt->bindValue(3,$this->getContent());
			$stmt->bindValue(4,$this->getTriedAt());
			$stmt->bindValue(5,$this->getReason());
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
		$stmt->bindValue(7,$this->getId());
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
		return self::hashToDomDocument($this->toHash(), 'TimeteMailFailReports');
	}

	/**
	 * get single TimeteMailFailReports instance from a DOMElement
	 *
	 * @param DOMElement $node
	 * @return TimeteMailFailReports
	 */
	public static function fromDOMElement(DOMElement $node) {
		$o=new TimeteMailFailReports();
		$o->assignByHash(self::domNodeToHash($node, self::$FIELD_NAMES, self::$DEFAULT_VALUES, self::$FIELD_TYPES));
			$o->notifyPristine();
		return $o;
	}

	/**
	 * get all instances of TimeteMailFailReports from the passed DOMDocument
	 *
	 * @param DOMDocument $doc
	 * @return TimeteMailFailReports[]
	 */
	public static function fromDOMDocument(DOMDocument $doc) {
		$instances=array();
		foreach ($doc->getElementsByTagName('TimeteMailFailReports') as $node) {
			$instances[]=self::fromDOMElement($node);
		}
		return $instances;
	}

}
?>