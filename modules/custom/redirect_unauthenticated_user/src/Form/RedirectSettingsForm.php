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
   * Simple helper to debug to the console
   *
   * @param  object, array, string $data
   * @return string
   */
  function debugToConsole($msg) {
    echo "<script>console.log(".json_encode($msg).")</script>";
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
      '#markup' => t('Handles redirection of unauthenticated users from front page and any 404 error to a specified URL below. Format: https://www.hidglobal.com'),
      '#weight' => -100,
    );
    $form['url_container'] = ['#type' => 'container', '#attributes' => ['class' => ['container-inline']]];
    $form['url_container']['destination_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Redirect to'),
    ];
//    $form['frame'] = ['#type' => 'container', '#attributes' => ['class' => ['container-inline']]];
//    $form['frame']['e_mail'] = [
//      '#type' => 'email',
//      '#title' => $this->t('EMail'),
//    ];
//    $form['array_filter'] = ['#type' => 'value', '#value' => TRUE];
//    $this->debugToConsole($form['url_container']);
    return parent::buildForm($form, $form_state);
  }
  /**
   * (@inheritdoc)
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('email');
    if ($value != \Drupal::service('email.validator')->isValid($value)) {
      $form_state->setErrorByName('email', t('The email address %mail is not valid', ['%mail' => $value]));
      return;
    }

    $this->debugToConsole($form_state->getValue('url_container'));
//    $value = trim($element['#value']);
//    $form_state
//      ->setValueForElement($element, $value);
//    if ($value !== '' && !UrlHelper::isValid($value, TRUE)) {
//      $form_state
//        ->setError($element, t('The URL %url is not valid.', array(
//          '%url' => $value,
//        )));
//    }
//    $node = \Drupal::routeMatch()->getParameter('node');
//    // Check if email is already set up for this node
//    $select = Database::getConnection()->select('rsvplist', 'r');
//    $select->fields('r', ['nid']);
//    $select->condition('nid', $node->id());
//    $select->condition('mail', $value);
//    $results = $select->execute();
//    if (!empty($results->fetchCol())) {
//      // We found a row with the specified nid and email
//      $form_state->setErrorByName('email', t('The address %mail is already subscribed to the list.', ['%mail' => $value]));
//    }
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
