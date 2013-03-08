<?php

/**
 * @file
 * Contains \Drupal\profile2\Plugin\Core\Entity\Profile.
 */

namespace Drupal\profile2\Plugin\Core\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\Entity;
use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;

/**
 * Defines the profile entity class.
 *
 * @Plugin(
 *   id = "profile2",
 *   label = @Translation("Profile"),
 *   module = "profile2",
 *   controller_class = "Drupal\profile2\ProfileStorageController",
 *   access_controller_class = "Drupal\profile2\ProfileAccessController",
 *   form_controller_class = {
 *     "default" = "Drupal\profile2\ProfileFormController"
 *   },
 *   render_controller_class = "Drupal\Core\Entity\EntityRenderController",
 *   base_table = "profile",
 *   uri_callback = "profile2_profile_uri",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "pid",
 *     "uuid" = "uuid",
 *     "bundle" = "type",
 *     "label" = "label"
 *   },
 *   bundle_keys = {
 *     "bundle" = "id"
 *   },
 *   view_modes = {
 *     "account" = {
 *       "label" = "User account",
 *       "custom_settings" = FALSE
 *     }
 *   }
 * )
 */
class Profile extends Entity implements ContentEntityInterface {

  /**
   * The profile id.
   *
   * @var integer
   */
  public $pid;

  /**
   * The profile UUID.
   *
   * @var string
   */
  public $uuid;

  /**
   * The name of the profile type.
   *
   * @var string
   */
  public $type;

  /**
   * The profile label.
   *
   * @var string
   */
  public $label;

  /**
   * The user id of the profile owner.
   *
   * @var integer
   */
  public $uid;

  /**
   * The Unix timestamp when the profile was created.
   *
   * @var integer
   */
  public $created;

  /**
   * The Unix timestamp when the profile was most recently saved.
   *
   * @var integer
   */
  public $changed;

  /**
   * Overrides Entity::id().
   */
  public function id() {
    return isset($this->pid) ? $this->pid : NULL;
  }

  /**
   * Overrides Entity::bundle().
   */
  public function bundle() {
    return $this->type;
  }

  /**
   * Overrides Entity::label().
   */
  public function label($langcode = NULL) {
    // If this profile has a custom label, use it. Otherwise, use the label of
    // the profile type.
    if (isset($this->label) && $this->label !== '') {
      return $this->label;
    }
    else {
      return entity_load('profile2_type', $this->type)->label($langcode);
    }
  }

}
