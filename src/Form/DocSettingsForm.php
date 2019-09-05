<?php

namespace Drupal\doc_page\Form;

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

    $form['doc_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Url'),
      '#description' => $this->t('Enter documentation url'),
      '#default_value' => $config->get('doc_url'),
    ];

    $validators = array(
      'file_validate_extensions' => array('md'),
    );

    $form['doc_markdown'] = array(
      '#type' => 'managed_file',
      '#name' => 'doc_markdown',
      '#title' => t('File *'),
      '#size' => 20,
      '#description' => t('Markdown format only'),
      '#default_value' => $config->get('doc_markdown'),
      '#upload_validators' => $validators,
      '#upload_location' => 'public://doc_markdowns/',
    );

    return parent::buildForm($form, $form_state);
  }

  /**
  * {@inheritdoc}
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    // Save file.
    if (isset($form_state->getValue('doc_markdown')[0])) {
      $file_value = $form_state->getValue('doc_markdown');
      $file = \Drupal::entityTypeManager()->getStorage('file')->load($file_value[0]);
      $file->setPermanent();
      $file->save();

      $this->config('doc.adminsettings')
        ->set('doc_markdown', $file_value)
        ->save();
    } else {
      // Remove file.
      $this->config('doc.adminsettings')
        ->set('doc_markdown', FALSE)
        ->save();
    }
    // Save url.
    $this->config('doc.adminsettings')
      ->set('doc_url', $form_state->getValue('doc_url'))
      ->save();
  }

  /**
  * {@inheritdoc}
  */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Make sure one of the fields has a value.
    if ($form_state->getValue('doc_markdown') == NULL && $form_state->getValue('doc_url') == NULL) {
      $form_state->setErrorByName('doc_markdown', $this->t('Either file or url is required.'));
      $form_state->setErrorByName('doc_url', $this->t("Either file or url is required."));
    }
    // And check the url if set.
    if ($form_state->getValue('doc_url') != '' &&   UrlHelper::isValid($form_state->getValue('doc_url'), TRUE) == FALSE) {
      $form_state->setErrorByName('doc_url', $this->t("This URL doesn't look right"));
    }
  }
}
