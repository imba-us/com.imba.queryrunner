<?php

class CRM_Queryrunner_QueryManager {

  public $queries = null;

  public function __construct() {
    $queries = array();
    $dao = new CRM_Queryrunner_DAO_Query();
    $dao->orderBy('name');
    $dao->find();
    while ($dao->fetch()) {
      $temp = array();
      CRM_Core_DAO::storeValues($dao, $temp);
      $queries[$dao->id] = new CRM_Queryrunner_Query($temp);
    }
    $this->queries = $queries;
  }

  private function _execute($query, $force = FALSE) {
    static $date;

    if ($force || ($query->is_active && $query->needsRunning())) {

      $start = microtime(TRUE);
      $dao = CRM_Core_DAO::executeQuery($query->query);
      $finish = microtime(TRUE);

      $execute = $finish - $start;
      $time = array();

      if ($execute >= 3600) {
        $time[] = ((int) ($execute / 3600)) . 'h';
        $execute = $execute % 3600;
      }
      if ($execute >= 60) {
        $time[] = ((int) ($execute / 60)) . 'm';
        $execute = $execute % 60;
      }
      if ($execute > 1) {
        $time[] = number_format($execute, 4) . 's';
      }
      else {
        $time[] = number_format($execute * 1000, 4) . 'ms';
      }

      $time = implode(' ', $time);

      echo "$query->name: {$dao->affectedRows()} row(s) affected, $time\n";

      $date = $query->saveLastRun($date);

      return TRUE;
    }
    return FALSE;
  }

  public function execute($params, $force = FALSE) {
    $executed = FALSE;
    
    ob_start();

    echo "Executing Queries...\n";
    
    if (!empty($params['name'])) {
      foreach($this->queries as $query) {
        if ($query->machine_name == $params['name']) {
          $params = $query->id;
          $force = TRUE;
          break;
        }
      }
    }
    if (is_numeric($params)) {
      if (!empty($this->queries[$params])) {
        $query = $this->queries[$params];
        $executed = $this->_execute($query, $force);
      }
    }
    else {
      foreach($this->queries as $query) {
        $result = $this->_execute($query, $force);
        $executed = $executed || $result;
      }
    }

    $output = ob_get_clean();

    return $executed ? $output : 'No queries were scheduled to run.';
  }

  public function getNameFromId($id) {
    return $this->queries[$id]->name;
  }

}