<?php

class CRM_Queryrunner_QueryManager {

  public $queries = null;

  public function __construct() {
    $this->queries = $this->_getQueries();
  }

  private function _getQueries() {
    if (!$this->queries) {
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
    return $this->queries;
  }

  private function _execute($query) {
    static $date;

    if ($query->is_active && $query->needsRunning()) {

      $start = microtime(true);
      $dao = CRM_Core_DAO::executeQuery($query->query);
      $finish = microtime(true);

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
      if ($execute > 0)
        $time[] = $execute . 's';

      $time = implode(' ', $time);

      echo "$query->name: {$dao->affectedRows()} row(s) affected, $time\n";

      $date = $query->saveLastRun($date, true);

      return true;
    }
    return false;
  }

  public function execute($params) {
    $count = 0;
    
    ob_start();

    echo "Executing Queries...\n";
    
    if (!empty($params['name'])) {
      $field = 'machine_name';
      $value = $params['name'];
      $cron = true;
    }
    elseif (is_numeric($params)) {
      $field = 'id';
      $value = $params;
      $cron = false;
    }

    if (!empty($field)) {
      foreach($this->queries as $query) {
        if ($query->$field == $value) {
          if ($cron)
            $query->next_run = 0;
          if ($this->_execute($query))
            $count++;
        }
      }
    }
    else {
      foreach($this->queries as $query) {
        if ($this->_execute($query))
          $count++;
      }
    }
    $output = ob_get_clean();

    return $count ? $output : 'No queries were scheduled to run.';
  }

  public function getNameFromId($id) {
    foreach($this->queries as $query) {
      if ($query->id == $id)
        return $query->name;
    }
  }

  public function getNextRun($id) {
    foreach($this->queries as $query) {
      if ($query->id == $id) {
        return $query->saveNextRun(null, true);
      }
    }
  }
  
}