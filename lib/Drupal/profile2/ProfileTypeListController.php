<?php

/**
 * @file
 * Contains \Drupal\profile2\ProfileTypeListController.
 */

namespace Drupal\profile2;

use Drupal\Core\Config\Entity\ConfigEntityListController;
use Drupal\Core\Entity\EntityInterface;

/**
 * List controller for profile types.
 */
class ProfileTypeListController extends ConfigEntityListController {

  /**
   * Overrides \Drupal\Core\Entity\EntityListController::buildHeader().
   */
  public function buildHeader() {
    $row = parent::buildHeader();
    $operations = array_pop($row);
    $row['registration'] = t('Registration');
    $row['operations'] = $operations;
    return $row;
  }

  /**
   * Overrides \Drupal\Core\Entity\EntityListController::buildRow().
   */
  public function buildRow(EntityInterface $entity) {
    $row = parent::buildRow($entity);
    $operations = array_pop($row);
    $row['registration'] = $entity->get('registration') ? t('Yes') : t('No');
    $row['operations'] = $operations;
    return $row;
  }

  /**
   * Overrides \Drupal\Core\Entity\EntityListController::getOperations().
   */
  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);
    if (module_exists('field_ui')) {
      // Unlike other bundle entities, the most common operation for profile
      // types is to manage fields, so we suggest that as default operation.
      $uri = $entity->uri();
      $operations['manage-fields'] = array(
        'title' => t('Manage fields'),
        'href' => $uri['path'] . '/fields',
        'options' => $uri['options'],
        'weight' => 5,
      );
      $operations['manage-display'] = array(
        'title' => t('Manage display'),
        'href' => $uri['path'] . '/display',
        'options' => $uri['options'],
        'weight' => 6,
      );
    }
    return $operations;
  }

}
