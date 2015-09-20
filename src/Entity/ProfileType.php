<?php

/**
 * @file
 * Contains \Drupal\profile\Entity\ProfileType.
 */


namespace Drupal\profile\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the profile type entity class.
 *
 * @ConfigEntityType(
 *   id = "profile_type",
 *   label = @Translation("Profile type"),
 *   handlers = {
 *     "list_builder" = "Drupal\profile\ProfileTypeListBuilder",
 *     "form" = {
 *       "default" = "Drupal\profile\Form\ProfileTypeForm",
 *       "add" = "Drupal\profile\Form\ProfileTypeForm",
 *       "edit" = "Drupal\profile\Form\ProfileTypeForm",
 *       "delete" = "Drupal\profile\Form\ProfileTypeDeleteForm"
 *     },
 *   },
 *   admin_permission = "administer profile types",
 *   config_prefix = "type",
 *   bundle_of = "profile",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "registration",
 *     "multiple",
 *     "weight",
 *     "status",
 *     "langcode"
 *   },
 *   links = {
 *     "add-form" = "/admin/config/people/profiles/types/add",
 *     "delete-form" = "/admin/config/people/profiles/types/manage/{profile_type}/delete",
 *     "edit-form" = "/admin/config/people/profiles/types/manage/{profile_type}",
 *     "admin-form" = "/admin/config/people/profiles/types/manage/{profile_type}",
 *     "collection" = "/admin/config/people/profiles/types"
 *   }
 * )
 */
class ProfileType extends ConfigEntityBase implements ProfileTypeInterface {

  /**
   * The primary identifier of the profile type.
   *
   * @var integer
   */
  protected $id;

  /**
   * The universally unique identifier of the profile type.
   *
   * @var string
   */
  protected $uuid;

  /**
   * The human-readable name of the profile type.
   *
   * @var string
   */
  protected $label;

  /**
   * Whether the profile type is shown during registration.
   *
   * @var boolean
   */
  protected $registration = FALSE;

  /**
   * Whether the profile type allows multiple profiles.
   *
   * @var boolean
   */
  protected $multiple = FALSE;

  /**
   * The weight of the profile type compared to others.
   *
   * @var integer
   */
  protected $weight = 0;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->get('label')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegistration() {
    return $this->registration;
  }

  /**
   * {@inheritdoc}
   */
  public function setRegistration($registration) {
    $this->registration = $registration;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMultiple() {
    return $this->multiple;
  }

  /**
   * {@inheritdoc}
   */
  public function setMultiple($multiple) {
    $this->multiple = $multiple;
    return $this;
  }

}
