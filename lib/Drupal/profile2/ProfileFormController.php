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

    if (!profile2_access('delete', $this->getEntity($form_state))) {
      unset($element['delete']);
    }

    return $element;
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::save().
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
   * Overrides Drupal\Core\Entity\EntityFormController::delete().
   */
  public function delete(array $form, array &$form_state) {
    $profile = $this->getEntity($form_state);
    // Redirect to the deletion confirmation form.
    $form_state['redirect'] = 'user/' . $profile->uid . '/edit/' . $profile->bundle() . '/delete';
  }

}
