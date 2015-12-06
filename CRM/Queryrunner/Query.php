<?php

class CRM_Queryrunner_Query {

  var $version = 3;

  var $name = NULL;

  var $apiParams = array();

  var $remarks = array();

  /**
   * @param array $params
   */
  public function __construct($params) {
    foreach ($params as $name => $param) {
      $this->$name = $param;
    }

    // version is set to 3 by default - if different number
    // defined in params, it's replaced later on, however,
    // it's practically useles, since it seems none of api v2
    // will work properly in cron job setup. It might become
    // useful when/if api v4 starts to emerge and will need
    // testing in the cron job setup. To permanenty require
    // hardcoded api version, it's enough to move below line
    // under following if block.
    $this->apiParams = array('version' => $this->version);

    if (!empty($this->parameters)) {
      $lines = explode("\n", $this->parameters);

      foreach ($lines as $line) {
        $pair = explode("=", $line);
        if (empty($pair[0]) || empty($pair[1])) {
          $this->remarks[] .= 'Malformed parameters!';
          break;
        }
        $this->apiParams[trim($pair[0])] = trim($pair[1]);
      }
    }
  }

  public static function getQueryFrequency() {
    return array(
      FREQ_NEVER => 'Never',
      FREQ_HOURLY => 'Hourly',
      FREQ_DAILY => 'Daily',
      FREQ_WEEKLY => 'Weekly',
      FREQ_MONTHLY => 'Monthly',
      FREQ_YEARLY => 'Yearly',
      FREQ_ALWAYS => 'Always',
    );
  }

  public function __get($key) {
    switch ($key) {
      case 'freq_text':
        $freq = self::getQueryFrequency();
        return $freq[$this->run_frequency];
    }
  }

  /**
   * @param null $date
   */
  public function saveLastRun($date = NULL, $saveNext = false) {
    $dao = new CRM_Queryrunner_DAO_Query();
    $dao->id = $this->id;
    $dao->last_run = $this->last_run = (($date == NULL) ? CRM_Utils_Date::currentDBDate() : CRM_Utils_Date::currentDBDate($date));

    if ($saveNext)
      $this->saveNextRun($dao);
    else
      $dao->save();

    return strtotime($this->last_run);
  }

  public function saveNextRun($dao = null, $returnOnly = false) {

    if ($this->last_run) {

      switch ($this->run_frequency) {
        case FREQ_NEVER:
          $this->next_run = (1 << 31) - 1;
          break;
        case FREQ_HOURLY:
          $offset = '+1 hour';
          break;
        case FREQ_DAILY:
          $offset = '+1 day';
          break;
        case FREQ_WEEKLY:
          $offset = '+1 week';
          break;
        case FREQ_MONTHLY:
          $offset = '+1 month';
          break;
        case FREQ_YEARLY:
          $offset = '+1 year';
          break;
      }
      if (!empty($offset))
        $this->next_run = strtotime($offset, strtotime($this->last_run));
    }
    else $this->next_run = 0;

    if ($returnOnly)
      return $this->next_run;
    else {
      if (!$dao) {
        $dao = new CRM_Queryrunner_DAO_Query();
        $dao->id = $this->id;
      }
      $dao->next_run = $this->next_run;
      $dao->save();
    }
  }

  /**
   * @return bool
   */
  public function needsRunning() {
    static $now;

    if (!$now)
      $now = time();

    return $now >= $this->next_run;
  }

  public function __destruct() {
  }

}
