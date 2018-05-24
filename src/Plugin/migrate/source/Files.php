<?php

namespace Drupal\aidsfree_migrate_collections\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
/**
 * Aidsfree collections from the d7 database
 *
 * @MigrateSource(
 *   id = "aidsfree_files",
 *   source_module = "aidsfree_migrate_collections",
 * )
 *
 */
class Files extends SqlBase{
  public function fields(){
    $fieldsManaged = [
      'fid' => $this->t('File ID..'),
      'uid' => $this->t('User uid associated with the file.'),
      'filename' => $this->t('The name of the file.'),
      'uri' => $this->t('URI to access the file.'),
      'filemime' => $this->t('The file MIME type.'),
      'filesize' => $this->t('Size of file in bytes.'),
      'timestamp' => $this->t('Unix timestamp when the file was added.'),
      'type' => $this->t('The type of this file.'),
    ];

    return $fieldsManaged;
  }
  public function getIds(){
    return [
      'fid' => [
        'type' => 'integer'
      ]
    ];
  }
  /**
   * {@inheritdoc}
   */
  public function query(){
    $query =  $this->select('file_managed', 'fm');
    $query->fields('fm', array_keys($this->fields()));

    return $query;
  }
}