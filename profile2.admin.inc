<?php

/**
 * @file
 * Profile type editing UI.
 */

use Drupal\profile2\Plugin\Core\Entity\ProfileType;

/**
 * Page callback; Lists available ProfileType objects.
 */
function profile2_type_list_page() {
  $controller = entity_list_controller('profile2_type');
  return $controller->render();
}

/**
 * Page callback: Presents the form for creating a profile type.
 *
 * @return array
 *   A form array as expected by drupal_render().
 */
function profile2_type_add() {
  $type = entity_create('profile2_type', array());
  return entity_get_form($type);
}

/**
 * Page callback: Presents the form for editing a profile type.
 *
 * @param Drupal\profile2\Plugin\Core\Entity\ProfileType $type
 *   The profile type to edit.
 *
 * @return array
 *   A form array as expected by drupal_render().
 */
function profile2_type_edit(ProfileType $type) {
  drupal_set_title(t('Edit %label profile type', array('%label' => $type->label())), PASS_THROUGH);
  return entity_get_form($type);
}

/**
 * Form constructor to delete a ProfileType object.
 *
 * @param Drupal\profile2\Plugin\Core\Entity\ProfileType $type
 *   The ProfileType object to delete.
 */
function profile2_type_delete_form($form, &$form_state, ProfileType $type) {
  $form_state['profile2_type'] = $type;

  $form['id'] = array('#type' => 'value', '#value' => $type->id());
  return confirm_form($form,
    t('Are you sure you want to delete %label and all of its associated profiles?', array('%label' => $type->label())),
    'admin/people/profiles',
    NULL,
    t('Delete')
  );
}

/**
 * Form submission handler for profile2_type_delete_form().
 */
function profile2_type_delete_form_submit($form, &$form_state) {
  $form_state['profile2_type']->delete();
  drupal_set_message(t('The profile type %label has been deleted.', array(
    '%label' => $form_state['profile2_type']->label(),
  )));
  $form_state['redirect'] = 'admin/people/profiles';
}
