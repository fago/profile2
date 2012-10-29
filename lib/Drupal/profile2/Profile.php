<?php

/**
 * @file
 * Contains Drupal\profile2\Profile.
 */

namespace Drupal\profile2;

use Drupal\Core\Entity\Entity;

/**
 * The class used for profile entities.
 */
class Profile extends Entity {

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
   * Overrides Drupal\Core\Entity\Entity::id().
   */
  public function id() {
    return isset($this->pid) ? $this->pid : NULL;
  }

  /**
   * Overrides Drupal\Core\Entity\Entity::bundle().
   */
  public function bundle() {
    return $this->type;
  }

  /**
   * Overrides Drupal\Core\Entity\Entity::label().
   */
  public function label($langcode = NULL) {
    if (isset($this->label) && $this->label !== '') {
      return $this->label;
    }
    else {
      return entity_load('profile2_type', $this->type)->label($langcode);
    }
  }

}
