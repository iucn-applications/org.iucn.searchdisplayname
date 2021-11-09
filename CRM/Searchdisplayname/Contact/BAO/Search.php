<?php

class CRM_Searchdisplayname_Contact_BAO_Search extends CRM_Contact_BAO_Query_Interface {

  /**
   * static field for all the export/import hrjob fields
   *
   * @var array
   * @static
   */
  public static $_fields = array();

  /**
   * Function get the import/export fields for hrjob
   *
   * @return array self::$_hrjobFields  associative array of hrjob fields
   * @static
   */
  public function &getFields() {
    return self::$_fields;
  }

  public function select(&$query) {
  }

  public function where(&$query) {
    $grouping = NULL;
    foreach (array_keys($query->_params) as $id) {
      if (empty($query->_params[$id][0])) {
        continue;
      }

      // If searching by sort name: add " OR display_name "
      if ($query->_params[$id][0] == 'sort_name') {
        $this->whereClauseSingle($query->_params[$id], $query);
      }
    }
  }

  public function whereClauseSingle(&$values, &$query) {

    // Replace existing sort name search to include display name

    list($name, $op, $value, $grouping, $wildcard) = $values;

    $value = is_array($value) ? $value[$op] : $value;

    if ($wildcard) {
      $strSearch = "%$value%";
    }
    else {
      $strSearch = "$value%";
    }

    $sort_name_search = '( contact_a.sort_name LIKE \''.$strSearch.'\' )';
    $display_name_replace = ' ' . $sort_name_search . ' OR ( contact_a.display_name LIKE \''.$strSearch.'\' ) ';
    if( preg_match($sort_name_search, $query->_where[$grouping][$grouping]) ){
      $query->_where[$grouping][$grouping] = preg_replace($sort_name_search, $display_name_replace, $query->_where[$grouping][$grouping]);
    }

  }

  public function from($name, $mode, $side) {
  }

  public function setTableDependency(&$tables) {
  }

  public function registerAdvancedSearchPane(&$panes) {
  }

  public function getPanesMapper(&$panes) {
  }

  public function buildAdvancedSearchPaneForm(&$form, $type) {
  }

  public function setAdvancedSearchPaneTemplatePath(&$paneTemplatePathArray, $type) {
  }

  public function alterSearchBuilderOptions(&$apiEntities, &$fieldOptions) {
  }

}
