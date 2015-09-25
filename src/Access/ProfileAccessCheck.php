<?php

/**
 * @file
 * Contains \Drupal\profile\Access\ProfileAccessCheck.
 */
namespace Drupal\profile\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessCheckInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Drupal\profile\Entity\ProfileTypeInterface;
use Symfony\Component\HttpFoundation\RequestStack;


/**
 * Checks access to add, edit and delete profiles.
 */
class ProfileAccessCheck implements AccessCheckInterface {
  /**
   * A user account to check access for.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
 * The entity manager.
 *
 * @var \Drupal\Core\Entity\EntityManagerInterface
 */
  protected $entityManager;

  /**
   * Service RequestStack
   * @var RequestStack
   */
  protected $requestStack;

  /**
   * Constructs a EntityCreateAccessCheck object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   */
  public function __construct(EntityManagerInterface $entity_manager, RequestStack $requestStack) {
    $this->entityManager = $entity_manager;
    $this->requestStack = $requestStack;
  }

  /**
   * Implements AccessCheckInterface::applies().
   */
  public function applies(Route $route) {
    return FALSE;
  }

  public function access(AccountInterface $account, ProfileTypeInterface $profile_type = NULL) {
    $access_control_handler = $this->entityManager->getAccessControlHandler('profile');
    $operation = $this->requestStack->getCurrentRequest()->attributes->get('operation');
    if ($operation == 'add') {
      return $access_control_handler->access($profile_type, $operation, LanguageInterface::LANGCODE_DEFAULT, $account, TRUE);
    }

    // If checking whether a profile of a particular type may be created.
    if ($profile_type) {
      return $access_control_handler->createAccess($profile_type->id(), $account, [], TRUE);
    }
    // If checking whether a profile of any type may be created.
    foreach ($this->entityManager->getStorage('profile_type')->loadMultiple() as $profile_type) {
      if (($access = $access_control_handler->createAccess($profile_type->id(), $account, [], TRUE)) && $access->isAllowed()) {
        return $access;
      }
    }

    // No opinion.
    return AccessResult::neutral();
  }
}