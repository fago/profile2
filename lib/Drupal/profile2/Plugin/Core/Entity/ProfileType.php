<?php

/**
 * @file
 * Contains \Drupal\profile2\Plugin\Core\Entity\ProfileType.
 */

namespace Drupal\profile2\Plugin\Core\Entity;

use Drupal\Component\Annotation\Plugin;
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

  /**
   * The primary identifier of the profile type.
   *
   * @var integer
   */
  public $id;

  /**
   * The universally unique identifier of the profile type.
   *
   * @var string
   */
  public $uuid;

  /**
   * The human-readable name of the profile type.
   *
   * @var string
   */
  public $label;

  /**
   * Whether the profile type is shown during registration.
   *
   * @var boolean
   */
  public $registration = FALSE;

  /**
   * The weight of the profile type compared to others.
   *
   * @var integer
   */
  public $weight = 0;

}
