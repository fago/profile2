<?php

/**
 * @file
 * Contains Drupal\profile2\Tests\ProfileEditTest.
 */

namespace Drupal\profile2\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests creating and editing of profiles.
 */
class ProfileEditTest extends WebTestBase {

  public static $modules = array('profile2', 'text');

  public static function getInfo() {
    return array(
      'name' => 'Profile editing',
      'description' => 'Tests creating and editing of profiles.',
      'group' => 'Profile2',
    );
  }

  function setUp() {
    parent::setUp();

    $type = entity_create('profile2_type', array(
      'id' => 'test',
      'label' => 'label',
      'weight' => 0,
      'registration' => TRUE,
    ));
    $type->save();
    $type = entity_create('profile2_type', array(
      'id' => 'test2',
      'label' => 'label2',
      'weight' => 2,
    ));
    $type->save();

    $field = array(
      'field_name' => 'profile_fullname',
      'type' => 'text',
      'cardinality' => 1,
      'translatable' => FALSE,
    );
    field_create_field($field);
    $instance = array(
      'entity_type' => 'profile2',
      'field_name' => 'profile_fullname',
      'bundle' => 'test',
      'label' => 'Full name',
      'description' => 'Specify your first and last name.',
      'widget' => array(
        'type' => 'text_textfield',
        'weight' => 0,
      ),
    );
    field_create_instance($instance);

    $this->checkPermissions(array(), TRUE);
  }

  /**
   * Tests CRUD for a profile related to a user and one unrelated to a user.
   */
  function testCRUD() {
    $user1 = $this->drupalCreateUser();
    // Create profiles for the user1 and unrelated to a user.
    entity_create('profile2', array('type' => 'test', 'uid' => $user1->uid))->save();
    entity_create('profile2', array('type' => 'test2', 'uid' => $user1->uid))->save();
    $profile = entity_create('profile2', array('type' => 'test', 'uid' => NULL));
    $profile->save();

    $entities = entity_load_multiple_by_properties('profile2', array(
      'uid' => $user1->uid,
    ));
    $profiles = array();
    foreach ($entities as $item) {
      $profiles[$item->type] = $item;
    }

    $this->assertEqual($profiles['test']->label(), 'label', 'Created and loaded profile 1.');
    $this->assertEqual($profiles['test2']->label(), 'label2', 'Created and loaded profile 2.');

    $loaded = entity_load('profile2', $profile->pid);
    $this->assertEqual($loaded->pid, $profile->pid, 'Loaded profile unrelated to a user.');

    $profiles['test']->delete();
    $entities = entity_load_multiple_by_properties('profile2', array(
      'uid' => $user1->uid,
    ));
    $profiles2 = array();
    foreach ($entities as $item) {
      $profiles2[$item->type] = $item;
    }

    $this->assertEqual(array_keys($profiles2), array('test2'), 'Profile successfully deleted.');

    $profiles2['test2']->save();
    $this->assertEqual($profiles['test2']->pid, $profiles2['test2']->pid, 'Profile successfully updated.');

    // Delete a profile type.
    profile2_type_load('test')->delete();

    // Try deleting multiple profiles by deleting all existing profiles.
    $pids = array_keys(entity_load_multiple('profile2'));
    $this->assertTrue($pids);
    entity_delete_multiple('profile2', $pids);
  }

  /**
   * Test registration integration.
   */
  function testRegistrationIntegration() {
    // Allow registration by site visitors without administrator approval.
    config('user.settings')->set('register', USER_REGISTER_VISITORS)->save();
    $edit = array();
    $edit['name'] = $name = $this->randomName();
    $edit['mail'] = $mail = $edit['name'] . '@example.com';
    $edit['profile_test[profile_fullname][und][0][value]'] = $this->randomName();
    $this->drupalPost('user/register', $edit, t('Create new account'));
    $this->assertText(t('A welcome message with further instructions has been sent to your e-mail address.'), t('User registered successfully.'));
    $new_user = user_load_by_name($name);
    $this->assertTrue((bool) $new_user->status, t('New account is active after registration.'));

    $profiles = entity_load_multiple_by_properties('profile2', array(
      'uid' => $new_user->uid,
      'type' => 'test',
    ));
    $profile = reset($profiles);
    $this->assertEqual($profile->profile_fullname[LANGUAGE_NOT_SPECIFIED][0]['value'], $edit['profile_test[profile_fullname][und][0][value]'], 'Profile created.');
  }

  /**
   * Test basic edit and display.
   */
  function testEditAndDisplay() {
    user_role_revoke_permissions(DRUPAL_AUTHENTICATED_RID, array('edit own test profile', 'view own test profile'));
    $user1 = $this->drupalCreateUser();
    $this->drupalLogin($user1);

    // Make sure access is denied to the profile.
    $this->drupalGet('user/' . $user1->uid . '/edit/test');
    $this->assertText(t('Access denied'), 'Access has been denied.');

    // Test creating a profile manually (e.g. by an admin) and ensure the user
    // may not see it.
    entity_create('profile2', array('type' => 'test', 'uid' => $user1->uid))->save();
    $this->drupalGet('user/' . $user1->uid);
    $this->assertNoText('label', 'Profile data is not visible to the owner.');

    $user2 = $this->drupalCreateUser(array('edit own test profile', 'view own test profile'));
    $this->drupalLogin($user2);

    // Create profiles for the user2.
    $edit['profile_fullname[und][0][value]'] = $this->randomName();
    $this->drupalPost('user/' . $user2->uid . '/edit/test', $edit, t('Save'));
    $this->assertText(t('Your profile has been saved.'), 'Profile saved.');

    $profiles = entity_load_multiple_by_properties('profile2', array(
      'uid' => $user2->uid,
      'type' => 'test',
    ));
    $profile = reset($profiles);
    $this->assertEqual($profile->profile_fullname[LANGUAGE_NOT_SPECIFIED][0]['value'], $edit['profile_fullname[und][0][value]'], 'Profile edited.');

    $this->drupalGet('user/' . $user2->uid);
    $this->assertText(check_plain($edit['profile_fullname[und][0][value]']), 'Profile displayed.');
  }

}
