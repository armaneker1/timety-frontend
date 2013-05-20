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
class TimeteSocialMedia extends Db2PhpEntityBase implements Db2PhpEntityModificationTracking {
	private static $CLASS_NAME='TimeteSocialMedia';
	const SQL_IDENTIFIER_QUOTE='`';
	const SQL_TABLE_NAME='timete_social_media';
	const SQL_INSERT='INSERT INTO `timete_social_media` (`id`,`type`,`date`,`imgUrl`,`imgWidth`,`imgHeight`,`videoUrl`,`userName`,`recordJSON`,`user_id`,`socialID`,`meidaType`,`socialUrl`,`description`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
	const SQL_INSERT_AUTOINCREMENT='INSERT INTO `timete_social_media` (`type`,`date`,`imgUrl`,`imgWidth`,`imgHeight`,`videoUrl`,`userName`,`recordJSON`,`user_id`,`socialID`,`meidaType`,`socialUrl`,`description`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
	const SQL_UPDATE='UPDATE `timete_social_media` SET `id`=?,`type`=?,`date`=?,`imgUrl`=?,`imgWidth`=?,`imgHeight`=?,`videoUrl`=?,`userName`=?,`recordJSON`=?,`user_id`=?,`socialID`=?,`meidaType`=?,`socialUrl`=?,`description`=? WHERE `id`=?';
	const SQL_SELECT_PK='SELECT * FROM `timete_social_media` WHERE `id`=?';
	const SQL_DELETE_PK='DELETE FROM `timete_social_media` WHERE `id`=?';
	const FIELD_ID=2018586550;
	const FIELD_TYPE=-1463191787;
	const FIELD_DATE=-1463691383;
	const FIELD_IMGURL=-1999284281;
	const FIELD_IMGWIDTH=-1460237794;
	const FIELD_IMGHEIGHT=1544273871;
	const FIELD_VIDEOURL=393618767;
	const FIELD_USERNAME=-1024426159;
	const FIELD_RECORDJSON=-1622730668;
	const FIELD_USER_ID=382612564;
	const FIELD_SOCIALID=464878499;
	const FIELD_MEIDATYPE=124163949;
	const FIELD_SOCIALURL=1526344647;
	const FIELD_DESCRIPTION=-1786634303;
	private static $PRIMARY_KEYS=array(self::FIELD_ID);
	private static $AUTOINCREMENT_FIELDS=array(self::FIELD_ID);
	private static $FIELD_NAMES=array(
		self::FIELD_ID=>'id',
		self::FIELD_TYPE=>'type',
		self::FIELD_DATE=>'date',
		self::FIELD_IMGURL=>'imgUrl',
		self::FIELD_IMGWIDTH=>'imgWidth',
		self::FIELD_IMGHEIGHT=>'imgHeight',
		self::FIELD_VIDEOURL=>'videoUrl',
		self::FIELD_USERNAME=>'userName',
		self::FIELD_RECORDJSON=>'recordJSON',
		self::FIELD_USER_ID=>'user_id',
		self::FIELD_SOCIALID=>'socialID',
		self::FIELD_MEIDATYPE=>'meidaType',
		self::FIELD_SOCIALURL=>'socialUrl',
		self::FIELD_DESCRIPTION=>'description');
	private static $PROPERTY_NAMES=array(
		self::FIELD_ID=>'id',
		self::FIELD_TYPE=>'type',
		self::FIELD_DATE=>'date',
		self::FIELD_IMGURL=>'imgUrl',
		self::FIELD_IMGWIDTH=>'imgWidth',
		self::FIELD_IMGHEIGHT=>'imgHeight',
		self::FIELD_VIDEOURL=>'videoUrl',
		self::FIELD_USERNAME=>'userName',
		self::FIELD_RECORDJSON=>'recordJSON',
		self::FIELD_USER_ID=>'userId',
		self::FIELD_SOCIALID=>'socialID',
		self::FIELD_MEIDATYPE=>'meidaType',
		self::FIELD_SOCIALURL=>'socialUrl',
		self::FIELD_DESCRIPTION=>'description');
	private static $PROPERTY_TYPES=array(
		self::FIELD_ID=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_TYPE=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_DATE=>Db2PhpEntity::PHP_TYPE_FLOAT,
		self::FIELD_IMGURL=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_IMGWIDTH=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_IMGHEIGHT=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_VIDEOURL=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_USERNAME=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_RECORDJSON=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_USER_ID=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_SOCIALID=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_MEIDATYPE=>Db2PhpEntity::PHP_TYPE_INT,
		self::FIELD_SOCIALURL=>Db2PhpEntity::PHP_TYPE_STRING,
		self::FIELD_DESCRIPTION=>Db2PhpEntity::PHP_TYPE_STRING);
	private static $FIELD_TYPES=array(
		self::FIELD_ID=>array(Db2PhpEntity::JDBC_TYPE_INTEGER,10,0,false),
		self::FIELD_TYPE=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,45,0,false),
		self::FIELD_DATE=>array(Db2PhpEntity::JDBC_TYPE_BIGINT,19,0,true),
		self::FIELD_IMGURL=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,500,0,true),
		self::FIELD_IMGWIDTH=>array(Db2PhpEntity::JDBC_TYPE_INTEGER,10,0,true),
		self::FIELD_IMGHEIGHT=>array(Db2PhpEntity::JDBC_TYPE_INTEGER,10,0,true),
		self::FIELD_VIDEOURL=>array(Db2PhpEntity::JDBC_TYPE_LONGVARCHAR,65535,0,true),
		self::FIELD_USERNAME=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,255,0,true),
		self::FIELD_RECORDJSON=>array(Db2PhpEntity::JDBC_TYPE_LONGVARCHAR,65535,0,true),
		self::FIELD_USER_ID=>array(Db2PhpEntity::JDBC_TYPE_INTEGER,10,0,true),
		self::FIELD_SOCIALID=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,100,0,true),
		self::FIELD_MEIDATYPE=>array(Db2PhpEntity::JDBC_TYPE_TINYINT,3,0,false),
		self::FIELD_SOCIALURL=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,500,0,true),
		self::FIELD_DESCRIPTION=>array(Db2PhpEntity::JDBC_TYPE_VARCHAR,500,0,true));
	private static $DEFAULT_VALUES=array(
		self::FIELD_ID=>null,
		self::FIELD_TYPE=>'',
		self::FIELD_DATE=>null,
		self::FIELD_IMGURL=>null,
		self::FIELD_IMGWIDTH=>null,
		self::FIELD_IMGHEIGHT=>null,
		self::FIELD_VIDEOURL=>null,
		self::FIELD_USERNAME=>null,
		self::FIELD_RECORDJSON=>null,
		self::FIELD_USER_ID=>null,
		self::FIELD_SOCIALID=>null,
		self::FIELD_MEIDATYPE=>0,
		self::FIELD_SOCIALURL=>null,
		self::FIELD_DESCRIPTION=>null);
	public  $id;
	public $type;
	public $date;
	public $imgUrl;
	public $imgWidth;
	public $imgHeight;
	public $videoUrl;
	public $userName;
	private $recordJSON;
	public $userId;
	public $socialID;
	public $meidaType;
	public $socialUrl;
	public $description;

	/**
	 * set value for id 
	 *
	 * type:INT UNSIGNED,size:10,default:null,primary,unique,autoincrement
	 *
	 * @param mixed $id
	 * @return TimeteSocialMedia
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
	 * set value for type 
	 *
	 * type:VARCHAR,size:45,default:null
	 *
	 * @param mixed $type
	 * @return TimeteSocialMedia
	 */
	public function &setType($type) {
		$this->notifyChanged(self::FIELD_TYPE,$this->type,$type);
		$this->type=$type;
		return $this;
	}

	/**
	 * get value for type 
	 *
	 * type:VARCHAR,size:45,default:null
	 *
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * set value for date 
	 *
	 * type:BIGINT,size:19,default:null,index,nullable
	 *
	 * @param mixed $date
	 * @return TimeteSocialMedia
	 */
	public function &setDate($date) {
		$this->notifyChanged(self::FIELD_DATE,$this->date,$date);
		$this->date=$date;
		return $this;
	}

	/**
	 * get value for date 
	 *
	 * type:BIGINT,size:19,default:null,index,nullable
	 *
	 * @return mixed
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * set value for imgUrl 
	 *
	 * type:VARCHAR,size:500,default:null,nullable
	 *
	 * @param mixed $imgUrl
	 * @return TimeteSocialMedia
	 */
	public function &setImgUrl($imgUrl) {
		$this->notifyChanged(self::FIELD_IMGURL,$this->imgUrl,$imgUrl);
		$this->imgUrl=$imgUrl;
		return $this;
	}

	/**
	 * get value for imgUrl 
	 *
	 * type:VARCHAR,size:500,default:null,nullable
	 *
	 * @return mixed
	 */
	public function getImgUrl() {
		return $this->imgUrl;
	}

	/**
	 * set value for imgWidth 
	 *
	 * type:INT,size:10,default:null,nullable
	 *
	 * @param mixed $imgWidth
	 * @return TimeteSocialMedia
	 */
	public function &setImgWidth($imgWidth) {
		$this->notifyChanged(self::FIELD_IMGWIDTH,$this->imgWidth,$imgWidth);
		$this->imgWidth=$imgWidth;
		return $this;
	}

	/**
	 * get value for imgWidth 
	 *
	 * type:INT,size:10,default:null,nullable
	 *
	 * @return mixed
	 */
	public function getImgWidth() {
		return $this->imgWidth;
	}

	/**
	 * set value for imgHeight 
	 *
	 * type:INT,size:10,default:null,nullable
	 *
	 * @param mixed $imgHeight
	 * @return TimeteSocialMedia
	 */
	public function &setImgHeight($imgHeight) {
		$this->notifyChanged(self::FIELD_IMGHEIGHT,$this->imgHeight,$imgHeight);
		$this->imgHeight=$imgHeight;
		return $this;
	}

	/**
	 * get value for imgHeight 
	 *
	 * type:INT,size:10,default:null,nullable
	 *
	 * @return mixed
	 */
	public function getImgHeight() {
		return $this->imgHeight;
	}

	/**
	 * set value for videoUrl 
	 *
	 * type:TEXT,size:65535,default:null,nullable
	 *
	 * @param mixed $videoUrl
	 * @return TimeteSocialMedia
	 */
	public function &setVideoUrl($videoUrl) {
		$this->notifyChanged(self::FIELD_VIDEOURL,$this->videoUrl,$videoUrl);
		$this->videoUrl=$videoUrl;
		return $this;
	}

	/**
	 * get value for videoUrl 
	 *
	 * type:TEXT,size:65535,default:null,nullable
	 *
	 * @return mixed
	 */
	public function getVideoUrl() {
		return $this->videoUrl;
	}

	/**
	 * set value for userName 
	 *
	 * type:VARCHAR,size:255,default:null,index,nullable
	 *
	 * @param mixed $userName
	 * @return TimeteSocialMedia
	 */
	public function &setUserName($userName) {
		$this->notifyChanged(self::FIELD_USERNAME,$this->userName,$userName);
		$this->userName=$userName;
		return $this;
	}

	/**
	 * get value for userName 
	 *
	 * type:VARCHAR,size:255,default:null,index,nullable
	 *
	 * @return mixed
	 */
	public function getUserName() {
		return $this->userName;
	}

	/**
	 * set value for recordJSON 
	 *
	 * type:TEXT,size:65535,default:null,nullable
	 *
	 * @param mixed $recordJSON
	 * @return TimeteSocialMedia
	 */
	public function &setRecordJSON($recordJSON) {
		$this->notifyChanged(self::FIELD_RECORDJSON,$this->recordJSON,$recordJSON);
		$this->recordJSON=$recordJSON;
		return $this;
	}

	/**
	 * get value for recordJSON 
	 *
	 * type:TEXT,size:65535,default:null,nullable
	 *
	 * @return mixed
	 */
	public function getRecordJSON() {
		return $this->recordJSON;
	}

	/**
	 * set value for user_id 
	 *
	 * type:INT,size:10,default:null,index,nullable
	 *
	 * @param mixed $userId
	 * @return TimeteSocialMedia
	 */
	public function &setUserId($userId) {
		$this->notifyChanged(self::FIELD_USER_ID,$this->userId,$userId);
		$this->userId=$userId;
		return $this;
	}

	/**
	 * get value for user_id 
	 *
	 * type:INT,size:10,default:null,index,nullable
	 *
	 * @return mixed
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * set value for socialID 
	 *
	 * type:VARCHAR,size:100,default:null,index,nullable
	 *
	 * @param mixed $socialID
	 * @return TimeteSocialMedia
	 */
	public function &setSocialID($socialID) {
		$this->notifyChanged(self::FIELD_SOCIALID,$this->socialID,$socialID);
		$this->socialID=$socialID;
		return $this;
	}

	/**
	 * get value for socialID 
	 *
	 * type:VARCHAR,size:100,default:null,index,nullable
	 *
	 * @return mixed
	 */
	public function getSocialID() {
		return $this->socialID;
	}

	/**
	 * set value for meidaType 0:Image, 1: Video
	 *
	 * type:TINYINT,size:3,default:null,index
	 *
	 * @param mixed $meidaType
	 * @return TimeteSocialMedia
	 */
	public function &setMeidaType($meidaType) {
		$this->notifyChanged(self::FIELD_MEIDATYPE,$this->meidaType,$meidaType);
		$this->meidaType=$meidaType;
		return $this;
	}

	/**
	 * get value for meidaType 0:Image, 1: Video
	 *
	 * type:TINYINT,size:3,default:null,index
	 *
	 * @return mixed
	 */
	public function getMeidaType() {
		return $this->meidaType;
	}

	/**
	 * set value for socialUrl 
	 *
	 * type:VARCHAR,size:500,default:null,nullable
	 *
	 * @param mixed $socialUrl
	 * @return TimeteSocialMedia
	 */
	public function &setSocialUrl($socialUrl) {
		$this->notifyChanged(self::FIELD_SOCIALURL,$this->socialUrl,$socialUrl);
		$this->socialUrl=$socialUrl;
		return $this;
	}

	/**
	 * get value for socialUrl 
	 *
	 * type:VARCHAR,size:500,default:null,nullable
	 *
	 * @return mixed
	 */
	public function getSocialUrl() {
		return $this->socialUrl;
	}

	/**
	 * set value for description 
	 *
	 * type:VARCHAR,size:500,default:null,nullable
	 *
	 * @param mixed $description
	 * @return TimeteSocialMedia
	 */
	public function &setDescription($description) {
		$this->notifyChanged(self::FIELD_DESCRIPTION,$this->description,$description);
		$this->description=$description;
		return $this;
	}

	/**
	 * get value for description 
	 *
	 * type:VARCHAR,size:500,default:null,nullable
	 *
	 * @return mixed
	 */
	public function getDescription() {
		return $this->description;
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
			self::FIELD_TYPE=>$this->getType(),
			self::FIELD_DATE=>$this->getDate(),
			self::FIELD_IMGURL=>$this->getImgUrl(),
			self::FIELD_IMGWIDTH=>$this->getImgWidth(),
			self::FIELD_IMGHEIGHT=>$this->getImgHeight(),
			self::FIELD_VIDEOURL=>$this->getVideoUrl(),
			self::FIELD_USERNAME=>$this->getUserName(),
			self::FIELD_RECORDJSON=>$this->getRecordJSON(),
			self::FIELD_USER_ID=>$this->getUserId(),
			self::FIELD_SOCIALID=>$this->getSocialID(),
			self::FIELD_MEIDATYPE=>$this->getMeidaType(),
			self::FIELD_SOCIALURL=>$this->getSocialUrl(),
			self::FIELD_DESCRIPTION=>$this->getDescription());
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
	 * Match by attributes of passed example instance and return matched rows as an array of TimeteSocialMedia instances
	 *
	 * @param PDO $db a PDO Database instance
	 * @param TimeteSocialMedia $example an example instance defining the conditions. All non-null properties will be considered a constraint, null values will be ignored.
	 * @param boolean $and true if conditions should be and'ed, false if they should be or'ed
	 * @param array $sort array of DSC instances
	 * @return TimeteSocialMedia[]
	 */
	public static function findByExample(PDO $db,TimeteSocialMedia $example, $and=true, $sort=null) {
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
	 * Will return matched rows as an array of TimeteSocialMedia instances.
	 *
	 * @param PDO $db a PDO Database instance
	 * @param array $filter array of DFC instances defining the conditions
	 * @param boolean $and true if conditions should be and'ed, false if they should be or'ed
	 * @param array $sort array of DSC instances
	 * @return TimeteSocialMedia[]
	 */
	public static function findByFilter(PDO $db, $filter, $and=true, $sort=null) {
		if (!($filter instanceof DFCInterface)) {
			$filter=new DFCAggregate($filter, $and);
		}
		$sql='SELECT * FROM `timete_social_media`'
		. self::buildSqlWhere($filter, $and, false, true)
		. self::buildSqlOrderBy($sort);

		$stmt=self::prepareStatement($db, $sql);
		self::bindValuesForFilter($stmt, $filter);
		return self::fromStatement($stmt);
	}

	/**
	 * Will execute the passed statement and return the result as an array of TimeteSocialMedia instances
	 *
	 * @param PDOStatement $stmt
	 * @return TimeteSocialMedia[]
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
	 * returns the result as an array of TimeteSocialMedia instances without executing the passed statement
	 *
	 * @param PDOStatement $stmt
	 * @return TimeteSocialMedia[]
	 */
	public static function fromExecutedStatement(PDOStatement $stmt) {
		$resultInstances=array();
		while($result=$stmt->fetch(PDO::FETCH_ASSOC)) {
			$o=new TimeteSocialMedia();
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
	 * Execute select query and return matched rows as an array of TimeteSocialMedia instances.
	 *
	 * The query should of course be on the table for this entity class and return all fields.
	 *
	 * @param PDO $db a PDO Database instance
	 * @param string $sql
	 * @return TimeteSocialMedia[]
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
		$sql='DELETE FROM `timete_social_media`'
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
		$this->setType($result['type']);
		$this->setDate($result['date']);
		$this->setImgUrl($result['imgUrl']);
		$this->setImgWidth($result['imgWidth']);
		$this->setImgHeight($result['imgHeight']);
		$this->setVideoUrl($result['videoUrl']);
		$this->setUserName($result['userName']);
		$this->setRecordJSON($result['recordJSON']);
		$this->setUserId($result['user_id']);
		$this->setSocialID($result['socialID']);
		$this->setMeidaType($result['meidaType']);
		$this->setSocialUrl($result['socialUrl']);
		$this->setDescription($result['description']);
	}

	/**
	 * Get element instance by it's primary key(s).
	 * Will return null if no row was matched.
	 *
	 * @param PDO $db
	 * @return TimeteSocialMedia
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
		$o=new TimeteSocialMedia();
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
		$stmt->bindValue(2,$this->getType());
		$stmt->bindValue(3,$this->getDate());
		$stmt->bindValue(4,$this->getImgUrl());
		$stmt->bindValue(5,$this->getImgWidth());
		$stmt->bindValue(6,$this->getImgHeight());
		$stmt->bindValue(7,$this->getVideoUrl());
		$stmt->bindValue(8,$this->getUserName());
		$stmt->bindValue(9,$this->getRecordJSON());
		$stmt->bindValue(10,$this->getUserId());
		$stmt->bindValue(11,$this->getSocialID());
		$stmt->bindValue(12,$this->getMeidaType());
		$stmt->bindValue(13,$this->getSocialUrl());
		$stmt->bindValue(14,$this->getDescription());
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
			$stmt->bindValue(1,$this->getType());
			$stmt->bindValue(2,$this->getDate());
			$stmt->bindValue(3,$this->getImgUrl());
			$stmt->bindValue(4,$this->getImgWidth());
			$stmt->bindValue(5,$this->getImgHeight());
			$stmt->bindValue(6,$this->getVideoUrl());
			$stmt->bindValue(7,$this->getUserName());
			$stmt->bindValue(8,$this->getRecordJSON());
			$stmt->bindValue(9,$this->getUserId());
			$stmt->bindValue(10,$this->getSocialID());
			$stmt->bindValue(11,$this->getMeidaType());
			$stmt->bindValue(12,$this->getSocialUrl());
			$stmt->bindValue(13,$this->getDescription());
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
		$stmt->bindValue(15,$this->getId());
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
		return self::hashToDomDocument($this->toHash(), 'TimeteSocialMedia');
	}

	/**
	 * get single TimeteSocialMedia instance from a DOMElement
	 *
	 * @param DOMElement $node
	 * @return TimeteSocialMedia
	 */
	public static function fromDOMElement(DOMElement $node) {
		$o=new TimeteSocialMedia();
		$o->assignByHash(self::domNodeToHash($node, self::$FIELD_NAMES, self::$DEFAULT_VALUES, self::$FIELD_TYPES));
			$o->notifyPristine();
		return $o;
	}

	/**
	 * get all instances of TimeteSocialMedia from the passed DOMDocument
	 *
	 * @param DOMDocument $doc
	 * @return TimeteSocialMedia[]
	 */
	public static function fromDOMDocument(DOMDocument $doc) {
		$instances=array();
		foreach ($doc->getElementsByTagName('TimeteSocialMedia') as $node) {
			$instances[]=self::fromDOMElement($node);
		}
		return $instances;
	}

}
?>