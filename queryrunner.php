<?php

require_once 'queryrunner.civix.php';

define('FREQ_NEVER', 0);
define('FREQ_HOURLY', 1);
define('FREQ_DAILY', 2);
define('FREQ_WEEKLY', 3);
define('FREQ_MONTHLY', 4);
define('FREQ_YEARLY', 5);
define('FREQ_ALWAYS', 6);

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function queryrunner_civicrm_config(&$config) {
  _queryrunner_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function queryrunner_civicrm_xmlMenu(&$files) {
  _queryrunner_civix_civicrm_xmlMenu($files);
}


function queryrunner_civicrm_navigationMenu(&$params) {
  $menu_item_search = array('url' => 'civicrm/query-runner');
  $menu_items = array();
  CRM_Core_BAO_Navigation::retrieve($menu_item_search, $menu_items);

  if (!empty($menu_items))
    return;

  $navId = CRM_Core_DAO::singleValueQuery('SELECT MAX(id) FROM civicrm_navigation') + 1;
  $adminID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'Administer', 'id', 'name');
  $systemID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'System settings', 'id', 'name');

  $params[$adminID]['child'][$systemID]['child'][$navId] = array(
    'attributes' => array(
      'label' => ts('Query Runner'),
      'name' => 'Query Runner',
      'url' => 'civicrm/query-runner',
      'permission' => 'administer CiviCRM',
      'operator' => 'AND',
      'separator' => null,
      'parentID' => $systemID,
      'navID' => $navId,
      'active' => 1,
    ),
  );
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function queryrunner_civicrm_install() {
  _queryrunner_civix_civicrm_install();

  $result = civicrm_api3('Job', 'create', array(
    'sequential' => 1,
    'run_frequency' => "Always",
    'name' => "Query Runner",
    'api_entity' => "Query",
    'api_action' => "execute",
    'description' => "Execute queries defined by Query Runner",
    'is_active' => 1,
  ));

  CRM_Core_DAO::executeQuery("CREATE TABLE IF NOT EXISTS `query_runner` (
                            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                            `name` varchar(255) NOT NULL DEFAULT '',
                            `machine_name` varchar(255) NOT NULL DEFAULT '',
                            `description` varchar(255) NOT NULL,
                            `query` text NOT NULL,
                            `run_frequency` tinyint(4) NOT NULL DEFAULT '0',
                            `last_run` timestamp NULL DEFAULT NULL,
                            `next_run` int(11) NOT NULL DEFAULT '0',
                            `is_active` tinyint(4) NOT NULL DEFAULT '1',
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `name` (`name`)
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function queryrunner_civicrm_uninstall() {
  _queryrunner_civix_civicrm_uninstall();

  $id = civicrm_api3('Job', 'getvalue', array(
    'sequential' => 1,
    'return' => "id",
    'name' => "Query Runner",
  ));
  if (is_numeric($id))
    $result = civicrm_api3('Job', 'delete', array(
      'sequential' => 1,
      'id' => $id,
    ));

  CRM_Core_DAO::executeQuery('DROP TABLE query_runner');
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function queryrunner_civicrm_enable() {
  _queryrunner_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function queryrunner_civicrm_disable() {
  _queryrunner_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function queryrunner_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _queryrunner_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function queryrunner_civicrm_managed(&$entities) {
  _queryrunner_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function queryrunner_civicrm_caseTypes(&$caseTypes) {
  _queryrunner_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function queryrunner_civicrm_angularModules(&$angularModules) {
_queryrunner_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function queryrunner_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _queryrunner_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function queryrunner_civicrm_preProcess($formName, &$form) {

}

*/
