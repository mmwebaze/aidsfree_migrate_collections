<?php
use \Drupal\paragraphs\Entity\Paragraph;
use \Drupal\aidsfree_migrate_collections\sql\TreatmentGuidelineSql;
use \Drupal\aidsfree_migrate_collections\sql\EventsSql;
use \Drupal\aidsfree_migrate_collections\sql\EventCollections;
use \Drupal\aidsfree_migrate_collections\sql\NodeHtskb;

/**
 * Implements hook_drush_command().
 */
function aidsfree_migrate_collections_drush_command(){
  $items = array();
  $items['aidsfree_migrate_collections-update'] = [
    'description' => 'Test remote database connect',
    'arguments' => [
      'collection_type' => 'The collection type to machine name.',
    ],
    'aliases' => ['amcdb'],
    'examples' => [
      'drush amcdb treatment' => 'Updates treatment guidelines.',
      'drush amcdb event' => 'Updates Events.',
    ],
  ];
  $items['aidsfree_migrate_collections-nodes'] = [
    'description' => 'migrates a specific node',
    'arguments' => [
      'node_type' => 'The node type machine name.',
    ],
    'aliases' => ['amckb'],
    'examples' => [
      'drush amckb hts_kb_summaries' => 'Migrates HTS KB content type.',
    ],
  ];
  return $items;
}

