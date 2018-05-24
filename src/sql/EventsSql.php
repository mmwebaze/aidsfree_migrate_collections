<?php

namespace Drupal\aidsfree_migrate_collections\sql;


class EventsSql extends MigrateQuery {

  private $collections;

  public function __construct($database) {
    parent::__construct($database);
    $collection = $this->collectionConfig();
    $node = $this->fields();
    $query = $this->database->select($collection['data']['table'], $collection['data']['alias']);
    $query->innerJoin($node['data']['table'], $node['data']['alias'], 'n.nid = s.entity_id');
    $query->fields($collection['data']['alias'], $collection['fields'])
      ->fields($node['data']['alias'], $node['fields']);
    $this->collections = $query->execute();
  }
  public function collectionConfig(){
    $meetSpeaker = [
      'fields' =>['field_meet_the_speakers_value', 'bundle'],
      'data' => [
        'table' => 'field_data_field_meet_the_speakers',
        'alias' => 's']
    ];

    return $meetSpeaker;
  }
  public function fields(){
    $nodeFields = [
      'fields' => ['nid', 'title'],
      'data' => [
        'table' => 'node',
        'alias' => 'n'
      ]
    ];

    return $nodeFields;
  }

  public function query() {
    /*SELECT n.nid, n.title, s. FROM field_data_field_meet_the_speakers s
INNER JOIN node n ON (n.nid = s.entity_id)*/

  }
  public function getCollections(){
    return $this->collections;
  }
}