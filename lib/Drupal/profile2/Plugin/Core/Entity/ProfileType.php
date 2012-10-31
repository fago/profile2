<?php

/**
 * @file
 * Definition of Drupal\profile2\Plugin\Core\Entity\ProfileType.
 */

namespace Drupal\profile2\Plugin\Core\Entity;

use Drupal\Core\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the profile type entity class.
 *
 * @Plugin(
 *   id = "profile2_type",
 *   label = @Translation("Profile type"),
 *   module = "profile2",
 *   controller_class = "Drupal\profile2\ProfileTypeStorageController",
 *   list_controller_class = "Drupal\profile2\ProfileTypeListController",
 *   form_controller_class = {
 *     "default" = "Drupal\profile2\ProfileTypeFormController"
 *   },
 *   config_prefix = "profile2.type",
 *   uri_callback = "profile2_profile_type_uri",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   }
 * )
 */
class ProfileType extends ConfigEntityBase {

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
