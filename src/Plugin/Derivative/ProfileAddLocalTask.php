<?php

/**
 * @file
 * Contains \Drupal\profile\Plugin\Derivative\ProfileAddLocalTask.
 */

namespace Drupal\profile\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Component\Utility\Unicode;
use Drupal\field\FieldConfigInterface;
use Drupal\user\UserInterface;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\Cache;

use Drupal\Core\PhpStorage\PhpStorageFactory;

use Symfony\Component\HttpFoundation\Request;

/**
 * Provides dynamic routes to add profiles.
 */
class ProfileAddLocalTask extends DeriverBase implements ContainerDeriverInterface {

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
   * Constructs a new ThemeLocalTask.
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
    $user = \Drupal::request()->attributes->get('user');
    $route_name = \Drupal::routeMatch()->getRouteName();

    if ($user instanceof UserInterface && $route_name == 'entity.user.edit_form') {

      PhpStorageFactory::get('service_container')->deleteAll();
      PhpStorageFactory::get('twig')->deleteAll();
      drupal_flush_all_caches();

      $configs = [];
      foreach ($this->config as $config) {
        $instances = array_filter($this->entityManager->getFieldDefinitions('profile', $config->get('id')), function ($field_definition) {
          return $field_definition instanceof FieldConfigInterface;
        });

        $display = FALSE;
        // No fields yet.
        if (!count($instances)) {
          continue;
        }
        else {
          // Expose profile types that users may create - either they have 0 of non-multiple or multiple.
          if ($config->get('multiple') === FALSE) {
            $profiles = $this->entityManager->getStorage('profile')
              ->loadByProperties([
                'uid' => $user->id(),
                'type' => $config->get('id'),
              ]);
            // Single profile, none yet.
            if (!count($profiles)) {
              $display = TRUE;
              $configs[] = $config;
            }
          }
          else {
            // Multiple profiles allowed.
            $display = TRUE;
            $configs[] = $config;
          }

          if ($display) {
            $id = $config->get('id') . '-' . $user->id();
            $this->derivatives[$id] = $base_plugin_definition;
            $this->derivatives[$id]['title'] = \Drupal::translation()
              ->translate('Add @type profile', ['@type' => Unicode::strtolower($config->get('label'))]);
            $this->derivatives[$id]['route_parameters'] = [
              'user' => $user->id(),
              'type' => $config->get('id')
            ];
          }

        }
      }
    }

    return $this->derivatives;
  }

}
