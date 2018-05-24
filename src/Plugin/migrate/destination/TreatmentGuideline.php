<?php

namespace Drupal\aidsfree_migrate_collections\Plugin\migrate\destination;

use Drupal\migrate\Plugin\migrate\destination\DestinationBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\destination\EntityContentBase;


/**
 * Node body field creation plugin
 *
 * @MigrateDestination(
 *   id = "treatment_guide_regimen_dest"
 * )
 *
 */
class TreatmentGuideline extends  EntityContentBase {
  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    $this->getEntity();
  }
  /**
   * {@inheritdoc}
   */
  /*public function getIds() {
    $ids['uid']['type'] = 'integer';
    $ids['module']['type'] = 'string';
    $ids['key']['type'] = 'string';
    return $ids;
  }*/
  /**
   * {@inheritdoc}
   */
  /*public function fields(MigrationInterface $migration = NULL) {
    return [
      'uid' => 'The user id.',
      'module' => 'The module name responsible for the settings.',
      'key' => 'The setting key to save under.',
      'settings' => 'The settings to save.',
    ];
  }*/
}