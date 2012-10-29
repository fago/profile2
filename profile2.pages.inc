<?php

/**
 * @file
 * Page and form callbacks for profiles.
 */

use Drupal\profile2\Profile;

/**
 * Confirmation form for deleting a profile.
 */
function profile2_delete_confirm_form($form, $form_state, Profile $profile) {
  $form_state['profile'] = $profile;
  // @todo Work out a good confirmation question.
  $confirm_question = t('Are you sure you want to delete %label?', array('%label' => $profile->label()));
  return confirm_form($form, $confirm_question, $profile->uri());
}

/**
 * Submit handler for deleting a profile.
 */
function profile2_delete_confirm_form_submit(array $form, array &$form_state) {
  $form_state['profile']->delete();
  // @todo Work out a good deletion message.
  drupal_set_message(t('Deleted %label.', array('%label' => $form_state['profile']->label())));

  // Redirect to the user page.
  $uri = entity_load('user', $form_state['profile']->uid)->uri();
  $form_state['redirect'] = $uri['path'];
}
