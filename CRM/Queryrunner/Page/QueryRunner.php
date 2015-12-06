<?php

/**
 * Page for displaying list of queries
 */
class CRM_Queryrunner_Page_QueryRunner extends CRM_Core_Page_Basic {

  /**
   * The action links that we need to display for the browse screen.
   *
   * @var array
   */
  static $_links = NULL;

  /**
   * Get BAO Name.
   *
   * @return string
   *   Classname of BAO.
   */
  public function getBAOName() {
    return 'CRM_Queryrunner_BAO_Query';
  }

  /**
   * Get action Links.
   *
   * @return array
   *   (reference) of action links
   */
  public function &links() {
    if (!(self::$_links)) {
      self::$_links = array(
        CRM_Core_Action::UPDATE => array(
          'name' => ts('Edit'),
          'url' => 'civicrm/query-runner',
          'qs' => 'action=update&id=%%id%%&reset=1',
          'title' => ts('Edit Query'),
        ),
        CRM_Core_Action::EXPORT => array(
          'name' => ts('Execute Now'),
          'url' => 'civicrm/query-runner',
          'qs' => 'action=export&id=%%id%%&reset=1',
          'title' => ts('Execute Query Now'),
        ),
        CRM_Core_Action::PREVIEW => array(
          'name' => ts('Cron / Drush'),
          'url' => 'civicrm/query-runner',
          'qs' => 'action=preview&id=%%id%%&reset=1',
          'title' => ts('View command to execute via cron / drush'),
        ),
        CRM_Core_Action::DISABLE => array(
          'name' => ts('Disable'),
          'ref' => 'crm-enable-disable',
          'title' => ts('Disable Query'),
        ),
        CRM_Core_Action::ENABLE => array(
          'name' => ts('Enable'),
          'ref' => 'crm-enable-disable',
          'title' => ts('Enable Query'),
        ),
        CRM_Core_Action::DELETE => array(
          'name' => ts('Delete'),
          'url' => 'civicrm/query-runner',
          'qs' => 'action=delete&id=%%id%%',
          'title' => ts('Delete Query'),
        ),
      );
    }
    return self::$_links;
  }

  /**
   * Run the page.
   *
   * This method is called after the page is created. It checks for the
   * type of action and executes that action.
   * Finally it calls the parent's run method.
   *
   * @return void
   */
  public function run() {
    CRM_Utils_System::setTitle(ts('Query Runner'));

    $this->_id = CRM_Utils_Request::retrieve('id', 'String',
      $this, FALSE, 0
    );
    $this->_action = CRM_Utils_Request::retrieve('action', 'String',
      $this, FALSE, 0
    );

    if ($this->_action == 'export') {
      $session = CRM_Core_Session::singleton();
      $session->pushUserContext(CRM_Utils_System::url('civicrm/queyr-runner', 'reset=1'));
    }

    return parent::run();
  }

  /**
   * Browse all jobs.
   *
   * @param null $action
   *
   * @return void
   */
  public function browse($action = NULL) {

    // using Export action for Execute. Doh.
    if ($this->_action & CRM_Core_Action::EXPORT) {
      $qm = new CRM_Queryrunner_QueryManager();
      $qm->execute($this->_id);

      $name = $qm->getNameFromId($this->_id);
      CRM_Core_Session::setStatus(ts("The $name query has been executed."), ts("Executed"), "success");
    }

    if (empty($qm))
      $qm = new CRM_Queryrunner_QueryManager();
    
    $rows = array();
    foreach ($qm->queries as $query) {
      $action = array_sum(array_keys($this->links()));

      if ($query->is_active)
        $action -= CRM_Core_Action::ENABLE;
      else
        $action -= CRM_Core_Action::DISABLE;

      $query->action = CRM_Core_Action::formLink(self::links(), $action,
        array('id' => $query->id),
        ts('more'),
        FALSE,
        'query.manage.action',
        'Query',
        $query->id
      );
      $query->next_run_date = date('Y-m-d G:i:s', $query->next_run);
      $query->freq_text = $query->freq_text;
      $query = get_object_vars($query);
      $rows[] = $query;

      if ($query['id'] == $this->_id)
        $this->assign('query', $query);
    }
    $this->assign('rows', $rows);

    if ($this->_action & CRM_Core_Action::PREVIEW) {
      $this->assign('server', isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
      $this->assign('doc_root', $_SERVER['DOCUMENT_ROOT']);
    }
  }

  /**
   * Get name of edit form.
   *
   * @return string
   *   Classname of edit form.
   */
  public function editForm() {
    return 'CRM_Queryrunner_Form_QueryRunner';
  }

  /**
   * Get edit form name.
   *
   * @return string
   *   name of this page.
   */
  public function editName() {
    return 'Queries';
  }

  /**
   * Get user context.
   *
   * @param null $mode
   *
   * @return string
   *   user context.
   */
  public function userContext($mode = NULL) {
    return 'civicrm/query-runner';
  }

}
