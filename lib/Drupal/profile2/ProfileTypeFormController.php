<?php

/**
 * @file
 * Contains \Drupal\profile2\ProfileTypeFormController.
 */

namespace Drupal\profile2;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityFormController;

/**
 * Form controller for profile type forms.
 */
class ProfileTypeFormController extends EntityFormController {

  /**
   * Overrides EntityFormController::form().
   */
  function form(array $form, array &$form_state, EntityInterface $type) {
    $form['label'] = array(
      '#title' => t('Label'),
      '#type' => 'textfield',
      '#default_value' => $type->label(),
      '#description' => t('The human-readable name of this profile type.'),
      '#required' => TRUE,
      '#size' => 30,
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#maxlength' => 32,
      '#machine_name' => array(
        'exists' => 'profile2_type_load',
      ),
    );
    $form['registration'] = array(
      '#type' => 'checkbox',
      '#title' => t('Include in user registration form'),
      '#default_value' => $type->get('registration'),
    );
    return $form;
  }

  /**
   * Overrides EntityFormController::actions().
   */
  protected function actions(array $form, array &$form_state) {
    $actions = parent::actions($form, $form_state);
    if (module_exists('field_ui') && $this->getEntity($form_state)->isNew()) {
      $actions['save_continue'] = $actions['submit'];
      $actions['save_continue']['#value'] = t('Save and manage fields');
      $actions['save_continue']['#submit'][] = array($this, 'redirectToFieldUI');
    }
    return $actions;
  }

  /**
   * Overrides EntityFormController::save().
   */
  public function save(array $form, array &$form_state) {
    $type = $this->getEntity($form_state);
    $status = $type->save();

    if ($status == SAVED_UPDATED) {
      drupal_set_message(t('%label profile type has been updated.', array('%label' => $type->label())));
    }
    else {
      drupal_set_message(t('%label profile type has been created.', array('%label' => $type->label())));
    }
    $form_state['redirect'] = 'admin/people/profiles';
  }

  /**
   * Form submission handler to redirect to Manage fields page of Field UI.
   */
  public function redirectToFieldUI(array $form, array &$form_state) {
    $type = $this->getEntity($form_state);
    $form_state['redirect'] = field_ui_bundle_admin_path('profile2', $type->id()) . '/fields';
  }

  /**
   * Overrides EntityFormController::delete().
   */
  public function delete(array $form, array &$form_state) {
    $type = $this->getEntity($form_state);
    $form_state['redirect'] = 'admin/people/profiles/manage/' . $type->id() . '/delete';
  }

}
