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
   * Overrides DatabaseStorageController::create().
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
   * Overrides DatabaseStorageController::preSave().
   */
  protected function preSave(EntityInterface $entity) {
    // Before saving the profile set the 'changed' timestamp.
    $entity->changed = REQUEST_TIME;
  }

}
