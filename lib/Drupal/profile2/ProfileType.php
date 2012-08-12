<?php

/**
 * @file
 * Contains Drupal\profile2\ProfileType.
 */

namespace Drupal\profile2;

use Drupal\entity\Entity;

/**
 * Use a separate class for profile types so we can specify some defaults
 * modules may alter.
 */
class ProfileType extends Entity {

  /**
   * Whether the profile type appears in the user categories.
   */
  public $userCategory = TRUE;

  /**
   * Whether the profile is displayed on the user account page.
   */
  public $userView = TRUE;

  public $type;
  public $label;
  public $weight = 0;

  /**
   * Returns whether the profile type is locked, thus may not be deleted or renamed.
   *
   * Profile types provided in code are automatically treated as locked, as well
   * as any fixed profile type.
   */
  public function isLocked() {
    return isset($this->status) && empty($this->is_new) && (($this->status & ENTITY_IN_CODE) || ($this->status & ENTITY_FIXED));
  }

  /**
   * Overridden, to introduce the method for old entity API versions (BC).
   *
   * @todo Remove once we bump the required entity API version.
   */
  public function getTranslation($property, $langcode = NULL) {
    if (module_exists('profile2_i18n')) {
      return parent::getTranslation($property, $langcode);
    }
    return $this->$property;
  }
}
