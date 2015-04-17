<?php

/**
 * @file
 * Contains \Drupal\profile\Tests\ProfileTypeCRUDTest.
 */

namespace Drupal\profile\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Component\Utility\Unicode;

/**
 * Tests basic CRUD functionality of profile types.
 *
 * @group profile
 */
class ProfileTypeCRUDTest extends WebTestBase {

  public static $modules = array('profile', 'field_ui', 'text');

  /**
   * Tests CRUD operations for profile types through the UI.
   */
  function testCRUDUI() {
    $this->drupalLogin($this->rootUser);

    // Create a new profile type.
    $this->drupalGet('admin/config/people/profiles/types');
    $this->clickLink(t('Add profile type'));
    $this->assertUrl('admin/config/people/profiles/types/add');
    $id = Unicode::strtolower($this->randomMachineName());
    $label = $this->randomString();
    $edit = array(
      'id' => $id,
      'label' => $label,
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertUrl('admin/config/people/profiles/types');
    $this->assertRaw(format_string('%label profile type has been created.', array('%label' => $label)));
    $this->assertLinkByHref("admin/config/people/profiles/types/manage/$id");
    $this->assertLinkByHref("admin/config/people/profiles/types/manage/$id/fields");
    $this->assertLinkByHref("admin/config/people/profiles/types/manage/$id/display");
    $this->assertLinkByHref("admin/config/people/profiles/types/manage/$id/delete");

    // Edit the new profile type.
    $this->drupalGet("admin/config/people/profiles/types/manage/$id");
    $this->assertRaw(format_string('Edit %label profile type', array('%label' => $label)));
    $edit = array(
      'registration' => 1,
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertUrl('admin/config/people/profiles/types');
    $this->assertRaw(format_string('%label profile type has been updated.', array('%label' => $label)));

    // Add a field to the profile type.
    $this->drupalGet("admin/config/people/profiles/types/manage/$id/fields/add-field");
    $field_name = Unicode::strtolower($this->randomMachineName());
    $field_label = $this->randomString();
    $edit = array(
      'new_storage_type' => 'string',
      'label' => $field_label,
      'field_name' => $field_name,
    );
    $this->drupalPostForm(NULL, $edit, t('Save and continue'));
    $this->drupalPostForm(NULL, array(), t('Save field settings'));
    $this->drupalPostForm(NULL, array(), t('Save settings'));
    $this->assertUrl("admin/config/people/profiles/types/manage/$id/fields", array(
      'query' => array(
        'field_config' => "profile.$id.field_$field_name",
        'destinations[0]' => "admin/config/people/profiles/types/manage/$id/fields/add-field",
      )
    ));
    $this->assertRaw(format_string('Saved %label configuration.', array('%label' => $field_label)));

    // Rename the profile type ID.
    $this->drupalGet("admin/config/people/profiles/types/manage/$id");
    $new_id = Unicode::strtolower($this->randomMachineName());
    $edit = array(
      'id' => $new_id,
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertUrl('admin/config/people/profiles/types');
    $this->assertRaw(format_string('%label profile type has been updated.', array('%label' => $label)));
    $this->assertLinkByHref("admin/config/people/profiles/types/manage/$new_id");
    $this->assertNoLinkByHref("admin/config/people/profiles/types/manage/$id");
    $id = $new_id;

    // Verify that the field is still associated with it.
    $this->drupalGet("admin/config/people/profiles/types/manage/$id/fields");
    // @todo D8 core: This assertion fails for an unknown reason. Database
    //   contains the right values, so field_attach_rename_bundle() works
    //   correctly. The pre-existing field does not appear on the Manage
    //   fields page of the renamed bundle. Not even flushing all caches
    //   helps. Can be reproduced manually.
    //$this->assertText(check_plain($field_label));
  }

}