function drush_aidsfree_migrate_collections_nodes($arg = NULL){
  drush_print(dt('Migrating nodes \''.$arg.'\'. from D7 to D8.'));
  $supportedNodeTypes = ['hts_kb_summaries'];

  $db = NULL;
  if(isset($arg)){
    if(in_array($arg, $supportedNodeTypes)){
      $db = getDatabaseConnection('migrate');
    }
  }
  switch ($arg){
    case 'hts_kb_summaries':
      drush_print(dt('Start migration'));
      $tbsKbNode = new NodeHtskb($db);
      $results = $tbsKbNode->query('field_regimen_options')->execute();
      resetConnection();

      foreach ($results as $result){
        //print_r($result);
        createNode($result, $tbsKbNode);
      }
      break;
    default:
      drush_print(dt('Content Type \''.$arg.'\' not supported. Possible types: '.implode(',', $supportedNodeTypes)));
  }



}
function drush_aidsfree_migrate_collections_update($arg = NULL){
  $supportedCollections = ['treatment', 'events'];
  $db = NULL;
  if(isset($arg)){
    if(in_array($arg, $supportedCollections)){

      $db = getDatabaseConnection('migrate');
    }
  }

  switch ($arg){
    case 'treatment':
      drush_print(dt('Updating Collection \''.$arg.'\'. to Paragraph.'));
      $treatmentGuideline = new TreatmentGuidelineSql($db);
      $results = $treatmentGuideline->query('field_regimen_options')->execute();
      resetConnection();
      foreach ($results as $result){
        //print_r($result->field_first_line_value);die();
        updateNode($result->bundle, $result, 'field_regimen_options');
        //print($result->bundle.",".$result->nid.",".$result->title."\n");
      }
      break;
    case 'events':
      drush_print(dt('Updating Collection \''.$arg.'\'. to Paragraph.'));

      $event = new EventCollections($db);

      $fields = ['field_meet_the_speakers', 'field_presentations', 'field_video_embed'];
      /*$node=getNode(18286);
      print_r($node->toArray()['field_presentations']);
      die();*/

      foreach ($fields as $field){
        $results=$event->query($field)->execute();
        resetConnection();

        foreach ($results as $result){

          updateNode($result->bundle, $result, $event->getDestinationField());
        }

      }
      //$results = $event->query('field_meet_the_speakers')->execute();


      //$results=$event->query('field_video_embed')->execute();

      //$node = getNode(17856, 'nid');
      //print_r($node->toArray());

      break;

    default:

      drush_print(dt('Collection \''.$arg.'\' not supported. Possible types: '.implode(',', $supportedCollections)));
  }


}
function getDatabaseConnection($key='default'){
  \Drupal\Core\Database\Database::setActiveConnection($key);
  $db = \Drupal\Core\Database\Database::getConnection();

  return $db;
}
function resetConnection(){
  \Drupal\Core\Database\Database::setActiveConnection();
}
function updateNode($bundle, $result, $destinationField){

  //$paragraphField = null;
  $node_storage = \Drupal::entityTypeManager()->getStorage('node');
  $node = $node_storage->load($result->nid);

  if ($bundle === 'treatment_guidelines'){
   // $paragraphField = $destinationField;

    if(isset($node)){
      $node->get($destinationField)->getValue();
      $regimenOptions = $node->get($destinationField)[0];

      if (isset($regimenOptions)){
        $target_id = $node->get($destinationField)->getValue()[0]['target_id'];
        $paragraph = Paragraph::load($target_id);
        print($result->nid.", ".$paragraph->id()."\n");
      }
      else{

        $paragraph = Paragraph::create([
          'type' => 'regimen',
          'field_first_line' => array(
            "value"  =>  $result->field_first_line_value,
            "format" => $result->field_first_line_format
          ),
          'field_second_line' => array(
            "value"  =>  $result->field_second_line_value,
            "format" => $result->field_second_line_format
          ),
          'field_third_line' => array(
            "value"  =>  $result->field_third_line_value,
            "format" => $result->field_third_line_format
          ),
        ]);
        $paragraph->save();

        $current = array(
          array(
            'target_id' => $paragraph->id(),
            'target_revision_id' => $paragraph->getRevisionId()
          )
        );
        $node->set($destinationField, $current);

        $node->save();
      }
    }
    else{
      print('No such node '.$result->nid."\n");
    }
  }
  elseif ($bundle === 'events'){
    //$node = getNodeByTitle($result->title);
    $node = getNode($result->title, 'events', 'title');
    if(isset($node)){
      //$node->get($destinationField)->getValue();
      $eventsFields = $node->get($destinationField)[0];

      /*if (isset($eventsFields)){
        $target_id = $node->get($destinationField)->getValue()[0]['target_id'];

        $paragraph = Paragraph::load($target_id);
        print($result->nid.", ".$paragraph->id()."\n");
      }*/
     // else{
        //create paragraph

      $newParagraph = null;
      if ($destinationField == 'field_meet_the_speakers'){
        print($node->id().'----'.$result->title." field_meet_the_speakers \n");
        $newParagraph = [
          'type' => 'meet_the_speakers',
          'field_bio' => array(
            "value"  =>  $result->field_bio_value,
            "format" => 'full_html',
          ),
          'field_speakername' => array(
            "value"  =>  $result->field_speakername_value,
          ),
          'field_headshot' => array(
            "target_id" => $result->field_headshot_fid,
            "alt" => $result->field_headshot_alt,
            "width" => $result->field_headshot_width,
            "height" => $result->field_headshot_height,
          ),
        ];
        if(!$result->field_headshot_fid){
          print('I have noooo value'."\n");
          unset($newParagraph['field_headshot']);
        }
        $paras = $node->get('field_meet_the_speakers')->getValue();
      }
      elseif ($destinationField == 'field_presentations'){
        print($node->id().'----'.$result->title." field_presentations \n");
        $newParagraph = [
          'type' => 'presentations',
          'field_author' => array(
            "value"  =>  $result->field_author_s__value,
            "format" => 'full_html',
          ),
          'field_day' => array(
            "value"  =>  explode('T',$result->field_day_value)[0],
          ),
          'field_file' => array(
            "target_id" => $result->field_file_fid,
            "display" => $result->field_file_display,
            "description" => $result->field_file_description,
          ),
          'field_session' => array(
            "value"  =>  $result->field_session_value,
          ),
          'field_link' => array(
            "uri"  =>  $result->field_websitelink_url,
            "title" => $result->field_websitelink_title,
          ),
        ];
        if(!$result->field_file_fid){
          print('I have noooo value--field_file_fid'."\n");
          unset($newParagraph['field_file']);
        }
        if(!$result->field_day_value){
          print('I have noooo value--field_day'."\n");
          unset($newParagraph['field_day']);
        }
        else{

          print($node->id()."--".$result->field_day_value."\n");
          print($node->id()."--".explode('T',$result->field_day_value)[0]."\n");
        }
        $paras = $node->get($destinationField)->getValue();
      }
      elseif ($destinationField == 'field_video_embed'){
        $newParagraph = [
          'type' => 'video',
          /*'field_link' => array(
            "uri"  =>  $result->field_author_s__value,
            "title" => 'full_html',
            //"options"  =>  $result->field_author_s__value,
          ),*/
          'field_transcript' => array(
            "target_id"  =>  $result->field_transcript_fid,
            "display" => $result->field_transcript_display,
            "description" => $result->field_transcript_description,
          ),
          'field_video_embed' => array(
            "value" => $result->field_video_video_url,
          ),
        ];
        $paras = $node->get($destinationField)->getValue();
      }
      else{
        print(" node with title is null-> ".$result->title."\n");
      }

        $paragraph = Paragraph::create($newParagraph);
        $paragraph->save();

        //print($paragraph->id().' ->) node '.$node->id().' field: '.$destinationField." \n");
        $current = array(
          //array(
            'target_id' => $paragraph->id(),
            'target_revision_id' => $paragraph->getRevisionId()
          //)
        );

        if (isset($paras)){
          array_push($paras, $current);
        }
        else{
          $paras=[];
          array_push($paras, $current);
        }
        $node->set($destinationField, $paras);
        $node->save();
      //}

    }
    else{
      print('No such node '.$result->nid."\n");
    }
  }
}
function getNodeByTitle($title){
  $nodes = \Drupal::entityTypeManager()
    ->getStorage('node')
    ->loadByProperties(['title' => $title]);
//print(array_keys($nodes)[0]." id \n");die('kkk');
  return array_values($nodes)[0];
}
function getNode($value, $type, $field = 'nid'){
  $node_storage = \Drupal::entityTypeManager()->getStorage('node');
  $id=$node_storage->getQuery()->condition('type', $type,'=')
    ->condition($field, $value,'=')
      //->condition('nid',956,'=')
    ->execute();
  $nodes=$node_storage->loadMultiple($id);

  return array_values($nodes)[0];
}
function createNode($result, NodeHtskb $queryObject){

  $node = getNode($result->nid, $result->type);
  print($result->field_hts_kb_type_tid.' New TID: '.($result->field_hts_kb_type_tid == 2216 ? 2178: 2179)."\n");

  if (!isset($node)){

    $newNode = [
      'type' => $result->type,
      'nid' => $result->nid,
      'vid' => $result->vid,
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
      //'field_link' => ['uri' => $result->field_link_url, 'title' => $result->field_link_title],
      'field_link' => $queryObject->getResults($result->nid, 'link'),
      'field_hts_kb_type' => ['target_id' => $result->field_hts_kb_type_tid == 2216 ? 2178: 2179],
      'field_hts_kb_section' => $queryObject->getResults($result->nid, 'section'),
    ];
    $node = \Drupal\node\Entity\Node::create($newNode);
    $node->save();
    print_r($result->nid.' node has been created '."\n");
  }
  else{
    print('Node with id '.$result->nid." has already been created \n");
  }
}