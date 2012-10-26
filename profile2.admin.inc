<?php

/**
 * @file
 * Profile type editing UI.
 */

use Drupal\profile2\ProfileType;

/**
 * Page callback: Presents the form for creating a profile type.
 *
 * @return array
 *   A form array as expected by drupal_render().
 */
function profile2_type_add() {
  $profile_type = entity_create('profile2_type', array());
  return entity_get_form($profile_type);
}

/**
 * Page callback: Presents the form for editing a profile type.
 *
 * @param Drupal\profile2\ProfileType $profile_type
 *   The profile type to edit.
 *
 * @return array
 *   A form array as expected by drupal_render().
 */
function profile2_type_edit(ProfileType $profile_type) {
  drupal_set_title(t('Edit @name', array('@name' => $profile_type->label)));
  return entity_get_form($profile_type);
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
