<?php

namespace Drupal\tripal_db_sequence_checker\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Tripal DB Sequence Checker settings for this site.
 */
class TripalDbSequenceCheckerSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tripal_db_sequence_checker_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['tripal_db_sequence_checker.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['example'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example'),
      '#description' => $this->t('Please type the word "example".'),
      '#default_value' => $this->config('tripal_db_sequence_checker.settings')->get('example'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('example') != 'example') {
      $form_state->setErrorByName('example', $this->t('The value is not correct. Instead enter "example" to get validation to pass.'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $value = $form_state->getValue('example');
    $this->config('tripal_db_sequence_checker.settings')
      ->set('example', $value)
      ->save();
    $this->messenger()->addStatus("Setting the value of tripal_db_sequence_checker.settings.example to $value.");

    parent::submitForm($form, $form_state);
  }

}
