<?php

/**
 * @file
 * Definition of Drupal\profile\Plugin\views\field\Label.
 */

namespace Drupal\profile\Plugin\views\field;

use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\ViewExecutable;

/**
 * Field handler to show the generated label of a profile.
 *
 * @ViewsField("profile_label")
 */
class Label extends FieldPluginBase {

  /**
   * Overrides \Drupal\views\Plugin\views\field\FieldPluginBase::init().
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);

    $this->additional_fields['pid'] = 'pid';
  }

  public function query() {
    $this->ensureMyTable();
    $this->addAdditionalFields();
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    if ($entity = $this->getEntity($values)) {
      $mark = $this->getMark($entity);
      return $this->renderLink($entity, $values) . ' ' . drupal_render($mark);
    }
  }

  /**
   * Prepares the link to the profile.
   *
   * @param \Drupal\Core\Entity\EntityInterface $profile
   *   The profile entity this field belongs to.
   * @param ResultRow $values
   *   The values retrieved from the view's result set.
   *
   * @return string
   *   Returns a string for the link text.
   */
  protected function renderLink($profile, ResultRow $values) {
    if ($profile->access('view')) {
      $this->options['alter']['make_link'] = TRUE;
      $this->options['alter']['path'] = 'profile/' . $profile->id();
      return $profile->label();
    }
  }

  /**
   * Helper function to get the renderable array of the entity's mark.
   *
   * @param $entity
   *  The profile entity this field belongs to.
   * @return array
   *  Renderable array of the entity mark.
   */
  protected function getMark($entity) {
    return array(
      '#theme' => 'mark',
      '#mark_type' => node_mark($entity->id(), $entity->getChangedTime()),
    );
  }

}
