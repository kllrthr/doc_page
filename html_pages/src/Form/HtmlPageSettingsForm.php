<?php

namespace Drupal\html_pages\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

/**
 * Defines a form for documentation page settings.
 *
 * @internal
 */
class HtmlPageSettingsForm extends ConfigFormBase {

  /**
  * {@inheritdoc}
  */
  protected function getEditableConfigNames() {
    return [
      'doc.adminsettings',
    ];
  }

  /**
  * {@inheritdoc}
  */
  public function getFormId() {
    return 'doc_page_settings';
  }

  /**
  * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('doc.adminsettings');

    $form['html'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Html pages'),
      '#description' => $this->t('Enter urls to html files.<br> Images in the file must be linked relative to the file.'),
    ];

    $form['html']['homepage_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Home url'),
      '#default_value' => $config->get('homepage_url'),
      '#required' => TRUE,
    ];

    $form['html']['get_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Getting started url'),
      '#default_value' => $config->get('get_url'),
      '#required' => TRUE,
    ];

    $form['html']['enrollment_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enrollment url'),
      '#default_value' => $config->get('enrollment_url'),
      '#required' => TRUE,
    ];

    $form['html']['contingency_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Contingency url'),
      '#default_value' => $config->get('contingency_url'),
      '#required' => TRUE,
    ];



    return parent::buildForm($form, $form_state);
  }

  /**
  * {@inheritdoc}
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the url.
    $this->config('doc.adminsettings')
      ->set('homepage_url', $form_state->getValue('homepage_url'))
      ->save();

    $this->config('doc.adminsettings')
      ->set('get_url', $form_state->getValue('get_url'))
      ->save();


    $this->config('doc.adminsettings')
      ->set('enrollment_url', $form_state->getValue('enrollment_url'))
      ->save();

    $this->config('doc.adminsettings')
      ->set('contingency_url', $form_state->getValue('contingency_url'))
      ->save();
  }

  /**
  * {@inheritdoc}
  */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // And check the url if set.
    if ($form_state->getValue('homepage_url') != '' && UrlHelper::isValid($form_state->getValue('homepage_url'), TRUE) == FALSE) {
      $form_state->setErrorByName('homepage_url', $this->t("Homepage URL doesn't look right"));
    }

    if ($form_state->getValue('get_url') != '' && UrlHelper::isValid($form_state->getValue('get_url'), TRUE) == FALSE) {
      $form_state->setErrorByName('get_url', $this->t("Getting started URL doesn't look right"));
    }

    if ($form_state->getValue('enrollment_url') != '' && UrlHelper::isValid($form_state->getValue('enrollment_url'), TRUE) == FALSE) {
      $form_state->setErrorByName('enrollment_url', $this->t("Enrollment URL doesn't look right"));
    }

    if ($form_state->getValue('contingency_url') != '' && UrlHelper::isValid($form_state->getValue('contingency_url'), TRUE) == FALSE) {
      $form_state->setErrorByName('contingency_url', $this->t("Contingency URL doesn't look right"));
    }

  }
}
