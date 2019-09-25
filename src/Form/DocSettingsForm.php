<?php

namespace Drupal\dp_markdown\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

/**
 * Defines a form for documentation page settings.
 *
 * @internal
 */
class DocSettingsForm extends ConfigFormBase {

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

    $form['markdown'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Markdown pages'),
      '#description' => $this->t('Enter urls to markdown files.<br> Images in the markdown file must be linked relative to the .md file'),
    ];

    $form['markdown']['doc_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Documentation url'),
      '#default_value' => $config->get('doc_url'),
      '#required' => TRUE,
    ];

    $form['markdown']['release_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Release info url'),
      '#default_value' => $config->get('release_url'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
  * {@inheritdoc}
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);


    $this->config('doc.adminsettings')
      ->set('doc_url', $form_state->getValue('doc_url'))
      ->save();

    $this->config('doc.adminsettings')
      ->set('release_url', $form_state->getValue('release_url'))
      ->save();
  }

  /**
  * {@inheritdoc}
  */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    if ($form_state->getValue('release_url') != '' && UrlHelper::isValid($form_state->getValue('release_url'), TRUE) == FALSE) {
      $form_state->setErrorByName('release_url', $this->t("Release info URL doesn't look right"));
    }

    if ($form_state->getValue('doc_url') != '' && UrlHelper::isValid($form_state->getValue('doc_url'), TRUE) == FALSE) {
      $form_state->setErrorByName('doc_url', $this->t("This URL doesn't look right"));
    }


  }
}
