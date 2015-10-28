<?php

/**
 * @file
 * Contains \Drupal\profile\Plugin\Derivative\ProfileLocalTask.
 */

namespace Drupal\profile\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides dynamic routes to add/edit/list profiles.
 */
class ProfileLocalTask extends DeriverBase implements ContainerDeriverInterface {

  /**
   * Stores the profile type config objects.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The entity manager service
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new ProfileAddLocalTask.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct($base_plugin_definition, ConfigFactoryInterface $config_factory, EntityManagerInterface $entity_manager) {
    $this->config = $config_factory->loadMultiple($config_factory->listAll('profile.type'));
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_definition) {
    return new static(
      $base_plugin_definition,
      $container->get('config.factory'),
      $container->get('entity.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [];

    //foreach ($this->config as $profile_type_id => $profile_type) {
    foreach ($this->entityManager->getStorage('profile_type')->loadMultiple() as $profile_type_id => $profile_type) {
      $this->derivatives['profile.type.' . $profile_type_id] = $base_plugin_definition;
      $this->derivatives['profile.type.' . $profile_type_id]['route_name'] = "entity.profile.type.$profile_type_id.user_profile_form";
      //$this->derivatives['profile.type.' . $profile_type_id]['weight'] = 1; // @TODO.
      $this->derivatives['profile.type.' . $profile_type_id]['title'] = $profile_type->label();
      $this->derivatives['profile.type.' . $profile_type_id]['parent_id'] = 'entity.user.edit_form';
      $this->derivatives['profile.type.' . $profile_type_id]['route_parameters'] = array('profile_type' => $profile_type_id);
    }
    return $this->derivatives;
  }

}
