<?php

/**
 * @file
 * Contains \Drupal\profile\Tests\ProfileCRUDTest.
 */

namespace Drupal\profile\Tests;

use Drupal\simpletest\KernelTestBase;

/**
 * Tests basic CRUD functionality of profiles.
 *
 * @group profile
 */
class ProfileCRUDTest extends KernelTestBase {

  public static $modules = ['system', 'field', 'entity_reference', 'field_sql_storage', 'user', 'profile'];

  function setUp() {
    parent::setUp();
    $this->installSchema('system', 'url_alias');
    $this->installSchema('system', 'sequences');
    $this->installSchema('user', 'users_data');
    $this->installEntitySchema('user');
    $this->installEntitySchema('profile');
    $this->enableModules(['field', 'entity_reference', 'user', 'profile']);
  }

  /**
   * Tests CRUD operations.
   */
  function testCRUD() {
    $types_data = [
      'profile_type_0' => ['label' => $this->randomMachineName()],
      'profile_type_1' => ['label' => $this->randomMachineName()],
    ];
    foreach ($types_data as $id => $values) {
      $types[$id] = entity_create('profile_type', ['id' => $id] + $values);
      $types[$id]->save();
    }
    $this->user1 = entity_create('user', [
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@example.com',
    ]);
    $this->user1->save();
    $this->user2 = entity_create('user', [
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@example.com',
    ]);
    $this->user2->save();

    // Create a new profile.
    $profile = entity_create('profile', $expected = [
      'type' => $types['profile_type_0']->id(),
      'uid' => $this->user1->id(),
    ]);
    $this->assertIdentical($profile->id(), NULL);
    $this->assertTrue($profile->uuid());
    $this->assertIdentical($profile->getType(), $expected['type']);
    $this->assertIdentical($profile->label(), t('@type profile of @username (uid: @uid)',
      [
        '@type' => $types['profile_type_0']->label(),
        '@username' => $this->user1->getUsername(),
        '@uid' => $this->user1->id(),
      ])
    );
    $this->assertIdentical($profile->getOwnerId(), $this->user1->id());
    $this->assertIdentical($profile->getCreatedTime(), REQUEST_TIME);
    $this->assertIdentical($profile->getChangedTime(), REQUEST_TIME);

    // Save the profile.
    $status = $profile->save();
    $this->assertIdentical($status, SAVED_NEW);
    $this->assertTrue($profile->id());
    $this->assertIdentical($profile->getChangedTime(), REQUEST_TIME);

    // List profiles for the user and verify that the new profile appears.
    $list = entity_load_multiple_by_properties('profile', [
      'uid' => $this->user1->id(),
    ]);
    $list_ids = array_keys($list);
    $this->assertEqual($list_ids, [(int) $profile->id()]);

    // Reload and update the profile.
    $profile = entity_load('profile', $profile->id());
    $profile->setChangedTime($profile->getChangedTime() - 1000);
    $original = clone $profile;
    $status = $profile->save();
    $this->assertIdentical($status, SAVED_UPDATED);
    $this->assertIdentical($profile->id(), $original->id());
    $this->assertEqual($profile->getCreatedTime(), REQUEST_TIME);
    $this->assertEqual($original->getChangedTime(), REQUEST_TIME - 1000);
    $this->assertEqual($profile->getChangedTime(), REQUEST_TIME);

    // Create a second profile.
    $user1_profile1 = $profile;
    $profile = entity_create('profile', [
      'type' => $types['profile_type_0']->id(),
      'uid' => $this->user1->id(),
    ]);
    $status = $profile->save();
    $this->assertIdentical($status, SAVED_NEW);
    $user1_profile = $profile;

    // List profiles for the user and verify that both profiles appear.
    $list = entity_load_multiple_by_properties('profile', [
      'uid' => $this->user1->id(),
    ]);
    $list_ids = array_keys($list);
    $this->assertEqual($list_ids, [
      (int) $user1_profile1->id(),
      (int) $user1_profile->id(),
    ]);

    // Delete the second profile and verify that the first still exists.
    $user1_profile->delete();
    $this->assertFalse(entity_load('profile', $user1_profile->id()));
    $list = entity_load_multiple_by_properties('profile', [
      'uid' => (int) $this->user1->id(),
    ]);
    $list_ids = array_keys($list);
    $this->assertEqual($list_ids, [(int) $user1_profile1->id()]);

    // Create a new second profile.
    $user1_profile = entity_create('profile', [
      'type' => $types['profile_type_1']->id(),
      'uid' => $this->user1->id(),
    ]);
    $status = $user1_profile->save();
    $this->assertIdentical($status, SAVED_NEW);

    // Create a profile for the second user.
    $user2_profile1 = entity_create('profile', [
      'type' => $types['profile_type_0']->id(),
      'uid' => $this->user2->id(),
    ]);
    $status = $user2_profile1->save();
    $this->assertIdentical($status, SAVED_NEW);

    // Delete the first user and verify that all of its profiles are deleted.
    $this->user1->delete();
    $this->assertFalse(entity_load('user', $this->user1->id()));
    $list = entity_load_multiple_by_properties('profile', [
      'uid' => $this->user1->id(),
    ]);
    $list_ids = array_keys($list);
    $this->assertEqual($list_ids, []);

    // List profiles for the second user and verify that they still exist.
    $list = entity_load_multiple_by_properties('profile', [
      'uid' => $this->user2->id(),
    ]);
    $list_ids = array_keys($list);
    $this->assertEqual($list_ids, [(int) $user2_profile1->id()]);

    // @todo Rename a profile type; verify that existing profiles are updated.
  }

}
