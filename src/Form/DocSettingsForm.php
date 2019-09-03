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
    }

    /**
    * {@inheritdoc}
    */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        if (UrlHelper::isValid($form_state->getValue('doc_url'), TRUE) == FALSE) {
            $form_state->setErrorByName('doc_url', $this->t("This URL doesn't look right"));
        }
    }
}
