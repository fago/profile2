<?php

/**
 * @file
 * Contains Drupal\profile2\ProfileAccessController.
 */

namespace Drupal\profile2;

use Drupal\user\Plugin\Core\Entity\User;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityAccessControllerInterface;

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
    return $this->access($profile, 'view', $account);
  }

  /**
   * Implements EntityAccessControllerInterface::createAccess().
   */
  public function createAccess(EntityInterface $profile, $langcode = LANGUAGE_DEFAULT, User $account = NULL) {
    // Map to 'edit' access.
    return $this->editAccess($profile, $langcode, $account);
  }

  /**
   * Implements EntityAccessControllerInterface::updateAccess().
   */
  public function updateAccess(EntityInterface $profile, $langcode = LANGUAGE_DEFAULT, User $account = NULL) {
    // Map to 'edit' access.
    return $this->editAccess($profile, $langcode, $account);
  }

  /**
   * Implements EntityAccessControllerInterface::deleteAccess().
   */
  public function deleteAccess(EntityInterface $profile, $langcode = LANGUAGE_DEFAULT, User $account = NULL) {
    return $this->access($profile, 'delete', $account);
  }

  /**
   * Checks 'edit' access for a profile.
   *
   * @param \Drupal\Core\Entity\EntityInterface $profile
   *   The profile for which to check 'edit' access.
   * @param string $langcode
   *   (optional) The language code for which to check access. Defaults to
   *   LANGUAGE_DEFAULT.
   * @param \Drupal\user\Plugin\Core\Entity\User $account
   *   (optional) The user for which to check access, or NULL to check access
   *   for the current user. Defaults to NULL.
   *
   * @return bool
   *   TRUE if access was granted, FALSE otherwise.
   */
  public function editAccess(EntityInterface $profile, $langcode = LANGUAGE_DEFAULT, User $account = NULL) {
    return $this->access($profile, 'edit', $account);
  }

  /**
   * Determines whether the given user has access to a profile.
   *
   * @param \Drupal\Core\Entity\EntityInterface $profile
   *   (optional) A profile to check access for. If nothing is given, access for
   *   all profiles is determined.
   * @param string $operation
   *   The operation being performed. One of 'view', 'update', 'create',
   *   'delete' or just 'edit' (being the same as 'create' or 'update').
   * @param \Drupal\user\Plugin\Core\Entity\User $account
   *   The user to check for. Leave it to NULL to check for the global user.
   * @return boolean
   *   TRUE if access was granted, FALSE otherwise.
   *
   * @see hook_profile2_access()
   * @see profile2_profile2_access()
   */
  protected function access(EntityInterface $profile, $operation, User $account = NULL) {
    if (!isset($account)) {
      $account = entity_load('user', $GLOBALS['user']->uid);
    }

    // Try to retrieve from cache first.
    $pid = $profile->id();
    $uid = $account->id();
    if (isset($this->accessCache[$uid][$operation][$pid])) {
      return $this->accessCache[$uid][$operation][$pid];
    }

    // Administrators can access any profile.
    if (user_access('administer profiles', $account)) {
      $this->accessCache[$uid][$operation][$pid] = TRUE;
      return TRUE;
    }

    $access = NULL;
    // Allow modules to grant / deny access.
    foreach (module_implements('profile2_access', $operation, $profile, $account) as $module) {
      $return = module_invoke($module, 'profile2_access', $operation, $profile, $account);
      if ($return === FALSE) {
        // Directly return FALSE if a module denies access.
        $this->accessCache[$uid][$operation][$pid] = FALSE;
        return FALSE;
      }
      if ($return === TRUE) {
        $access = TRUE;
      }
    }

    $this->accessCache[$uid][$operation][$pid] = $access === TRUE ?: FALSE;
    return $this->accessCache[$uid][$operation][$pid];
  }

}
