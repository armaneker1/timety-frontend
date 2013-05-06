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
class TimeteUserSettings extends Db2PhpEntityBase implements Db2PhpEntityModificationTracking {
	private static $CLASS_NAME='TimeteUserSettings';
	const SQL_IDENTIFIER_QUOTE='`';
	const SQL_TABLE_NAME='timete_user_settings';
	const SQL_INSERT='INSERT INTO `timete_user_settings` (`user_id`,`bg_image_active`,`bg_image`,`bg_image_repeat`,`bg_color_active`,`bg_color`,`banner_active`) VALUES (?,?,?,?,?,?,?)';
	const SQL_INSERT_AUTOINCREMENT='INSERT INTO `timete_user_settings` (`user_id`,`bg_image_active`,`bg_image`,`bg_image_repeat`,`bg_color_active`,`bg_color`,`banner_active`) VALUES (?,?,?,?,?,?,?)';
	const SQL_UPDATE='UPDATE `timete_user_settings` SET `user_id`=?,`bg_image_active`=?,`bg_image`=?,`bg_image_repeat`=?,`bg_color_active`=?,`bg_color`=?,`banner_active`=? WHERE `user_id`=?';
	const SQL_SELECT_PK='SELECT * FROM `timete_user_settings` WHERE `user_id`=?';
	const SQL_DELETE_PK='DELETE FROM `timete_user_settings` WHERE `user_id`=?';
	const FIELD_USER_ID=995851703;
	const FIELD_BG_IMAGE_ACTIVE=-2131475508;
	const FIELD_BG_IMAGE=-186812871;
	const FIELD_BG_IMAGE_REPEAT=-1643056543;
	const FIELD_BG_COLOR_ACTIVE=-464624444;
	const FIELD_BG_COLOR=-192283583;
	const FIELD_BANNER_ACTIVE=914425121;
	private static $PRIMARY_KEYS=array(self::FIELD_USER_ID);
	private static $AUTOINCREMENT_FIELDS=array();
	private static $FIELD_NAMES=array(
		self::FIELD_USER_ID=>'user_id',
		self::FIELD_BG_IMAGE_ACTIVE=>'bg_image_active',
		self::FIELD_BG_IMAGE=>'bg_image',
		self::FIELD_BG_IMAGE_REPEAT=>'bg_image_repeat',
		self::FIELD_BG_COLOR_ACTIVE=>'bg_color_active',
		self::FIELD_BG_COLOR=>'bg_color',
		self::FIELD_BANNER_ACTIVE=>'banner_active');
	private static $PROPERTY_NAMES=array(
		self::FIELD_USER_ID=>'userId',
		self::FIELD_BG_IMAGE_ACTIVE=>'bgImageActive',
		self::FIELD_BG_IMAGE=>'bgImage',
		self::FIELD_BG_IMAGE_REPEAT=>'bgImageRepeat',
		self::FIELD_BG_COLOR_ACTIVE=>'bgColorActive',
		self::FIELD_BG_COLOR=>'bgColor',
		self::FIELD_BANNER_ACTIVE=>'bannerActive');
	private static $PROPERTY_TYPES=array(
		self::FIELD_USER_ID=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_BG_IMAGE_ACTIVE=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_BG_IMAGE=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_BG_IMAGE_REPEAT=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_BG_COLOR_ACTIVE=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_BG_COLOR=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_BANNER_ACTIVE=>Db2PhpEntity::PHP_TYPE_INT);
	private static $FIELD_TYPES=array(
		self::FIELD_USER_ID=>array(Db2PhpEntity::JDBC_TYPE_INTEGER,10,0,false),
		self::FIELD_BG_IMAGE_ACTIVE=>array(Db2PhpEntity::JDBC_TYPE_INTEGER,10,0,false),
		self::FIELD_BG_IMAGE=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,500,0,false),
		self::FIELD_BG_IMAGE_REPEAT=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,100,0,false),
		self::FIELD_BG_COLOR_ACTIVE=>array(Db2PhpEntity::JDBC_TYPE_INTEGER,10,0,false),
		self::FIELD_BG_COLOR=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,100,0,false),
		self::FIELD_BANNER_ACTIVE=>array(Db2PhpEntity::JDBC_TYPE_INTEGER,10,0,false));
	private static $DEFAULT_VALUES=array(
		self::FIELD_USER_ID=>0,
		self::FIELD_BG_IMAGE_ACTIVE=>0,
		self::FIELD_BG_IMAGE=>'',
		self::FIELD_BG_IMAGE_REPEAT=>'',
		self::FIELD_BG_COLOR_ACTIVE=>0,
		self::FIELD_BG_COLOR=>'',
		self::FIELD_BANNER_ACTIVE=>0);
	private $userId;
	private $bgImageActive;
	private $bgImage;
	private $bgImageRepeat;
	private $bgColorActive;
	private $bgColor;
	private $bannerActive;

	/**
	 * set value for user_id 
	 *
	 * type:INT,size:10,default:null,primary,unique
	 *
	 * @param mixed $userId
	 * @return TimeteUserSettings
	 */
	public function &setUserId($userId) {
		$this->notifyChanged(self::FIELD_USER_ID,$this->userId,$userId);
		$this->userId=$userId;
		return $this;
	}

	/**
	 * get value for user_id 
	 *
	 * type:INT,size:10,default:null,primary,unique
	 *
	 * @return mixed
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * set value for bg_image_active 
	 *
	 * type:INT,size:10,default:null
	 *
	 * @param mixed $bgImageActive
	 * @return TimeteUserSettings
	 */
	public function &setBgImageActive($bgImageActive) {
		$this->notifyChanged(self::FIELD_BG_IMAGE_ACTIVE,$this->bgImageActive,$bgImageActive);
		$this->bgImageActive=$bgImageActive;
		return $this;
	}

	/**
	 * get value for bg_image_active 
	 *
	 * type:INT,size:10,default:null
	 *
	 * @return mixed
	 */
	public function getBgImageActive() {
		return $this->bgImageActive;
	}

	/**
	 * set value for bg_image 
	 *
	 * type:VARCHAR,size:500,default:null
	 *
	 * @param mixed $bgImage
	 * @return TimeteUserSettings
	 */
	public function &setBgImage($bgImage) {
		$this->notifyChanged(self::FIELD_BG_IMAGE,$this->bgImage,$bgImage);
		$this->bgImage=$bgImage;
		return $this;
	}

	/**
	 * get value for bg_image 
	 *
	 * type:VARCHAR,size:500,default:null
	 *
	 * @return mixed
	 */
	public function getBgImage() {
		return $this->bgImage;
	}

	/**
	 * set value for bg_image_repeat 
	 *
	 * type:VARCHAR,size:100,default:null
	 *
	 * @param mixed $bgImageRepeat
	 * @return TimeteUserSettings
	 */
	public function &setBgImageRepeat($bgImageRepeat) {
		$this->notifyChanged(self::FIELD_BG_IMAGE_REPEAT,$this->bgImageRepeat,$bgImageRepeat);
		$this->bgImageRepeat=$bgImageRepeat;
		return $this;
	}

	/**
	 * get value for bg_image_repeat 
	 *
	 * type:VARCHAR,size:100,default:null
	 *
	 * @return mixed
	 */
	public function getBgImageRepeat() {
		return $this->bgImageRepeat;
	}

	/**
	 * set value for bg_color_active 
	 *
	 * type:INT,size:10,default:null
	 *
	 * @param mixed $bgColorActive
	 * @return TimeteUserSettings
	 */
	public function &setBgColorActive($bgColorActive) {
		$this->notifyChanged(self::FIELD_BG_COLOR_ACTIVE,$this->bgColorActive,$bgColorActive);
		$this->bgColorActive=$bgColorActive;
		return $this;
	}

	/**
	 * get value for bg_color_active 
	 *
	 * type:INT,size:10,default:null
	 *
	 * @return mixed
	 */
	public function getBgColorActive() {
		return $this->bgColorActive;
	}

	/**
	 * set value for bg_color 
	 *
	 * type:VARCHAR,size:100,default:null
	 *
	 * @param mixed $bgColor
	 * @return TimeteUserSettings
	 */
	public function &setBgColor($bgColor) {
		$this->notifyChanged(self::FIELD_BG_COLOR,$this->bgColor,$bgColor);
		$this->bgColor=$bgColor;
		return $this;
	}

	/**
	 * get value for bg_color 
	 *
	 * type:VARCHAR,size:100,default:null
	 *
	 * @return mixed
	 */
	public function getBgColor() {
		return $this->bgColor;
	}

	/**
	 * set value for banner_active 
	 *
	 * type:INT,size:10,default:0
	 *
	 * @param mixed $bannerActive
	 * @return TimeteUserSettings
	 */
	public function &setBannerActive($bannerActive) {
		$this->notifyChanged(self::FIELD_BANNER_ACTIVE,$this->bannerActive,$bannerActive);
		$this->bannerActive=$bannerActive;
		return $this;
	}

	/**
	 * get value for banner_active 
	 *
	 * type:INT,size:10,default:0
	 *
	 * @return mixed
	 */
	public function getBannerActive() {
		return $this->bannerActive;
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
			self::FIELD_BG_IMAGE_ACTIVE=>$this->getBgImageActive(),
			self::FIELD_BG_IMAGE=>$this->getBgImage(),
			self::FIELD_BG_IMAGE_REPEAT=>$this->getBgImageRepeat(),
			self::FIELD_BG_COLOR_ACTIVE=>$this->getBgColorActive(),
			self::FIELD_BG_COLOR=>$this->getBgColor(),
			self::FIELD_BANNER_ACTIVE=>$this->getBannerActive());
	}


	/**
	 * return array with the field id as index and the field value as value for the identifier fields.
	 *
	 * @return array
	 */
	public function getPrimaryKeyValues() {
		return array(
			self::FIELD_USER_ID=>$this->getUserId());
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
	 * Match by attributes of passed example instance and return matched rows as an array of TimeteUserSettings instances
	 *
	 * @param PDO $db a PDO Database instance
	 * @param TimeteUserSettings $example an example instance defining the conditions. All non-null properties will be considered a constraint, null values will be ignored.
	 * @param boolean $and true if conditions should be and'ed, false if they should be or'ed
	 * @param array $sort array of DSC instances
	 * @return TimeteUserSettings[]
	 */
	public static function findByExample(PDO $db,TimeteUserSettings $example, $and=true, $sort=null) {
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
	 * Will return matched rows as an array of TimeteUserSettings instances.
	 *
	 * @param PDO $db a PDO Database instance
	 * @param array $filter array of DFC instances defining the conditions
	 * @param boolean $and true if conditions should be and'ed, false if they should be or'ed
	 * @param array $sort array of DSC instances
	 * @return TimeteUserSettings[]
	 */
	public static function findByFilter(PDO $db, $filter, $and=true, $sort=null) {
		if (!($filter instanceof DFCInterface)) {
			$filter=new DFCAggregate($filter, $and);
		}
		$sql='SELECT * FROM `timete_user_settings`'
		. self::buildSqlWhere($filter, $and, false, true)
		. self::buildSqlOrderBy($sort);

		$stmt=self::prepareStatement($db, $sql);
		self::bindValuesForFilter($stmt, $filter);
		return self::fromStatement($stmt);
	}

	/**
	 * Will execute the passed statement and return the result as an array of TimeteUserSettings instances
	 *
	 * @param PDOStatement $stmt
	 * @return TimeteUserSettings[]
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
	 * returns the result as an array of TimeteUserSettings instances without executing the passed statement
	 *
	 * @param PDOStatement $stmt
	 * @return TimeteUserSettings[]
	 */
	public static function fromExecutedStatement(PDOStatement $stmt) {
		$resultInstances=array();
		while($result=$stmt->fetch(PDO::FETCH_ASSOC)) {
			$o=new TimeteUserSettings();
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
	 * Execute select query and return matched rows as an array of TimeteUserSettings instances.
	 *
	 * The query should of course be on the table for this entity class and return all fields.
	 *
	 * @param PDO $db a PDO Database instance
	 * @param string $sql
	 * @return TimeteUserSettings[]
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
		$sql='DELETE FROM `timete_user_settings`'
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
		$this->setBgImageActive($result['bg_image_active']);
		$this->setBgImage($result['bg_image']);
		$this->setBgImageRepeat($result['bg_image_repeat']);
		$this->setBgColorActive($result['bg_color_active']);
		$this->setBgColor($result['bg_color']);
		$this->setBannerActive($result['banner_active']);
	}

	/**
	 * Get element instance by it's primary key(s).
	 * Will return null if no row was matched.
	 *
	 * @param PDO $db
	 * @return TimeteUserSettings
	 */
	public static function findById(PDO $db,$userId) {
		$stmt=self::prepareStatement($db,self::SQL_SELECT_PK);
		$stmt->bindValue(1,$userId);
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
		$o=new TimeteUserSettings();
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
		$stmt->bindValue(2,$this->getBgImageActive());
		$stmt->bindValue(3,$this->getBgImage());
		$stmt->bindValue(4,$this->getBgImageRepeat());
		$stmt->bindValue(5,$this->getBgColorActive());
		$stmt->bindValue(6,$this->getBgColor());
		$stmt->bindValue(7,$this->getBannerActive());
	}


	/**
	 * Insert this instance into the database
	 *
	 * @param PDO $db
	 * @return mixed
	 */
	public function insertIntoDatabase(PDO $db) {
		$stmt=self::prepareStatement($db,self::SQL_INSERT);
		$this->bindValues($stmt);
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
	 * Update this instance into the database
	 *
	 * @param PDO $db
	 * @return mixed
	 */
	public function updateToDatabase(PDO $db) {
		$stmt=self::prepareStatement($db,self::SQL_UPDATE);
		$this->bindValues($stmt);
		$stmt->bindValue(8,$this->getUserId());
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
		$stmt->bindValue(1,$this->getUserId());
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
		return self::hashToDomDocument($this->toHash(), 'TimeteUserSettings');
	}

	/**
	 * get single TimeteUserSettings instance from a DOMElement
	 *
	 * @param DOMElement $node
	 * @return TimeteUserSettings
	 */
	public static function fromDOMElement(DOMElement $node) {
		$o=new TimeteUserSettings();
		$o->assignByHash(self::domNodeToHash($node, self::$FIELD_NAMES, self::$DEFAULT_VALUES, self::$FIELD_TYPES));
			$o->notifyPristine();
		return $o;
	}

	/**
	 * get all instances of TimeteUserSettings from the passed DOMDocument
	 *
	 * @param DOMDocument $doc
	 * @return TimeteUserSettings[]
	 */
	public static function fromDOMDocument(DOMDocument $doc) {
		$instances=array();
		foreach ($doc->getElementsByTagName('TimeteUserSettings') as $node) {
			$instances[]=self::fromDOMElement($node);
		}
		return $instances;
	}

}
?>