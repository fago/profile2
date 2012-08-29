<?php

/**
 * @file
 * Contains Drupal\profile2\ProfileType.
 */

namespace Drupal\profile2;

use Drupal\config\ConfigurableBase;

/**
 * Use a separate class for profile types so we can specify some defaults
 * modules may alter.
 */
class ProfileType extends ConfigurableBase {

  public $id;
  public $uuid;
  public $label;

  public $weight = 0;

  /**
   * Whether profile type is shown during registration.
   *
   * @var boolean
   */
  public $registration = FALSE;

  /**
   * Whether the profile type appears in the user categories.
   */
  public $userCategory = TRUE;

  /**
   * Whether the profile is displayed on the user account page.
   */
  public $userView = TRUE;

}
