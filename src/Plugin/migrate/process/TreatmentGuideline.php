<?php
namespace Drupal\aidsfree_migrate_collections\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Treatment guidelines process plugin
 *
 * @MigrateProcessPlugin(
 *   id = "treatment_guide_regimen_pro"
 * )
 *
 */
class TreatmentGuideline extends ProcessPluginBase{
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property){

    return $value;
  }
}