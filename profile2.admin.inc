<?php

/**
 * @file
 * Profile type editing UI.
 */

use Drupal\profile2\ProfileType;

/**
 * Generates the profile type editing form.
 */
function profile2_type_form($form, &$form_state, ProfileType $profile_type = NULL, $op = 'edit') {
  // During initial form build, add the entity to the form state for use
  // during form building and processing. During a rebuild, use what is in the
  // form state.
  if (!isset($form_state['profile2_type'])) {
    if (!isset($profile_type)) {
      $profile_type = entity_create('profile2_type', array());
    }
    $form_state['profile2_type'] = $profile_type;
  }
  else {
    $profile_type = $form_state['profile2_type'];
  }

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
      'source' => array('label'),
    ),
  );

  $form['registration'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show during user account registration.'),
    '#default_value' => $profile_type->get('registration'),
  );

  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save profile type'),
    '#weight' => 40,
  );

  if ($profile_type->id()) {
    $form['actions']['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Delete profile type'),
      '#weight' => 45,
      '#limit_validation_errors' => array(),
      '#submit' => array('profile2_type_form_submit_delete')
    );
  }
  return $form;
}

/**
 * Form API submit callback for the type form.
 */
function profile2_type_form_submit(&$form, &$form_state) {
  form_state_values_clean($form_state);

  $profile_type = $form_state['profile2_type'];
  entity_form_submit_build_entity('profile2_type', $profile_type, $form, $form_state);

  $profile_type->save();

  drupal_set_message(t('%label configuration has been saved.', array('%label' => $profile_type->label())));

  $form_state['redirect'] = 'admin/structure/profiles';
}

/**
 * Form constructor to delete a ProfileType object.
 *
 * @param Drupal\profile2\ProfileType $profile_type
 *   The ProfileType object to delete.
 */
function profile2_type_delete_form($form, &$form_state, ProfileType $profile_type) {
  $form_state['profile2_type'] = $profile_type;

  $form['id'] = array('#type' => 'value', '#value' => $profile_type->id());
  return confirm_form($form,
    t('Are you sure you want to delete %label', array('%label' => $profile_type->label())),
    'admin/structure/profiles',
    NULL,
    'Delete'
  );
}

/**
 * Form submission handler for profile_type_delete_form().
 */
function profile2_type_delete_form_submit($form, &$form_state) {
  $form_state['profile2_type']->delete();
  $form_state['redirect'] = 'admin/structure/profiles';
}

/**
 * Form API submit callback for the delete button.
 */
function profile2_type_form_submit_delete(&$form, &$form_state) {
  $form_state['redirect'] = 'admin/structure/profiles/manage/' . $form_state['profile2_type']->id() . '/delete';
}
