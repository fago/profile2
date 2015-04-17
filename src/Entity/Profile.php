<?php

/**
 * @file
 * Contains \Drupal\profile\Entity\Profile.
 */

namespace Drupal\profile\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\profile\ProfileInterface;
use Drupal\user\UserInterface;


/**
 * Defines the profile entity class.
 *
 * @ContentEntityType(
 *   id = "profile",
 *   label = @Translation("Profile"),
 *   bundle_label = @Translation("Profile"),
 *   handlers = {
 *     "view_builder" = "Drupal\profile\ProfileViewBuilder",
 *     "views_data" = "Drupal\profile\ProfileViewsData",
 *     "access" = "Drupal\profile\ProfileAccessControlHandler",
 *     "list_builder" = "Drupal\profile\ProfileListBuilder",
 *     "form" = {
 *       "default" = "Drupal\profile\ProfileFormController",
 *       "add" = "Drupal\profile\ProfileFormController",
 *       "edit" = "Drupal\profile\ProfileFormController",
 *       "delete" = "Drupal\profile\Form\ProfileDeleteForm",
 *     },
 *   },
 *   bundle_entity_type = "profile_type",
 *   field_ui_base_route = "entity.profile_type.edit_form",
 *   admin_permission = "administer profiles",
 *   base_table = "profile",
 *   data_table = "profile_field_data",
 *   revision_table = "profile_revision",
 *   fieldable = TRUE,
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "pid",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid"
 *   },
 *  links = {
 *    "canonical" = "/profile/{profile}",
 *    "admin-form" = "/admin/config/people/profiles/types/manage/{profile_type}",
 *    "edit-form" = "/user/{user}/edit/profile/{profile_type}/{profile}",
 *    "delete-form" = "/profile/{profile}/delete"
 *   },
 * )
 */
class Profile extends ContentEntityBase implements ProfileInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['pid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Profile ID'))
      ->setDescription(t('The profile ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The profile UUID.'))
      ->setReadOnly(TRUE);

    $fields['vid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Revision ID'))
      ->setDescription(t('The profile revision ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Profile type'))
      ->setDescription(t('The profile type.'))
      ->setSetting('target_type', 'profile_type')
      ->setSetting('max_length', EntityTypeInterface::BUNDLE_MAX_LENGTH);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User ID'))
      ->setDescription(t('The user ID of the user associated with the profile.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The profile language code.'))
      ->setRevisionable(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Active status'))
      ->setDescription(t('A boolean indicating whether the profile is active.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the profile was created.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the profile was last edited.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

  /**
   * Overrides Entity::id().
   */
  public function id() {
    return $this->get('pid')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    $profile_type = ProfileType::load($this->bundle());
    return
      t('@type profile of @username (uid: @uid)',
        array(
          '@type' => $profile_type->label(),
          '@username' => $this->getOwner()->getUsername(),
          '@uid' => $this->getOwnerId()
        ));
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->bundle();
  }

  /**
   * {@inheritdoc}
   */
  public function setType($type) {
    $this->set('type', $this->bundle());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setChangedTime($timestamp) {
    $this->set('changed', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionCreationTime() {
    return $this->get('revision_timestamp')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionCreationTime($timestamp) {
    $this->set('revision_timestamp', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionAuthor() {
    return $this->get('revision_uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionAuthorId($uid) {
    $this->set('revision_uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isActive() {
    return (bool) $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setActive($active) {
    $this->set('status', $active ? PROFILE_ACTIVE : PROFILE_NOT_ACTIVE);
    return $this;
  }

}
