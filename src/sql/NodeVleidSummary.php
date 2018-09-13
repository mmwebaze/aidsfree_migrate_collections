<?php

namespace Drupal\aidsfree_migrate_collections\sql;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use \Drupal\Core\Database\Database;


class NodeVleidSummary {
  use StringTranslationTrait;
  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  public function __construct($database) {
    $this->database = $database;
  }
  private function fields(){
    $nodeFields = [
      'nid' => $this->t('Node id'),
      'title' => $this->t('Node title'),
      'type' => $this->t('The type of this node.'),
      'uid' => $this->t('The user id that owns this node'),
      'status' => $this->t('Boolean indicating whether node is published'),
      'created' => $this->t('The Unix timestamp when the node was created.'),
      'changed' => $this->t('The Unix timestamp when the node was most recently saved.'),
       // 'status' => $this->t('The user id that owns this node'),
    ];

    return $nodeFields;
  }
  public function query(){
    $query =  $this->database->select('node', 'n');
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
    $query->condition('n.type', 'vl_eid_summaries', '=');

    return $query->execute();
  }
  public function create($result){
    //$results = $this->query();

    //foreach ($results as $result){
      print($result->nid.' - '.$result->title."\n");
      $newNode = array(
        'type' => $result->type,
        // 'nid' => $result->nid,
        // 'vid' => $result->vid,
        'uid' => $result->uid,
        'title' => $result->title,
        'status' => 1,
        'created' => $result->created,
        'changed' => $result->changed,
        'body' => ['value' => $result->body_value, 'format' => $result->body_format],
        'field_citation' => ['value' => $result->field_citation_value, 'format' => $result->field_citation_format],
        'field_yeartext' => ['value' => $result->field_yeartext_value == 'n.d.'? '' : $result->field_yeartext_value],
        'field_new' => ['value' => $result->field_new_value],
        'field_order' => ['value' => $result->field_order_value],
        'field_link' => $this->getResults($result->nid, 'link'),
        'field_vl_eid_type' => $this->getResults($result->nid, 'field_vl_eid_type'),
      );

      $node = \Drupal\node\Entity\Node::create($newNode);
      $node->save();
   // }

  }
  public function getResults($nid, $field) {
    if ($field == 'link') {
      $query = $this->database->select('field_data_field_link', 'l');
      $query->fields('l', ['field_link_url', 'field_link_title']);
      $query->condition('l.entity_id', $nid, '=');
      $results = $query->execute();
      Database::setActiveConnection();
      $links = [];
      foreach ($results as $result) {
        //print_r($result->field_link_url.'--- '.$result->field_link_title."\n");
        array_push($links, [
          'uri' => $result->field_link_url,
          'title' => $result->field_link_title
        ]);
      }
      return $links;
    }
    else if ($field == 'field_vl_eid_type'){
      $query = $this->database->select('field_data_field_vl_eid_type', 't');
      $query->fields('t', ['field_vl_eid_type_tid']);
      $query->condition('t.entity_id', $nid, '=');
      $results = $query->execute();
      Database::setActiveConnection();
      $vleidType = [];
      foreach ($results as $result) {
        array_push($vleidType, ['target_id' => $result->field_vl_eid_type_tid]);
      }
      return $vleidType;
    }
    else{

    }
  }
}