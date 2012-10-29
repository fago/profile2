<?php

/**
 * @file
 * Page and form callbacks for profiles.
 */

use Drupal\profile2\Profile;

/**
 * Form constructor to confirm deletion of a profile.
 *
 * @param Drupal\profile2\Profile $profile
 *   The profile to delete.
 *
 * @return array
 *   A form render array suitable for drupal_render().
 *
 * @see profile2_delete_confirm_form_submit()
 */
function profile2_delete_confirm_form(array $form, array &$form_state, Profile $profile) {
  $form_state['profile'] = $profile;
  $form_state['account'] = entity_load('user', $profile->uid);
  $form['pid'] = array('#type' => 'value', '#value' => $profile->id());

  if ($GLOBALS['user']->uid == $profile->uid) {
    $confirm_question = t('Are you sure you want to delete your %label profile?', array(
      '%label' => $profile->label(),
    ));
  }
  else {
    $confirm_question = t("Are you sure you want to delete %name's %label profile?", array(
      '%name' => user_format_name($form_state['account']),
      '%label' => $profile->label(),
    ));
  }
  return confirm_form($form, $confirm_question, $profile->uri(), NULL, t('Delete'));
}

/**
 * Form submission handler for profile2_delete_confirm_form().
 */
function profile2_delete_confirm_form_submit(array $form, array &$form_state) {
  $form_state['profile']->delete();

  if ($GLOBALS['user']->uid == $form_state['profile']->uid) {
    $message = t('Your %label profile has been deleted.', array(
      '%label' => $form_state['profile']->label(),
    ));
  }
  else {
    $message = t("%name's %label profile has been deleted.", array(
      '%name' => user_format_name($form_state['account']),
      '%label' => $form_state['profile']->label(),
    ));
  }
  drupal_set_message($message);

  // Redirect to the user page.
  $uri = $form_state['account']->uri();
  $form_state['redirect'] = array($uri['path'], $uri['options']);
}
