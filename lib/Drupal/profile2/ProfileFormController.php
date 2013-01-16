<?php

/**
 * @file
 * Contains \Drupal\profile2\ProfileFormController.
 */

namespace Drupal\profile2;

use Drupal\Core\Entity\EntityFormController;

/**
 * Form controller for profile forms.
 */
class ProfileFormController extends EntityFormController {

  /**
   * Overrides EntityFormController::actions().
   */
  protected function actions(array $form, array &$form_state) {
    $element = parent::actions($form, $form_state);
    $element['delete']['#access'] = $this->getEntity($form_state)->access('delete');
    return $element;
  }

  /**
   * Overrides EntityFormController::save().
   */
  public function save(array $form, array &$form_state) {
    $profile = $this->getEntity($form_state);
    $profile->save();

    if ($GLOBALS['user']->uid == $profile->uid) {
      drupal_set_message(t('Your profile has been saved.'));
    }
    else {
      drupal_set_message(t("%name's profile has been updated.", array('%name' => user_format_name(user_load($profile->uid)))));
    }
  }

  /**
   * Overrides EntityFormController::delete().
   */
  public function delete(array $form, array &$form_state) {
    $profile = $this->getEntity($form_state);
    // Redirect to the deletion confirmation form.
    $form_state['redirect'] = 'user/' . $profile->uid . '/edit/' . $profile->bundle() . '/delete';
  }

}
