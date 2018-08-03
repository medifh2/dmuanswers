<?php

/**
 * @file
 * Contains \Drupal\answers\Form\AnswersSettings.
 */

namespace Drupal\answers\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class AnswersSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'answers_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('answers.settings');

    foreach (Element::children($form) as $variable) {
      $config->set($variable, $form_state->getValue($form[$variable]['#parents']));
    }
    $config->save();

    if (method_exists($this, '_submitForm')) {
      $this->_submitForm($form, $form_state);
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['answers.settings'];
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = [];
    $form['answers_question_lock_settings'] = [
      '#type' => 'fieldset',
      '#title' => t('Question Lock Settings'),
      '#weight' => -100,
    ];

    // @FIXME
    // Could not extract the default value because it is either indeterminate, or
    // not scalar. You'll need to provide a default value in
    // config/install/answers.settings.yml and config/schema/answers.schema.yml.
    $form['answers_question_lock_settings']['answers_question_lock_message'] = [
      '#type' => 'textfield',
      '#title' => t('Question lock message'),
      '#description' => t('Text to use to notify user that a question is locked'),
      '#default_value' => \Drupal::config('answers.settings')->get('answers_question_lock_message'),
    ];

    return parent::buildForm($form, $form_state);
  }

}
?>
