<?php

/**
 * @file
 * Contains \Drupal\profile2\Tests\ProfileAccessTest.
 */

namespace Drupal\profile2\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests profile access handling.
 */
class ProfileAccessTest extends WebTestBase {

  public static $modules = array('profile2', 'text');

  public static function getInfo() {
    return array(
      'name' => 'Profile access',
      'description' => 'Tests profile access handling.',
      'group' => 'Profile2',
    );
  }

  function setUp() {
    parent::setUp();

    $this->type = entity_create('profile2_type', array(
      'id' => 'test',
      'label' => 'Test profile',
    ));
    $this->type->save();
    $id = $this->type->id();

    $this->field = array(
      'field_name' => 'profile_fullname',
      'type' => 'text',
      'cardinality' => 1,
      'translatable' => FALSE,
    );
    $this->field = field_create_field($this->field);
    $this->instance = array(
      'entity_type' => 'profile2',
      'field_name' => $this->field['field_name'],
      'bundle' => $this->type->id(),
      'label' => 'Full name',
      'widget' => array(
        'type' => 'text_textfield',
      ),
    );
    $this->instance = field_create_instance($this->instance);
    $this->display = entity_get_display('profile2', 'test', 'default')
      ->setComponent($this->field['field_name'], array(
        'type' => 'text_default',
      ));
    $this->display->save();

    $this->checkPermissions(array(), TRUE);

    user_role_grant_permissions(DRUPAL_AUTHENTICATED_RID, array('access user profiles'));
    $this->admin_user = $this->drupalCreateUser(array(
      'administer profile types',
      "view any $id profile",
      "edit any $id profile",
      "delete any $id profile",
    ));
  }

  /**
   * Tests administrative-only profiles.
   */
  function testAdminOnlyProfiles() {
    $id = $this->type->id();
    $field_name = $this->field['field_name'];

    // Create a test user account.
    $web_user = $this->drupalCreateUser(array('access user profiles'));
    $uid = $web_user->id();
    $value = $this->randomName();

    // Administratively enter profile field values for the new account.
    $this->drupalLogin($this->admin_user);
    $edit = array(
      "{$field_name}[und][0][value]" => $value,
    );
    $this->drupalPost("user/$uid/edit/$id", $edit, t('Save'));

    // Verify that the administrator can see the profile.
    $this->drupalGet("user/$uid");
    $this->assertText($this->type->label());
    $this->assertText($value);

    // Verify that the user can not access or edit the profile.
    $this->drupalLogin($web_user);
    $this->drupalGet("user/$uid");
    $this->assertNoText($this->type->label());
    $this->assertNoText($value);
    $this->drupalGet("user/$uid/edit/$id");
    $this->assertResponse(403);

    // Allow users to edit own profiles.
    user_role_grant_permissions(DRUPAL_AUTHENTICATED_RID, array("edit own $id profile"));

    // Verify that the user is able to edit the own profile.
    $value = $this->randomName();
    $edit = array(
      "{$field_name}[und][0][value]" => $value,
    );
    $this->drupalPost("user/$uid/edit/$id", $edit, t('Save'));
    $this->assertText(t('Your profile has been saved.'));

    // Verify that the own profile is still not visible on the account page.
    $this->drupalGet("user/$uid");
    $this->assertNoText($this->type->label());
    $this->assertNoText($value);

    // Allow users to view own profiles.
    user_role_grant_permissions(DRUPAL_AUTHENTICATED_RID, array("view own $id profile"));

    // Verify that the own profile is visible on the account page.
    $this->drupalGet("user/$uid");
    $this->assertText($this->type->label());
    $this->assertText($value);

    // Allow users to delete own profiles.
    user_role_grant_permissions(DRUPAL_AUTHENTICATED_RID, array("delete own $id profile"));

    // Verify that the user can delete the own profile.
    $this->drupalPost("user/$uid/edit/$id", array(), t('Delete'));
    $this->drupalPost(NULL, array(), t('Delete'));
    $this->assertRaw(t('Your %label profile has been deleted.', array('%label' => $this->type->label())));
    $this->assertUrl("user/$uid");

    // Verify that the profile is gone.
    $this->drupalGet("user/$uid");
    $this->assertNoText($this->type->label());
    $this->assertNoText($value);
    $this->drupalGet("user/$uid/edit/$id");
    $this->assertNoText($value);
  }

}
