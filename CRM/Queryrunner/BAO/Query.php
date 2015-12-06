<?php

/**
 * This class contains query related functions.
 */
class CRM_Queryrunner_BAO_Query extends CRM_Queryrunner_DAO_Query {

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Create the query
   *
   * @param array $params
   *   An assoc array of name/value pairs.
   *
   * @return CRM_Queryrunner_DAO_Query
   */
  public static function create($params) {
    $query = new CRM_Queryrunner_DAO_Query();
    $query->copyValues($params);
    return $query->save();
  }

  /**
   * Retrieve DB object based on input parameters.
   *
   * It also stores all the retrieved values in the default array.
   *
   * @param array $params
   *   (reference ) an assoc array of name/value pairs.
   * @param array $defaults
   *   (reference ) an assoc array to hold the flattened values.
   *
   * @return CRM_Queryrunner_DAO_Query|null
   *   object on success, null otherwise
   */
  public static function retrieve(&$params, &$defaults) {
    $query = new CRM_Queryrunner_DAO_Query();
    $query->copyValues($params);
    if ($query->find(TRUE)) {
      CRM_Core_DAO::storeValues($query, $defaults);
      return $query;
    }
    return NULL;
  }

  /**
   * Update the is_active flag in the db.
   *
   * @param int $id
   *   Id of the database record.
   * @param bool $is_active
   *   Value we want to set the is_active field.
   *
   * @return Object
   *   DAO object on sucess, null otherwise
   *
   */
  public static function setIsActive($id, $is_active) {
    return CRM_Core_DAO::setFieldValue('CRM_Queryrunner_DAO_Query', $id, 'is_active', $is_active);
  }

  /**
   * Function to delete the query
   *
   * @param $queryID
   *   ID of the query to be deleted.
   *
   * @return bool|null
   */
  public static function del($queryID) {
    if (!$queryID) {
      CRM_Core_Error::fatal(ts('Invalid value passed to delete function.'));
    }

    $dao = new CRM_Queryrunner_DAO_Query();
    $dao->id = $queryID;
    if (!$dao->find(TRUE)) {
      return NULL;
    }

    if ($dao->delete()) {
      return TRUE;
    }
  }

}
