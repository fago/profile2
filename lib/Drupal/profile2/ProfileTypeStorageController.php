<?php

/**
 * @file
 * Contains Drupal\profile2\ProfileTypeController.
 */

namespace Drupal\profile2;

use Drupal\Core\Config\Entity\ConfigStorageController;
use Drupal\Core\Entity\EntityInterface;

/**
 * Controller class for profile types.
 */
class ProfileTypeStorageController extends ConfigStorageController {

  /**
   * Overrides \Drupal\Core\Config\Entity\ConfigStorageController::postSave().
   */
  protected function postSave(EntityInterface $entity, $update) {
    parent::postSave($entity, $update);

    if (!$update) {
      field_attach_create_bundle('profile2', $entity->id());
    }
    elseif ($entity->original->id() != $entity->id()) {
      field_attach_rename_bundle('profile2', $entity->original->id(), $entity->id());
    }
  }

  /**
   * Overrides \Drupal\Core\Config\Entity\ConfigStorageController::postDelete().
   */
  protected function postDelete($entities) {
    parent::postDelete($entities);

    foreach ($entities as $entity) {
      field_attach_delete_bundle('profile2', $entity->id());
    }
  }

}
