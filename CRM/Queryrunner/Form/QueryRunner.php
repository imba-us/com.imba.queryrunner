<?php
/**
 *
 */
class CRM_Queryrunner_Form_QueryRunner extends CRM_Admin_Form {
  protected $_id = NULL;

  public function preProcess() {

    parent::preProcess();

    CRM_Utils_System::setTitle(ts('Query Runner'));

    if ($this->_id) {
      $refreshURL = CRM_Utils_System::url('civicrm/query-runner',
        "reset=1&action=update&id={$this->_id}",
        FALSE, NULL, FALSE
      );
    }
    else {
      $refreshURL = CRM_Utils_System::url('civicrm/query-runner',
        "reset=1&action=add",
        FALSE, NULL, FALSE
      );
    }

    $this->assign('refreshURL', $refreshURL);
  }

  /**
   * Build the form object.
   *
   * @param bool $check
   *
   * @return void
   */
  public function buildQuickForm($check = FALSE) {
    parent::buildQuickForm();

    if ($this->_action & CRM_Core_Action::DELETE) {
      return;
    }

    $attributes = CRM_Core_DAO::getAttribute('CRM_Queryrunner_DAO_Query');

    $this->add('text', 'name', ts('Name'), $attributes['name'], TRUE);

    $this->addRule('name', ts('Name already exists in Database.'), 'objectExists', array(
        'CRM_Queryrunner_DAO_Query',
        $this->_id,
      ));

    $this->add('text', 'description', ts('Description'), $attributes['description']);

    $this->add('textarea', 'query', ts('Query'), "cols=80 rows=10", true);

    $this->add('select', 'run_frequency', ts('Run Frequency'), CRM_Queryrunner_Query::getQueryFrequency(), true);

    $this->addDateTime('scheduled_run_date', ts('Scheduled Run Date'), FALSE, array('formatType' => 'activityDateTime'));

    $this->add('checkbox', 'is_active', ts('Is this Query active?'));
  }

  /**
   * @return array
   */
  public function setDefaultValues() {
    $defaults = array();

    if (!$this->_id) {
      $defaults['is_active'] = $defaults['is_default'] = 1;
      return $defaults;
    }

    $dao = new CRM_Queryrunner_DAO_Query();
    $dao->id = $this->_id;
    if (!$dao->find(TRUE)) {
      return $defaults;
    }

    CRM_Core_DAO::storeValues($dao, $defaults);

    if ($ts = $defaults['scheduled_run']) {
      $defaults['scheduled_run_date'] = date('m/d/Y', $ts);
      $defaults['scheduled_run_date_time'] = date('h:iA', $ts);
    }

    $this->assign('query', $defaults);

    return $defaults;
  }

  /**
   * Process the form submission.
   *
   *
   * @return void
   */
  public function postProcess() {

    CRM_Utils_System::flushCache('CRM_Queryrunner_DAO_Query');

    if ($this->_action & CRM_Core_Action::DELETE) {
      CRM_Queryrunner_BAO_Query::del($this->_id);
      CRM_Core_Session::setStatus("", ts('Query Deleted.'), "success");
      return;
    }

    $values = $this->controller->exportValues($this->_name);
    $ts = strtotime(trim("{$values['scheduled_run_date']} {$values['scheduled_run_date_time']}"));

    $dao = new CRM_Queryrunner_DAO_Query();

    $dao->id = $this->_id;
    $dao->name = $values['name'];
    $dao->machine_name = strtolower(CRM_Utils_String::munge($dao->name, '_', null));
    $dao->description = $values['description'];
    $dao->query = $values['query'];
    $dao->run_frequency = $values['run_frequency'];
    $dao->is_active = CRM_Utils_Array::value('is_active', $values, 0);
    $dao->scheduled_run = $ts ?: 0;
    $dao->save();
  }

}
