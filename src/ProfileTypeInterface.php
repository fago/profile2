<?php

/**
 * @file
 * Contains \Drupal\profile\Entity\ProfileTypeInterface.
 */

namespace Drupal\profile;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a profile type entity.
 */
interface ProfileTypeInterface extends ConfigEntityInterface {

  /**
   * Returns the label of the profile type.
   */
  public function getLabel();

  /**
   * Return the registration flag for allowing creation of profile type
   * at user registration.
   */
  public function getRegistration();

}
