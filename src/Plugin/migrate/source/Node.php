<?php

namespace Drupal\aidsfree_migrate_collections\Plugin\migrate\source;


use Drupal\migrate\Plugin\migrate\source\SqlBase;
/**
 * Aidsfree node from the d7 database
 *
 * @MigrateSource(
 *   id = "node_hts_kbs_src_plugin"
 * )
 *
 */
class Node extends SqlBase {
  /**
   * defines the fields in the node table
   *
   * @return array of fields for node table
   */
  public function fields(){
    $fields = [
      'nid' => $this->t('The primary identifier for a node.'),
      'vid' => $this->t('The current node revision version identifier.'),
      'type' => $this->t('The node_type type of this node.'),
      'title' => $this->t('The language of this node.'),
      'uid' => $this->t('The users uid that owns this node.'),
      'status' => $this->t('A boolean value see node table description.'),
      'created' => $this->t('Timestamp when the node was created.'),
      'changed' => $this->t('Timestamp when the node was most recently changed.'),
      'comment' => $this->t('A boolean value see node table description'),
      'promote' => $this->t('A boolean value see node table description'),
      'sticky' => $this->t('A boolean value see node table description.'),
      'tnid' => $this->t('The translation set id for this node.'),
      'translate' => $this->t('A boolean value see node table description.'),
    ];

    return $fields;
  }

  /**
   * @return array of node ids as defined in the node table.
   */
  public function getIds(){
    return [
      'nid' => [
        'type' => 'integer'
      ]
    ];
  }

  /**
   *
   *
   * @return results of inner join node and field_data_body tables
   */
  public function query(){
    $query =  $this->select('node', 'n');
    $query->innerJoin('field_data_body', 'b', 'n.nid = b.entity_id');
    $query->innerJoin('field_data_field_citation', 'c', 'n.nid = c.entity_id');
    $query->innerJoin('field_data_field_yeartext', 'y', 'n.nid = y.entity_id');
    $query->innerJoin('field_data_field_new', 'w', 'n.nid = w.entity_id');
    $query->leftJoin('field_data_field_order', 'o', 'n.nid = o.entity_id');
    $query->fields('n', array_keys($this->fields()))
      ->fields('b', ['body_value', 'body_summary', 'body_format'])
    ->fields('c', ['field_citation_value', 'field_citation_format'])
    ->fields('y', ['field_yeartext_value'])
    ->fields('w', ['field_new_value'])
    ->fields('o', ['field_order_value']);
    #$query->condition('n.type', 'about');

    return $query;
  }
}