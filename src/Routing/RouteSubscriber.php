<?php

/**
 * @file
 * Contains \Drupal\profile\Routing\RouteSubscriber.
 */

namespace Drupal\profile\Routing;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Subscriber for Profile routes.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * The entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new RouteSubscriber object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountInterface $account;
   *   Current drupal account.
   */
  public function __construct(EntityManagerInterface $entity_manager, AccountInterface $account) {
    $this->entityManager = $entity_manager;
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity.manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    foreach ($this->entityManager->getStorage('profile_type')->loadMultiple() as $profile_type_id => $profile_type) {
      $route = new Route(
        "/user/{user}/edit/user_profile_form/{profile_type}",
        array(
          'profile_type' => NULL,
          '_controller' => '\Drupal\profile\Controller\ProfileController::userProfileForm',
        ),
        array(
          '_profile_access_check' =>  'add'
        ),
        array(
          'parameters' => array(
            'user' => array(
              'type' => 'entity:user',
            ),
            'profile_type' => array(
              'type' => 'entity:profile_type',
            ),
            $profile_type_id => array(
              'type' => 'profile:' . $profile_type_id,
            ),
          ),
        )
      );
      $collection->add("entity.profile.type.$profile_type_id.user_profile_form", $route);
    }
  }

}
