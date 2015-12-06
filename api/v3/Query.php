<?php

function _civicrm_api3_query_create_spec(&$params) {
  $params['run_frequency']['api.required'] = 1;
  $params['name']['api.required'] = 1;
  $params['query']['api.required'] = 1;

  $params['is_active']['api.default'] = 1;
}

function civicrm_api3_query_create($params) {
  return _civicrm_api3_basic_create('CRM_Queryrunner_BAO_Query', $params);
}

function civicrm_api3_query_get($params) {
  return _civicrm_api3_basic_get('CRM_Queryrunner_BAO_Query', $params);
}

function civicrm_api3_query_delete($params) {
  _civicrm_api3_basic_delete('CRM_Queryrunner_BAO_Query', $params);
}

function civicrm_api3_query_execute($params) {
  $qm = new CRM_Queryrunner_QueryManager();
  return civicrm_api3_create_success($qm->execute($params));
}

function civicrm_api3_query_setvalue($params) {
  $params[$params['field']] = $params['value'];
  unset($params['field'], $params['value']);
  return civicrm_api3_query_create($params);
}


