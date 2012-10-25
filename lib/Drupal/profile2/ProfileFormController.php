<?php

/**
 * @file
 * Contains Drupal\profile2\ProfileFormController.
 */

namespace Drupal\profile2;

use Drupal\Core\Entity\EntityFormController;

/**
 * Form controller for profile forms.
 */
class ProfileFormController extends EntityFormController {

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::actionsElement().
   */
  protected function actionsElement(array $form, array &$form_state) {
    $element = parent::actionsElement($form, $form_state);

    if (!user_access('administer profiles')) {
      unset($element['delete']);
    }

    return $element;
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::save().
   */
  public function save(array $form, array &$form_state) {
    global $user;

    $profile = $this->getEntity($form_state);
    $profile->save();

    if ($user->uid == $profile->uid) {
      drupal_set_message(t('Your profile has been saved.'));
    }
    else {
      drupal_set_message(t("%name's profile has been updated.", array('%name' => user_format_name(user_load($profile->uid)))));
    }
  }
}
