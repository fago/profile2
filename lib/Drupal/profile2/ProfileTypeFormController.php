<?php

/**
 * @file
 * Contains Drupal\profile2\ProfileTypeFormController.
 */

namespace Drupal\profile2;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityFormController;

/**
 * Form controller for profile type forms.
 */
class ProfileTypeFormController extends EntityFormController {

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::form().
   */
  function form(array $form, array &$form_state, EntityInterface $profile_type) {
    $form['label'] = array(
      '#title' => t('Label'),
      '#type' => 'textfield',
      '#default_value' => $profile_type->label(),
      '#description' => t('The human-readable name of this profile type.'),
      '#required' => TRUE,
      '#size' => 30,
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $profile_type->id(),
      '#maxlength' => 32,
      '#machine_name' => array(
        'exists' => 'profile2_type_load',
      ),
    );
    $form['registration'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show during user account registration.'),
      '#default_value' => $profile_type->get('registration'),
    );
    return $form;
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::save().
   */
  public function save(array $form, array &$form_state) {
    $profile_type = $this->getEntity($form_state);
    $status = $profile_type->save();

    if ($status == SAVED_UPDATED) {
      drupal_set_message(t('%label configuration has been updated.', array('%label' => $profile_type->label())));
    }
    else {
      drupal_set_message(t('%label configuration has been inserted.', array('%label' => $profile_type->label())));
    }
    $form_state['redirect'] = 'admin/structure/profiles';

    // Rebuild the menu tree.
    // @todo Make a router rebuild unnecessary.
    menu_router_rebuild();
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::delete().
   */
  public function delete(array $form, array &$form_state) {
    $profile_type = $this->getEntity($form_state);
    $form_state['redirect'] = 'admin/structure/profiles/manage/' . $profile_type->id() . '/delete';
  }
}
