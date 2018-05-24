<?php

namespace Drupal\aidsfree_migrate_collections\sql;

use Drupal\Core\StringTranslation\StringTranslationTrait;

abstract class MigrateQuery {
  use StringTranslationTrait;
  protected $destinationField = [];
  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  public function __construct($database) {
    $this->database = $database;
  }

  public function fields(){
    $nodeFields = [
      'nid' => $this->t('Node id'),
      'title' => $this->t('Node title'),
    ];

    return $nodeFields;
  }
  public abstract function getDestinationField();
  public abstract function query($destinationField);
}