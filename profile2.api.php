<?php

/**
 * @file
 * This file contains no working PHP code; it exists to provide additional
 * documentation for doxygen as well as to document hooks in the standard
 * Drupal manner.
 */

use Drupal\profile2\Plugin\Core\Entity\Profile;
use Drupal\user\Plugin\Core\Entity\User;

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Control access to profiles.
 *
 * Modules may implement this hook if they want to have a say in whether or not
 * a given user has access to perform a given operation on a profile.
 *
 * @param $op
 *   The operation being performed. One of 'view', 'edit' (being the same as
 *   'create' or 'update') and 'delete'.
 * @param $profile
 *   A profile to check access for.
 * @param $account
 *   (optional) The user to check for. Defaults to the currently logged in user.
 * @return boolean
 *   Return TRUE to grant access, FALSE to explicitly deny access. Return NULL
 *   or nothing to not affect the operation.
 *   Access is granted as soon as a module grants access and no one denies
 *   access. Thus if no module explicitly grants access, access will be denied.
 */
function hook_profile2_access($op, Profile $profile, User $account = NULL) {
  // Explicitly deny access for a 'secret' profile type.
  if ($profile->type == 'secret' && !user_access('custom permission')) {
    return FALSE;
  }
  // For profiles other than the default profile grant access.
  if ($profile->type != 'main' && user_access('custom permission')) {
    return TRUE;
  }
  // In other cases do not alter access.
}

/**
 * @}
 */
