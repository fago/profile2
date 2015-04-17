<?php

/**
 * @file
 * Contains \Drupal\profile\Form\ProfileDeleteForm.
 */

namespace Drupal\profile\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\profile\Entity\ProfileType;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a confirmation form for deleting a profile entity.
 */
class ProfileDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * The URL generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Constructs a ProfileDeleteForm object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The URL generator.
   */
  public function __construct(EntityManagerInterface $entity_manager, UrlGeneratorInterface $url_generator) {
    parent::__construct($entity_manager);
    $this->urlGenerator = $url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('url_generator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete this profile?');
  }

  /**
   * {@inheritdoc}
   */
  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.user.canonical', array(
      'user' => $this->entity->getOwnerId(),
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $profile_type = ProfileType::load($this->entity->bundle());
    $this->entity->delete();

    $this->logger('profile')->notice('@type profile deleted.', array('@type' => $profile_type->id()));

    drupal_set_message(t('@type profile deleted.', array('@type' => $profile_type->label())));

    $form_state->setRedirect('entity.user.canonical', array('user' => $this->entity->getOwnerId()));
  }

}
