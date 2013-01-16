<?php

/**
 * @file
 * Contains \Drupal\profile2\ProfileAccessController.
 */

namespace Drupal\profile2;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityAccessControllerInterface;
use Drupal\user\Plugin\Core\Entity\User;

/**
 * Access controller for profiles.
 */
class ProfileAccessController implements EntityAccessControllerInterface {

  /**
   * Static cache for access checks.
   *
   * @var array
   */
  protected $accessCache = array();

  /**
   * Implements EntityAccessControllerInterface::viewAccess().
   */
  public function viewAccess(EntityInterface $profile, $langcode = LANGUAGE_DEFAULT, User $account = NULL) {
    return $this->access($profile, 'view', $langcode, $account);
  }

  /**
   * Implements EntityAccessControllerInterface::createAccess().
   */
  public function createAccess(EntityInterface $profile, $langcode = LANGUAGE_DEFAULT, User $account = NULL) {
    // Create and update operations are folded into edit access for profiles.
    return $this->access($profile, 'edit', $langcode, $account);
  }

  /**
   * Implements EntityAccessControllerInterface::updateAccess().
   */
  public function updateAccess(EntityInterface $profile, $langcode = LANGUAGE_DEFAULT, User $account = NULL) {
    // Create and update operations are folded into edit access for profiles.
    return $this->access($profile, 'edit', $langcode, $account);
  }

  /**
   * Implements EntityAccessControllerInterface::deleteAccess().
   */
  public function deleteAccess(EntityInterface $profile, $langcode = LANGUAGE_DEFAULT, User $account = NULL) {
    return $this->access($profile, 'delete', $langcode, $account);
  }

  /**
   * Determines whether the given user has access to a profile.
   *
   * @param \Drupal\Core\Entity\EntityInterface $profile
   *   A profile to check access for.
   * @param string $operation
   *   The operation being performed. One of 'view', 'create', 'update', or
   *   'delete'.
   * @param string $langcode
   *   The language code for which to check access.
   * @param \Drupal\user\Plugin\Core\Entity\User $account
   *   (optional) The user to check for. Omit to check access for the global
   *   user.
   *
   * @return bool
   *   TRUE if access is allowed, FALSE otherwise.
   *
   * @see hook_profile2_access()
   * @see profile2_profile2_access()
   */
  protected function access(EntityInterface $profile, $operation, $langcode, User $account = NULL) {
    if (!isset($account)) {
      $account = entity_load('user', $GLOBALS['user']->uid);
    }
    // Check for the bypass access permission first. No need to cache this,
    // since user_access() is cached already.
    if (user_access('bypass profile access', $account)) {
      return TRUE;
    }
    $uid = $account->id();
    // For existing profiles, check access for the particular profile ID. When
    // creating a new profile, check access for the profile's bundle.
    $pid = $profile->id() ?: $profile->bundle();

    if (isset($this->accessCache[$uid][$operation][$pid][$langcode])) {
      return $this->accessCache[$uid][$operation][$pid][$langcode];
    }

    $access = NULL;
    // Ask modules to grant or deny access.
    foreach (module_implements('profile2_access', $operation, $profile, $account) as $module) {
      $return = module_invoke($module, 'profile2_access', $operation, $profile, $account);
      // If a module denies access, there's no point in asking further.
      if ($return === FALSE) {
        $access = $return;
        break;
      }
      // A module may grant access, but others may still deny.
      if ($return === TRUE) {
        $access = TRUE;
      }
    }
    $this->accessCache[$uid][$operation][$pid][$langcode] = ($access === TRUE);

    return $this->accessCache[$uid][$operation][$pid][$langcode];
  }

}
