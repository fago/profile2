<?php

/**
 * @file
 * Contains \Drupal\profile\ProfileInterface.
 */

namespace Drupal\profile;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\user\UserInterface;

/**
 * Provides an interface defining a profile entity.
 */
interface ProfileInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Returns the profile type.
   */
  public function getType();

  /**
   * Sets the profile type.
   *
   * @param string $type
   *   The profile type.
   *
   * @return \Drupal\profile\ProfileInterface
   *   The called profile entity.
   */
  public function setType($type);

  /**
   * Returns the profile creation timestamp.
   */
  public function getCreatedTime();

  /**
   * Sets the profile creation timestamp.
   *
   * @param int $timestamp
   *   The profile creation timestamp.
   *
   * @return \Drupal\profile\ProfileInterface
   *   The called profile entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the profile revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the profile revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\profile\ProfileInterface
   *   The called profile entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Returns the profile revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionAuthor();

  /**
   * Sets the profile revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\profile\ProfileInterface
   *   The called profile entity.
   */
  public function setRevisionAuthorId($uid);

  /**
   * Returns a label for the profile.
   */
  public function label();

  /**
   * Returns the node published status indicator.
   *
   * Unpublished profiles are only visible to their authors and to administrators.
   *
   * @return bool
   *   TRUE if the profile is active.
   */
  public function isActive();

  /**
   * Sets the published status of a profile.
   *
   * @param bool $actvie
   *   TRUE to set this profile to active, FALSE to set it to inactive.
   *
   * @return \Drupal\profile\ProfileInterface
   *   The called profile entity.
   */
  public function setActive($active);

}
