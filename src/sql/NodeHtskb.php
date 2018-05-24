<?php

namespace Drupal\aidsfree_migrate_collections\sql;

use \Drupal\Core\Database\Database;

class NodeHtskb extends MigrateQuery{
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
  public function getDestinationField(){}
  public function query($destinationField){
    $query =  $this->database->select('node', 'n');
    $query->innerJoin('field_data_body', 'b', 'n.nid = b.entity_id');
    $query->innerJoin('field_data_field_citation', 'c', 'n.nid = c.entity_id');
    $query->innerJoin('field_data_field_yeartext', 'y', 'n.nid = y.entity_id');
    $query->innerJoin('field_data_field_new', 'w', 'n.nid = w.entity_id');
    $query->innerJoin('field_data_field_hts_kb_type', 'k', 'n.nid = k.entity_id');
    $query->leftJoin('field_data_field_order', 'o', 'n.nid = o.entity_id');
    //$query->leftJoin('field_data_field_link', 'l', 'n.nid = l.entity_id');
    $query->fields('n', array_keys($this->fields()))
      ->fields('b', ['body_value', 'body_summary', 'body_format'])
      ->fields('c', ['field_citation_value', 'field_citation_format'])
      ->fields('y', ['field_yeartext_value'])
      ->fields('w', ['field_new_value'])
      ->fields('o', ['field_order_value'])
      //->fields('l', ['field_link_url', 'field_link_title'])
      ->fields('k', ['field_hts_kb_type_tid']);
    $query->condition('n.type', 'hts_kb_summaries', '=');

    return $query;
  }

  public function getResults($nid, $field){
    if ($field == 'link'){
      $query =  $this->database->select('field_data_field_link', 'l');
      $query->fields('l', ['field_link_url', 'field_link_title']);
      $query->condition('l.entity_id', $nid, '=');
      $results = $query->execute();
      Database::setActiveConnection();
      $links = [];
      foreach ($results as $result){
        //print_r($result->field_link_url.'--- '.$result->field_link_title."\n");
        array_push($links, ['uri' => $result->field_link_url, 'title' => $result->field_link_title]);
      }
      return $links;
    }
    else{
      $query =  $this->database->select('field_data_field_hts_kb_section', 's');
      $query->fields('s', ['field_hts_kb_section_tid	']);
      $query->condition('s.entity_id', $nid, '=');
      $results = $query->execute();
      Database::setActiveConnection();
      $sections = [];
      foreach ($results as $result){
        $tid = NULL;
        switch($result->field_hts_kb_section_tid){
          case 2191:
            $tid = 2172;
            break;
          case 2196:
            $tid = 2173;
            break;
          case 2201:
            $tid = 2174;
            break;
          case 2206:
            $tid = 2175;
            break;
          case 2211:
            $tid = 2176;
            break;
          case 2221:
            $tid = 2177;
            break;
        }
        //print_r($result->field_hts_kb_section_tid."\n");
        array_push($sections, ['target_id' => $tid]);
      }
      return $sections;
    }
  }
}