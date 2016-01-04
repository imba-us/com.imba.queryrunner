<?php

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';

class CRM_Queryrunner_DAO_Query extends CRM_Core_DAO
{
  /**
   * static instance to hold the table name
   *
   * @var string
   */
  static $_tableName = 'query_runner';
  /**
   * static instance to hold the field values
   *
   * @var array
   */
  static $_fields = null;
  /**
   * static instance to hold the keys used in $_fields for each field.
   *
   * @var array
   */
  static $_fieldKeys = null;
  /**
   * static instance to hold the FK relationships
   *
   * @var string
   */
  static $_links = null;
  /**
   * static instance to hold the values that can
   * be imported
   *
   * @var array
   */
  static $_import = null;
  /**
   * static instance to hold the values that can
   * be exported
   *
   * @var array
   */
  static $_export = null;
  /**
   * static value to see if we should log any modifications to
   * this table in the civicrm_log table
   *
   * @var boolean
   */
  static $_log = false;
  /**
   * Query Id
   *
   * @var int unsigned
   */
  public $id;
  /**
   * Name
   *
   * @var string
   */
  public $name;
  /**
   * Machine Name, used to run query via cron/drush
   *
   * @var string
   */
  public $machine_name;
  /**
   * Description of the query
   *
   * @var string
   */
  public $description;
  /**
   * The query
   *
   * @var string
   */
  public $query;
  /**
   * query run frequency
   *
   * @var string
   */
  public $run_frequency;
  /**
   * When was this query last run
   *
   * @var int
   */
  public $last_run;
  /**
   * Scheduled run
   *
   * @var int
   */
  public $scheduled_run;
  /**
   * Is query active
   *
   * @var int
   */
  public $is_active;

  function __construct()
  {
    $this->__table = 'query_runner';
    parent::__construct();
  }
  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  static function &fields()
  {
    if (!(self::$_fields)) {
      self::$_fields = array(
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Query ID') ,
          'description' => 'Query Id',
          'required' => true,
        ),
        'name' => array(
          'name' => 'name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Query Name') ,
          'description' => 'Title of the query',
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ),
        'machine_name' => array(
          'name' => 'machine_name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Machine Name') ,
          'description' => 'Used to run the query via cron/drush',
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ),
        'description' => array(
          'name' => 'description',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Query Description') ,
          'description' => 'Description of the query',
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ),
        'query' => array(
          'name' => 'query',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => ts('Query') ,
          'description' => 'The query to execute',
        ),
        'run_frequency' => array(
          'name' => 'run_frequency',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Run Frequency'),
          'description' => 'How often should this query be run'
        ),
        'last_run' => array(
          'name' => 'last_run',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Last Run') ,
          'description' => 'UNIX timestamp for when this query last ran',
          'default' => 'NULL',
        ),
        'scheduled_run' => array(
          'name' => 'scheduled_run',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Next Run'),
          'description' => 'UNIX timestamp for next execution'
        ),
        'is_active' => array(
          'name' => 'is_active',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Active'),
          'description' => 'Is the query active'
        ),
      );
    }
    return self::$_fields;
  }
  /**
   * Returns an array containing, for each field, the arary key used for that
   * field in self::$_fields.
   *
   * @return array
   */
  static function &fieldKeys()
  {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'id' => 'id',
        'name' => 'name',
        'machine_name' => 'machine_name',
        'description' => 'description',
        'query' => 'query',
        'run_frequency' => 'run_frequency',
        'last_run' => 'last_run',
        'scheduled_run' => 'scheduled_run',
        'is_active' => 'is_active',
      );
    }
    return self::$_fieldKeys;
  }
  /**
   * Returns the names of this table
   *
   * @return string
   */
  static function getTableName()
  {
    return self::$_tableName;
  }
  /**
   * Returns if this table needs to be logged
   *
   * @return boolean
   */
  function getLog()
  {
    return self::$_log;
  }
  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  static function &import($prefix = false)
  {
    return self::$_import;
  }
  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  static function &export($prefix = false)
  {
    return self::$_export;
  }
}
