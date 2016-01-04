<?php

class CRM_Queryrunner_Query {

  public function __construct($params) {
    foreach ($params as $name => $param) {
      $this->$name = $param;
    }
  }

  public static function getQueryFrequency() {
    return array(
      'Never' => ts('Never'),
      'Hourly' => ts('Hourly'),
      'Daily' => ts('Daily'),
      'Weekly' => ts('Weekly'),
      'Monthly' => ts('Monthly'),
      'Quarter' => ts('Quarterly'),
      'Yearly' => ts('Yearly'),
      'Always' => ts('Always'),
    );
  }

  public function saveLastRun($ts = 0, $scheduled = FALSE) {
    $dao = new CRM_Queryrunner_DAO_Query();
    $dao->id = $this->id;
    $dao->last_run = $this->last_run = ($ts ?: time());

    if ($scheduled) {
      $dao->scheduled_run = 0;
    }

    $dao->save();

    return $this->last_run;
  }

  public function needsRunning() {
    static $now;

    if (!$now)
      $now = time();

    // a scheduled run overrides run_frequency
    if ($this->scheduled_run) {
      if ($now >= $this->scheduled_run) {
        $this->saveLastRun($now, TRUE);
        return TRUE;
      }
      return FALSE;
    }

    // run if never run
    if (!$this->last_run)
      return TRUE;

    // now let run_frequency decide
    switch ($this->run_frequency) {

        case 'Never':
          return FALSE;

        case 'Always':
          return TRUE;

        case 'Yearly':
          $offset = '+1 year';
          break;

        case 'Quarter':
          $offset = '+3 months';
          break;

        case 'Monthly':
          $offset = '+1 month';
          break;

        case 'Weekly':
          $offset = '+1 week';
          break;

        case 'Daily':
          $offset = '+1 day';
          break;

        case 'Hourly':
          $offset = '+1 hour';
          break;
    }

    $next_run = strtotime($offset, $this->last_run);

    return $now >= $next_run;
  }

}
