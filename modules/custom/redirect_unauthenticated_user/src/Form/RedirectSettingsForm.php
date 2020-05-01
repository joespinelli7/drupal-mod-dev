<?php
/**
 * @file
 * Contains \Drupal\redirect_unauthenticated_user\Form\RedirectSettingsForm
 */

namespace Drupal\redirect_unauthenticated_user\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Render\Element\Form;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form to configure RSVP List module settings
 */

class RedirectSettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */

  public function getFormId() {
    return 'redirect_unauthenticated_user_admin_settings';
  }
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'redirect_unauthenticated_user.settings'
    ];
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
//    $types = node_type_get_names();
//    $config = $this->config('redirect_unauthenticated_user.settings');
    $form['text_header'] = array
    (
      '#prefix' => '<h3>',
      '#suffix' => '</h3>',
      '#markup' => t('URL redirection (Ex: https://www.hidglobal.com => https://www.hidglobal.com/support)'),
      '#weight' => -100,
    );
    $form['url_from'] = ['#type' => 'container', '#attributes' => ['class' => ['container-inline']]];
    $form['url_from']['redirecting_url'] = [
      '#title' => $this->t('From: https://baseurl.com/'),
      '#type' => 'url',
    ];
    $form['url_to'] = ['#type' => 'container', '#attributes' => ['class' => ['container-inline']]];
    $form['url_to']['destination_url'] = [
      '#type' => 'url',
      '#title' => $this->t('To'),
    ];
//    $form['frame'] = ['#type' => 'container', '#attributes' => ['class' => ['container-inline']]];
//    $form['frame']['e_mail'] = [
//      '#type' => 'email',
//      '#title' => $this->t('EMail'),
//    ];
//    $form['array_filter'] = ['#type' => 'value', '#value' => TRUE];

    return parent::buildForm($form, $form_state);
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $allowed_types = array_filter($form_state->getValue('redirect_unauthenticated_user_types'));
    sort($allowed_types);
    $this->config('redirect_unauthenticated_user.settings')
      ->set('allowed_types', $allowed_types)
      ->save();
    parent::submitForm($form, $form_state);
  }
}
