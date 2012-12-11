<?php

/**
 * @file
 * Contains Drupal\profile2\Tests\ProfileFieldAccessTest.
 */

namespace Drupal\profile2\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests profile field access functionality.
 */
class ProfileFieldAccessTest extends WebTestBase {

  public static $modules = array('profile2', 'text', 'field_ui');

  public static function getInfo() {
    return array(
      'name' => 'Field access',
      'description' => 'Tests profile field access functionality.',
      'group' => 'Profile2',
    );
  }

  function setUp() {
    parent::setUp();

    $this->type = entity_create('profile2_type', array(
      'id' => 'personal',
      'label' => 'Personal data',
      'weight' => 0,
      'registration' => TRUE,
    ));
    $this->type->save();

    $this->checkPermissions(array(), TRUE);
    $this->admin_user = $this->drupalCreateUser(array(
      'access user profiles',
      'administer profile types',
      'bypass profile access',
    ));
    $user_permissions = array(
      'access user profiles',
      'edit own personal profile',
      'view any personal profile',
    );
    $this->web_user = $this->drupalCreateUser($user_permissions);
    $this->other_user = $this->drupalCreateUser($user_permissions);
  }

  /**
   * Tests private profile field access.
   */
  function testPrivateField() {
    $id = $this->type->id();

    $this->drupalLogin($this->admin_user);

    // Create a private profile field.
    $edit = array(
      'fields[_add_new_field][label]' => 'Secret',
      'fields[_add_new_field][field_name]' => 'secret',
      'fields[_add_new_field][type]' => 'text',
      'fields[_add_new_field][widget_type]' => 'text_textfield',
    );
    $this->drupalPost("admin/people/profiles/manage/$id/fields", $edit, t('Save'));

    $edit = array(
      'field[settings][profile2_private]' => 1,
    );
    $this->drupalPost(NULL, $edit, t('Save field settings'));

    $this->drupalPost(NULL, array(), t('Save settings'));

    // Fill in a field value.
    $this->drupalLogin($this->web_user);
    $uid = $this->web_user->id();
    $secret = $this->randomName();
    $edit = array(
      'field_secret[und][0][value]' => $secret,
    );
    $this->drupalPost("user/$uid/edit/$id", $edit, t('Save'));

    // Verify that the private field value appears for the profile owner.
    $this->drupalGet("user/$uid");
    $this->assertText($secret);

    // Verify that the private field value appears for the administrator.
    $this->drupalLogin($this->admin_user);
    $this->drupalGet("user/$uid");
    $this->assertText($secret);

    // Verify that the private field value does not appear for other users.
    $this->drupalLogin($this->other_user);
    $this->drupalGet("user/$uid");
    $this->assertNoText($secret);
  }

}
