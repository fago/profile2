<?php

/**
 * @file
 * Provides Entity metadata integration.
 */

/**
 * Implements hook_entity_property_info_alter().
 */
function profile2_entity_property_info_alter(&$info) {
  // Add related profiles to the user object.
  $properties = &$info['user']['properties'];
  foreach (profile2_get_types() as $type_name => $type) {
    $properties['profile_' . $type_name] = array(
      'type' => 'profile2',
      'label' => t('@type_name profile', array('@type_name' => drupal_ucfirst($type->label))),
      'description' => t("The users's @type_name profile.", array('@type_name' => $type->label)),
      'getter callback' => 'profile2_user_get_properties',
      'bundle' => $type_name,
    );
  }
}
