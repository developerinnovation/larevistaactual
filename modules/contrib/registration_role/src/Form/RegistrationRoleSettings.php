<?php

namespace Drupal\registration_role\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Contribute form.
 */
class RegistrationRoleSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'registration_role_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['registration_role.setting'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('registration_role.setting');
    $case = $config->get('role_to_select');
    $roles = user_roles(TRUE);
    unset($roles['authenticated']);
    foreach ($roles as $key => $value) {
      $options[$key] = $value->label();
    }
    $form['role_to_select'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Roles to Assign'),
      '#required' => TRUE,
      '#options' => $options,
      '#default_value' => $case,
      '#description' => 'The selected role will be assigned to users who register using the user-registration form. Be sure this role does not have any privileges you fear giving out without reviewing who receives it.',
    );

    $mode_case = $config->get('registration_mode');
    $registration_mode_options = [
      'user' => t('User self registration'),
      'admin' => t('Both user self registration and user creation by admin'),
    ];
    $form['registration_mode'] = array(
      '#type' => 'radios',
      '#title' => t('Registration mode'),
      '#required' => TRUE,
      '#options' => $registration_mode_options,
      '#default_value' => $mode_case ? $mode_case : 'user',
      '#description' => 'Select if the role will be assigned only on the user self registration form, or also when an administrator is creating that user.',
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $saved_role = $form_state->getValue('role_to_select');
    $this->config('registration_role.setting')
      ->set('role_to_select', $saved_role)
      ->save();

    $saved_mode = $form_state->getValue('registration_mode');
    $this->config('registration_role.setting')
      ->set('registration_mode', $saved_mode)
      ->save();

    parent::submitForm($form, $form_state);
  }

}
