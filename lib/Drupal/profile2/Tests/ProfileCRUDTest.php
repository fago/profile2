<?php

/**
 * @file
 * Contains Drupal\profile2\Tests\ProfileCRUDTest.
 */

namespace Drupal\profile2\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests basic CRUD functionality of profiles.
 */
class ProfileCRUDTest extends WebTestBase {

  public static $modules = array('profile2');

  public static function getInfo() {
    return array(
      'name' => 'Profile CRUD operations',
      'description' => 'Tests basic CRUD functionality of profiles.',
      'group' => 'Profile2',
    );
  }

  /**
   * Tests CRUD operations.
   */
  function testCRUD() {
    $types_data = array(
      0 => array('label' => $this->randomName()),
      1 => array('label' => $this->randomName()),
    );
    foreach ($types_data as $id => $values) {
      $types[$id] = entity_create('profile2_type', array('id' => $id) + $values);
      $types[$id]->save();
    }
    $this->user1 = $this->drupalCreateUser();
    $this->user2 = $this->drupalCreateUser();

    // Create a new profile.
    $profile = entity_create('profile2', $expected = array(
      'type' => $types[0]->id(),
      'uid' => $this->user1->id(),
    ));
    $this->assertIdentical($profile->id(), NULL);
    $this->assertIdentical($profile->type, $expected['type']);
    $this->assertIdentical($profile->label(), $types[0]->label());
    $this->assertIdentical($profile->uid, $this->user1->id());
    $this->assertIdentical($profile->created, REQUEST_TIME);
    $this->assertIdentical($profile->changed, NULL);

    // Save the profile.
    $status = $profile->save();
    $this->assertIdentical($status, SAVED_NEW);
    $this->assertTrue($profile->id());
    $this->assertIdentical($profile->changed, REQUEST_TIME);

    // List profiles for the user and verify that the new profile appears.
    $list = profile2_load_by_user($this->user1);
    $this->assertEqual($list, array(
      $profile->bundle() => $profile,
    ));

    // Reload and update the profile.
    $profile = entity_load('profile2', $profile->id());
    $profile->changed -= 1000;
    $original = clone $profile;
    $status = $profile->save();
    $this->assertIdentical($status, SAVED_UPDATED);
    $this->assertIdentical($profile->id(), $original->id());
    $this->assertEqual($profile->created, REQUEST_TIME);
    $this->assertEqual($original->changed, REQUEST_TIME - 1000);
    $this->assertEqual($profile->changed, REQUEST_TIME);

    // Create a second profile.
    $user1_profile1 = $profile;
    $profile = entity_create('profile2', array(
      'type' => $types[1]->id(),
      'uid' => $this->user1->id(),
    ));
    $status = $profile->save();
    $this->assertIdentical($status, SAVED_NEW);
    $user1_profile2 = $profile;

    // List profiles for the user and verify that both profiles appear.
    $list = profile2_load_by_user($this->user1);
    $this->assertEqual($list, array(
      $user1_profile1->bundle() => $user1_profile1,
      $user1_profile2->bundle() => $user1_profile2,
    ));

    // Delete the second profile and verify that the first still exists.
    $user1_profile2->delete();
    $this->assertFalse(entity_load('profile2', $user1_profile2->id()));
    $list = profile2_load_by_user($this->user1);
    $this->assertEqual($list, array(
      $user1_profile1->bundle() => $user1_profile1,
    ));

    // Create a new second profile.
    $user1_profile2 = entity_create('profile2', array(
      'type' => $types[1]->id(),
      'uid' => $this->user1->id(),
    ));
    $status = $user1_profile2->save();
    $this->assertIdentical($status, SAVED_NEW);

    // Create a profile for the second user.
    $user2_profile1 = entity_create('profile2', array(
      'type' => $types[0]->id(),
      'uid' => $this->user2->id(),
    ));
    $status = $user2_profile1->save();
    $this->assertIdentical($status, SAVED_NEW);

    // Delete the first user and verify that all of its profiles are deleted.
    $this->user1->delete();
    $this->assertFalse(entity_load('user', $this->user1->id()));
    $list = profile2_load_by_user($this->user1);
    $this->assertEqual($list, array());

    // List profiles for the second user and verify that they still exist.
    $list = profile2_load_by_user($this->user2);
    $this->assertEqual($list, array(
      $user2_profile1->bundle() => $user2_profile1,
    ));

    // @todo
    // Rename a profile type and verify that existing profiles are updated.
    // Verify expected behavior of 'uid' => NULL; Profile2 supported this for whatever reason.
  }

}
