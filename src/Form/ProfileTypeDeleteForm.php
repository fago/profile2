<?php

/**
 * @file
 * Contains \Drupal\profile\Form\ProfileTypeDeleteForm.
 */

namespace Drupal\profile\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Logger\RfcLogLevel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a confirmation form for deleting a Profile type entity.
 */
class ProfileTypeDeleteForm extends EntityConfirmFormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new NodeTypeDeleteConfirm object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %label profile type?', array('%label' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('profile.overview_types');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $num_profiles = $this->database->query("SELECT COUNT(*) FROM {profile} WHERE type = :type", array(':type' => $this->entity->id()))
      ->fetchField();
    if ($num_profiles) {
      $caption = '<p>' . \Drupal::translation()
          ->formatPlural($num_profiles, '%type is used by 1 profile on your site. You can not remove this profile type until you have removed all of the %type profiles.', '%type is used by @count profiles on your site. You may not remove %type until you have removed all of the %type profiles.', array('%type' => $this->entity->label())) . '</p>';
      $form['#title'] = $this->entity->label();
      $form['description'] = array('#markup' => $caption);
      return $form;
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, FormStateInterface $form_state) {
    $this->entity->delete();

    \Drupal::service('logger.factory')
      ->get('profile')
      ->log(RfcLogLevel::NOTICE, 'Profile type %label has been deleted.', array('@type' => $this->entity->label()));

    drupal_set_message(t('Profile type %label has been deleted.', array('%label' => $this->entity->label())));

    $form_state->setRedirect('profile.overview_types');
  }

}
