<?php

namespace Drupal\aidsfree_migrate_collections\sql;


class EventCollections extends MigrateQuery{

  public function __construct($database) {
    parent::__construct($database);
  }
  public function getDestinationField(){
    return $this->destinationField;
  }
  public function query($destinationField) {
    $this->destinationField = $destinationField;
    if ($this->destinationField == 'field_meet_the_speakers'){
      return $this->meetTheSpeakerQuery();
    }
    elseif ($this->destinationField == 'field_presentations'){
      return $this->presentationsCollection();
    }
    elseif ($this->destinationField == 'field_video_embed'){
      return $this->videoCollection();
    }
    else{
      die("Migration does not support this field yet.");
    }
  }
  private function meetTheSpeakerQuery(){
    $query =  $this->database->select('field_data_field_meet_the_speakers', 's');
    $query->innerJoin('node ', 'n', 'n.nid = s.entity_id');
    $query->leftJoin('field_data_field_headshot ', 'hs', 'hs.entity_id = s.field_meet_the_speakers_value');
    $query->leftJoin('field_data_field_speakername ', 'sn', 'sn.entity_id = s.field_meet_the_speakers_value');
    $query->leftJoin('field_data_field_bio ', 'b', 'b.entity_id = s.field_meet_the_speakers_value');
    $query->fields('n', $this->fields()['fields'])
      ->fields('s', ['field_meet_the_speakers_value', 'bundle'])
      ->fields('hs', ['field_headshot_fid', 'field_headshot_alt', 'field_headshot_title', 'field_headshot_width', 'field_headshot_height'])
      ->fields('sn', ['field_speakername_value','field_speakername_format'])
      ->fields('b', ['field_bio_value', 'field_bio_format']);

    return $query;
  }
  private function presentationsCollection(){
    $query =  $this->database->select('field_data_field_presentations', 'p');
    $query->innerJoin('node ', 'n', 'n.nid = p.entity_id');
    $query->leftJoin('field_data_field_file ', 'ff', 'ff.entity_id = p.field_presentations_value');
    $query->leftJoin('field_data_field_author_s_ ', 'au', 'au.entity_id = p.field_presentations_value');
    $query->leftJoin('field_data_field_session ', 'fs', 'fs.entity_id = p.field_presentations_value');
    $query->leftJoin('field_data_field_day ', 'fd', 'fd.entity_id = p.field_presentations_value');
    $query->leftJoin('field_data_field_websitelink ', 'fl', 'fl.entity_id = p.field_presentations_value');
    $query->fields('n', $this->fields()['fields'])
      ->fields('p', ['field_presentations_value', 'bundle'])
      ->fields('ff', ['field_file_fid', 'field_file_display', 'field_file_description'])
      ->fields('au', ['field_author_s__value', 'field_author_s__format'])
      ->fields('fs', ['field_session_value', 'field_session_format'])
      ->fields('fd', ['field_day_value'])
      ->fields('fl', ['field_websitelink_url', 'field_websitelink_title']);

    return $query;
  }
  private function videoCollection(){
print('Run'."\n");
    $query =  $this->database->select('field_data_field_video_s_', 'vs');
    $query->leftJoin('node', 'n', 'n.nid = vs.entity_id');
    $query->leftJoin('field_data_field_transcript', 'ft', 'ft.entity_id = vs.field_video_s__value');
    $query->leftJoin('field_data_field_video', 'fv', 'fv.entity_id = vs.field_video_s__value');
    $query->fields('n', $this->fields()['fields'])
      ->fields('fv', ['field_video_video_url', 'field_video_thumbnail_path','field_video_video_data', 'field_video_description'])
      ->fields('ft', ['field_transcript_fid', 'field_transcript_display', 'field_transcript_description'])
      ->fields('vs', ['field_video_s__value', 'bundle']);

    return $query;
  }
  public function fields(){
    $nodeFields = [
      'fields' => ['nid', 'title', 'type'],
      'table' => 'node',
      'alias' => 'n',
    ];

    return $nodeFields;
  }
}