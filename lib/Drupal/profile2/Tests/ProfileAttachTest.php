<?php

/**
 * @file
 * Contains \Drupal\profile2\Tests\ProfileAttachTest.
 */

namespace Drupal\profile2\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests attaching of profile entity forms to other forms.
 */
class ProfileAttachTest extends WebTestBase {

  public static $modules = array('profile2', 'text');

  public static function getInfo() {
    return array(
      'name' => 'Profile form attachment',
      'description' => 'Tests attaching of profile entity forms to other forms.',
      'group' => 'Profile2',
    );
  }

  function setUp() {
    parent::setUp();

    $this->type = entity_create('profile2_type', array(
      'id' => 'test',
      'label' => 'Test profile',
      'weight' => 0,
      'registration' => TRUE,
    ));
    $this->type->save();

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
      'required' => TRUE,
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
  }

  /**
   * Test user registration integration.
   */
  function testUserRegisterForm() {
    $id = $this->type->id();
    $field_name = $this->field['field_name'];

    // Allow registration without administrative approval and log in user
    // directly after registering.
    config('user.settings')
      ->set('register', USER_REGISTER_VISITORS)
      ->set('verify_mail', 0)
      ->save();
    user_role_grant_permissions(DRUPAL_AUTHENTICATED_RID, array('view own test profile'));

    // Verify that the additional profile field is attached and required.
    $name = $this->randomName();
    $pass_raw = $this->randomName();
    $edit = array(
      'name' => $name,
      'mail' => $this->randomName() . '@example.com',
      'pass[pass1]' => $pass_raw,
      'pass[pass2]' => $pass_raw,
    );
    $this->drupalPost('user/register', $edit, t('Create new account'));
    $this->assertRaw(t('@name field is required.', array('@name' => $this->instance['label'])));

    // Verify that we can register.
    $edit["profile[$id][$field_name][und][0][value]"] = $this->randomName();
    $this->drupalPost(NULL, $edit, t('Create new account'));
    $this->assertText(t('Registration successful. You are now logged in.'));

    $new_user = user_load_by_name($name);
    $this->assertTrue($new_user->status, 'New account is active after registration.');

    // Verify that a new profile was created for the new user ID.
    $profiles = entity_load_multiple_by_properties('profile2', array(
      'uid' => $new_user->id(),
      'type' => $this->type->id(),
    ));
    $profile = reset($profiles);
    $this->assertEqual($profile->{$field_name}[LANGUAGE_NOT_SPECIFIED][0]['value'], $edit["profile[$id][$field_name][und][0][value]"], 'Field value found in loaded profile.');

    // Verify that the profile field value appears on the user account page.
    $this->drupalGet('user');
    $this->assertText($edit["profile[$id][$field_name][und][0][value]"], 'Field value found on user account page.');
  }

}
