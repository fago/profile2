<?php

/**
 * @file
 * Contains \Drupal\profile\Tests\ProfileAccessTest.
 */

namespace Drupal\profile\Tests;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\simpletest\WebTestBase;

/**
 * Tests profile access handling.
 *
 * @group profile
 */
class ProfileAccessTest extends WebTestBase {

  public static $modules = array('profile', 'text');

  private $type;
  private $field;
  private $admin_user;

  function setUp() {
    parent::setUp();

    $this->type = entity_create('profile_type', array(
      'id' => 'test',
      'label' => 'Test profile',
    ));
    $this->type->save();
    $id = $this->type->id();

    $this->field = array(
      'field_name' => 'profile_fullname',
      'entity_type' => 'profile',
      'type' => 'text',
      'cardinality' => 1,
      'translatable' => FALSE,
    );
    $this->field = FieldStorageConfig::create($this->field);
    $this->field->save();

    $instance = array(
      'entity_type' => $this->field->get('entity_type'),
      'field_name' => $this->field->get('field_name'),
      'bundle' => $this->type->id(),
      'label' => 'Full name',
      'widget' => array(
        'type' => 'text_textfield',
      ),
    );
    $instance = FieldConfig::create($instance);
    $instance->save();

    $display = entity_get_display('profile', 'test', 'default')
      ->setComponent($this->field->get('field_name'), array(
        'type' => 'text_default',
      ));
    $display->save();

    entity_get_display('profile', 'test', 'default')
      ->setComponent($this->field->get('field_name'), array(
        'type' => 'text_default',
      ))
    ->save();

    entity_get_form_display('profile', 'test', 'default')
      ->setComponent($this->field->get('field_name'), array(
        'type' => 'string_textfield',
      ))
      ->save();

    $this->checkPermissions(array(), TRUE);

    user_role_grant_permissions(DRUPAL_AUTHENTICATED_RID, array('access user profiles'));
    $this->admin_user = $this->drupalCreateUser(array(
      'administer profile types',
      "view any $id profile",
      "add any $id profile",
      "edit any $id profile",
      "delete any $id profile",
    ));
  }

  /**
   * Tests administrative-only profiles.
   */
  function testAdminOnlyProfiles() {
    $id = $this->type->id();
    $field_name = $this->field->get('field_name');

    // Create a test user account.
    $web_user = $this->drupalCreateUser(array('access user profiles'));
    $uid = $web_user->id();
    $value = $this->randomMachineName();

    // Administratively enter profile field values for the new account.
    $this->drupalLogin($this->admin_user);
    $edit = array(
      "{$field_name}[0][value]" => $value,
    );
    $this->drupalPostForm("user/$uid/edit/profile/$id", $edit, t('Save'));

    $profiles = entity_load_multiple_by_properties('profile', array(
      'uid' => $uid,
      'type' => $this->type->id(),
    ));

    $profile = reset($profiles);
    $profile_id = $profile->id();

    // Verify that the administrator can see the profile.
    $this->drupalGet("user/$uid");
    $this->assertText($this->type->label());
    $this->assertText($value);
    $this->drupalLogout();

    // Verify that the user can not access, create or edit the profile.
    $this->drupalLogin($web_user);
    $this->drupalGet("user/$uid");
    $this->assertNoText($this->type->label());
    $this->assertNoText($value);
    $this->drupalGet("user/$uid/edit/profile/$id/$profile_id");
    $this->assertResponse(403);

    // Check edit link isn't displayed.
    $this->assertNoLinkByHref("user/$uid/edit/profile/$id/$profile_id");
    // Check delete link isn't displayed.
    $this->assertNoLinkByHref("user/$uid/delete/profile/$id/$profile_id");

    // Allow users to edit own profiles.
    user_role_grant_permissions(DRUPAL_AUTHENTICATED_RID, array("edit own $id profile"));

    // Verify that the user is able to edit the own profile.
    $value = $this->randomMachineName();
    $edit = array(
      "{$field_name}[0][value]" => $value,
    );
    $this->drupalPostForm("user/$uid/edit/profile/$id/$profile_id", $edit, t('Save'));
    $this->assertText(format_string('profile has been updated.'));

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
    $this->drupalGet("user/$uid/edit/profile/$id/$profile_id");
    $this->clickLink(t('Delete'));
    $this->drupalPostForm(NULL, array(), t('Delete'));
    $this->assertRaw(format_string('@label profile deleted.', array('@label' => $this->type->label())));
    $this->assertUrl("user/$uid");

    // Verify that the profile is gone.
    $this->drupalGet("user/$uid");
    $this->assertNoText($this->type->label());
    $this->assertNoText($value);
    $this->drupalGet("user/$uid/edit/profile/$id");
    $this->assertNoText($value);
  }

}
