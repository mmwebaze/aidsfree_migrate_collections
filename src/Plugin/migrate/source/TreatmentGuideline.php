<?php

namespace Drupal\aidsfree_migrate_collections\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Aidsfree collections from the d7 database
 *
 * @MigrateSource(
 *   id = "treatment_guide_regimen_sql"
 * )
 *
 */
class TreatmentGuideline extends SqlBase{

  public function pathOptionFields(){
    $pathOptionFields = [
      'field_treatment_path_option_value' => $this->t('Path option value'),
      'bundle' => $this->t('Bundle associated with field collection'),
    ];

    return $pathOptionFields;
  }
  public function regimenFieds(){
    $regimenFields = [
      'field_regimen_value' => $this->t('Field regimen value'),
    ];

    return $regimenFields;
  }
  public function firstLineFields(){
    $firstLine = [
      'field_first_line_value' => $this->t('First line value'),
      'field_first_line_format' => $this->t('First line value format'),
    ];

    return $firstLine;
  }
  public function secondLineFields(){
    $secondLine = [
      'field_second_line_value' => $this->t('Second line value'),
      'field_second_line_format' => $this->t('Second line value format'),
    ];

    return $secondLine;
  }
  public function thirdLineFields(){
    $thirdLine = [
      'field_third_line_value' => $this->t('Third line value'),
      'field_third_line_format' => $this->t('Third line value format'),
    ];

    return $thirdLine;
  }
  public function fields(){
    $nodeFields = [
      'nid' => $this->t('Node id'),
      'title' => $this->t('Node title'),
    ];

    return $nodeFields;
  }
  public function getIds(){
    return [
      'nid' => [
        'type' => 'integer'
      ]
    ];
  }
  /**
   * {@inheritdoc}
   */
  public function query(){
    $query =  $this->select('field_data_field_treatment_path_option', 'fdpo');
    $query->innerJoin('field_data_field_regimen ', 'dffr', 'fdpo.field_treatment_path_option_value = dffr.entity_id');
    $query->innerJoin('node ', 'n', 'n.nid = fdpo.entity_id');
    $query->innerJoin('field_data_field_first_line ', 'fl', 'dffr.field_regimen_value = fl.entity_id');
    $query->innerJoin('field_data_field_second_line ', 'sl', 'dffr.field_regimen_value = sl.entity_id');
    $query->innerJoin('field_data_field_third_line ', 'tl', 'dffr.field_regimen_value = tl.entity_id');
    $query->fields('fdpo', array_keys($this->pathOptionFields()))
      ->fields('n', array_keys($this->fields()))
      ->fields('dffr', array_keys($this->regimenFieds()))
      ->fields('fl',array_keys($this->firstLineFields()))
      ->fields('sl',array_keys($this->secondLineFields()))
      ->fields('tl',array_keys($this->thirdLineFields()));

    return $query;
  }
}