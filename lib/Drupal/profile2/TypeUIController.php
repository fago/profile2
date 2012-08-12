<?php

/**
 * @file
 * Contains Drupal\profile2\TypeUIController.
 */

namespace Drupal\profile2;

use EntityDefaultUIController;

/**
 * UI controller.
 */
class TypeUIController extends EntityDefaultUIController {

  /**
   * Overrides hook_menu() defaults.
   */
  public function hook_menu() {
    $items = parent::hook_menu();
    $items[$this->path]['description'] = 'Manage profiles, including fields.';
    return $items;
  }
}

