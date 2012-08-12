<?php

/**
 * @file
 * Contains Drupal\profile2\MetadataController.
 */

namespace Drupal\profile2;

use EntityDefaultMetadataController;

/**
 * Extend the defaults.
 */
class MetadataController extends EntityDefaultMetadataController {

  public function entityPropertyInfo() {
    $info = parent::entityPropertyInfo();
    $properties = &$info[$this->type]['properties'];

    $properties['label'] = array(
      'label' => t('Label'),
      'description' => t('The profile label.'),
      'setter callback' => 'entity_property_verbatim_set',
      'setter permission' => 'administer profiles',
      'schema field' => 'label',
    );

    $properties['type'] = array(
      'type' => 'profile2_type',
      'getter callback' => 'entity_property_getter_method',
      'setter callback' => 'entity_property_verbatim_set',
      'setter permission' => 'administer profiles',
      'required' => TRUE,
      'description' => t('The profile type.'),
    ) + $properties['type'];

    unset($properties['uid']);

    $properties['user'] = array(
      'label' => t("User"),
      'type' => 'user',
      'description' => t("The owner of the profile."),
      'getter callback' => 'entity_property_getter_method',
      'setter callback' => 'entity_property_setter_method',
      'setter permission' => 'administer profiles',
      'required' => TRUE,
      'schema field' => 'uid',
    );

    $properties['created'] = array(
      'label' => t("Date created"),
      'type' => 'date',
      'description' => t("The date the profile was created."),
      'setter callback' => 'entity_property_verbatim_set',
      'setter permission' => 'administer profiles',
      'schema field' => 'created',
    );
    $properties['changed'] = array(
      'label' => t("Date changed"),
      'type' => 'date',
      'schema field' => 'changed',
      'description' => t("The date the profile was most recently updated."),
    );

    return $info;
  }
}

