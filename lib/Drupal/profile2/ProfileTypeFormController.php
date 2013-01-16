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
      '#title' => t('Show during user account registration.'),
      '#default_value' => $type->get('registration'),
    );
    return $form;
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
   * Overrides EntityFormController::delete().
   */
  public function delete(array $form, array &$form_state) {
    $type = $this->getEntity($form_state);
    $form_state['redirect'] = 'admin/people/profiles/manage/' . $type->id() . '/delete';
  }
}
