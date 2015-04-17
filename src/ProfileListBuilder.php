<?php

/**
 * @file
 * Contains \Drupal\profile\ProfileListController.
 */

namespace Drupal\profile;

use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\LanguageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * List controller for profiles.
 *
 * @see \Drupal\profile\Entity\Profile
 */
class ProfileListBuilder extends EntityListBuilder {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * Constructs a new ProfileListController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatter $date_formatter) {
    parent::__construct($entity_type, $storage);

    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = array(
      'label' => $this->t('Label'),
      'type' => array(
        'data' => $this->t('Type'),
        'class' => array(RESPONSIVE_PRIORITY_MEDIUM),
      ),
      'owner' => array(
        'data' => $this->t('Owner'),
        'class' => array(RESPONSIVE_PRIORITY_LOW),
      ),
      'status' => $this->t('Status'),
      'changed' => array(
        'data' => $this->t('Updated'),
        'class' => array(RESPONSIVE_PRIORITY_LOW),
      ),
    );
    if (\Drupal::languageManager()->isMultilingual()) {
      $header['language_name'] = array(
        'data' => $this->t('Language'),
        'class' => array(RESPONSIVE_PRIORITY_LOW),
      );
    }
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\profile\ProfileInterface $entity */
    $mark = array(
      '#theme' => 'mark',
      '#mark_type' => node_mark($entity->id(), $entity->getChangedTime()),
    );
    $langcode = $entity->language()->id;
    $uri = $entity->urlInfo();
    $options = $uri->getOptions();
    $options += ($langcode != LanguageInterface::LANGCODE_NOT_SPECIFIED && isset($languages[$langcode]) ? array('language' => $languages[$langcode]) : array());
    $uri->setOptions($options);
    $row['label']['data'] = array(
        '#type' => 'link',
        '#title' => $entity->label(),
        '#suffix' => ' ' . drupal_render($mark),
      ) + $uri->toRenderArray();
    $row['type'] = $entity->getType()->id();
    $row['owner']['data'] = array(
      '#theme' => 'username',
      '#account' => $entity->getOwner(),
    );
    $row['status'] = $entity->isActive() ? $this->t('active') : $this->t('not active');
    $row['changed'] = $this->dateFormatter->format($entity->getChangedTime(), 'short');
    $language_manager = \Drupal::languageManager();
    if ($language_manager->isMultilingual()) {
      $row['language_name'] = $language_manager->getLanguageName($langcode);
    }

    $route_params = array('user' => $entity->getOwnerId(), 'type' => $entity->bundle(), 'profile' => $entity->id());
    $links['edit'] = array(
      'title' => t('Edit'),
      'route_name' => 'entity.profile.edit_form',
      'route_parameters' => $route_params,
    );
    $links['delete'] = array(
      'title' => t('Delete'),
      'route_name' => 'entity.profile.delete_form',
      'route_parameters' => $route_params,
    );

    $row[] = array(
      'data' => array(
        '#type' => 'operations',
        '#links' => $links,
      ),
    );

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    $destination = drupal_get_destination();
    foreach ($operations as $key => $operation) {
      $operations[$key]['query'] = $destination;
    }
    return $operations;
  }

}
