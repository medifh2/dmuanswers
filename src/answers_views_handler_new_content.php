<?php
namespace Drupal\answers;

/**
 * Field handler to display new content in question listing.
 *
 * @ingroup views_field_handlers
 */
class answers_views_handler_new_content extends views_handler_field {

  function construct() {
    parent::construct();
    $user = \Drupal::currentUser();
    if ($user->uid) {
      $this->additional_fields['nid'] = array('table' => 'node', 'field' => 'nid');
      $this->additional_fields['created'] = array('table' => 'node', 'field' => 'created');
    }
  }

  function query() {
    $user = \Drupal::currentUser();
    if (!$user->uid) {
      return;
    }
    $this->field_alias = 'new_answers';
    $this->ensure_my_table();
    $this->add_additional_fields();
  }

  function render($values) {
    $user = \Drupal::currentUser();
	if (!$user->uid) {
      return '';
    }
    $qid = $this->get_value($values, 'nid');
    $created = $this->get_value($values, 'created');
    $timestamp = node_last_viewed($qid);
    $output = '';
    // if the node was viewed or is a favorite
    if($timestamp) {
      $answers = db_query("SELECT nid FROM  {node} node LEFT JOIN {field_data_answers_related_question} field_data_answers_related_question ON node.nid = field_data_answers_related_question.entity_id AND field_data_answers_related_question.entity_type = 'node' AND field_data_answers_related_question.deleted = '0' WHERE field_data_answers_related_question.answers_related_question_target_id = :qid AND node.status = '1' AND node.created > :last_viewed_timestamp", array(':qid' => $qid, ':last_viewed_timestamp' => $timestamp))->fetchCol();
      if($answers) {
        // render as link will point to latest answer
        $new_answer_text = format_plural(count($answers), "1 new answer", "@count new answers");
        $output = l($new_answer_text, 'node/' . $qid, array('attributes' =>  array('class' => 'answers-new'), 'fragment' => "node-".end($answers)));
      }
    }
    else {
      if($created > $user->access) {
        $output = '<span class="answers-new">'. t('new') . '</span>';
      }
    }
    return $output;
  }
}
