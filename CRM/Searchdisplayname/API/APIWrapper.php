<?php

require_once 'api/Wrapper.php';

/**
 * Class CRM_Searchdisplayname_API_APIWrapper
 */
class CRM_Searchdisplayname_API_APIWrapper implements API_Wrapper {

  /**
   * @var CRM_Utils_API_ReloadOption
   */
  private static $_singleton = NULL;

  /**
   * @return CRM_Utils_API_ReloadOption
   */
  public static function singleton() {
    if (self::$_singleton === NULL) {
      self::$_singleton = new CRM_API_DisplayNameSearchAPIWrapper();
    }
    return self::$_singleton;
  }

  /**
   * @inheritDoc
   */
  public function fromApiInput($apiRequest) {
    return $apiRequest;
  }

  /**
   * @inheritDoc
   */
  public function toApiOutput($apiRequest, $result) {

    $search_term = @$apiRequest['params']['name'];

    if (strlen($search_term) > 0) {
      // Search for display name
      $display_name_results = civicrm_api3('Contact', 'get', array(
        'debug' => 1,
        'sequential' => 1,
        'return' => "sort_name, email",
        'display_name' => array('LIKE' => "%$search_term%"),
      ));

      // Compare results by id
      $result_ids = array_column($result['values'],'id');
      $display_name_ids = array_column($display_name_results['values'],'id');
      $display_results_to_add = array_diff($display_name_ids,$result_ids);


      // Increment count
      $result['count'] += count($display_results_to_add);

      // Add the display name results that weren't found before
      foreach ($display_results_to_add as $key => $id) {
        $api_result = $display_name_results['values'][$key];
        $result['values'][] = array(
          'id' => $api_result['contact_id'],
          'sort_name' => $api_result['sort_name'],
          'email' => $api_result['email'],
          'data' => $api_result['sort_name'] . " :: " . $api_result['email'],
        );
      }
    }

    return $result;
  }

}
