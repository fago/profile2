<?php

/**
 * @file
 * Contains Drupal\profile2\ProfileStorageController.
 */

namespace Drupal\profile2;

use Drupal\Core\Entity\DatabaseStorageController;
use Drupal\Core\Entity\EntityInterface;

/**
 * Controller class for profile types.
 */
class ProfileStorageController extends DatabaseStorageController {

  /**
   * Overrides Drupal\Core\Entity\DatabaseStorageController::create().
   */
  public function create(array $values) {
    $entity = parent::create($values);

    // Set the created time to now.
    if (!$entity->created) {
      $entity->created = REQUEST_TIME;
    }

    return $entity;
  }

  /**
   * Overrides Drupal\Core\Entity\DatabaseStorageController::preSave().
   */
  protected function preSave(EntityInterface $entity) {
    // Before saving the profile set the 'changed' timestamp.
    $entity->changed = REQUEST_TIME;
  }

  /**
   * Overrides Drupal\Core\Entity\DatabaseStorageController::postSave().
   */
  protected function postSave(EntityInterface $entity, $update) {
    parent::postSave($entity, $update);

    // Update the static cache from profile2_load_by_user().
    $cache = &drupal_static('profile2_load_by_user', array());
    unset($cache[$entity->uid]);

    if ($update) {
      unset($cache[$entity->original->uid]);
    }
  }

  /**
   * Overrides Drupal\Core\Entity\DatabaseStorageController::postSave().
   */
  protected function postDelete($entities) {
    // Update the static cache from profile2_load_by_user().
    $cache = &drupal_static('profile2_load_by_user', array());
    foreach ($entities as $entity) {
      unset($cache[$entity->uid]);
    }
  }

}
