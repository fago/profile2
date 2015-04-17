<?php

/**
 * @file
 * Contains \Drupal\profile\ProfileViewsData.
 */

namespace Drupal\profile;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides the views data for the node entity type.
 */
class ProfileViewsData extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['profile']['table']['group'] = t('Profile');

    $data['profile']['table']['base'] = array(
      'field' => 'pid',
      'title' => t('Profile'),
      'help' => t('Profile items are created by users.'),
    );
    $data['profile']['table']['entity type'] = 'profile';

    $data['profile']['pid'] = array(
      'title' => t('Profile ID'),
      'help' => t('The unique ID of the profile item.'),
      'field' => array(
        'id' => 'numeric',
      ),
      'argument' => array(
        'id' => 'profile_pid',
        'name field' => 'title',
        'numeric' => TRUE,
      ),
      'filter' => array(
        'id' => 'numeric',
      ),
      'sort' => array(
        'id' => 'standard',
      ),
    );

    $data['profile']['label'] = array(
      'title' => t('Label'),
      'help' => t('The label of the profile item.'),
      'field' => array(
        'id' => 'profile_label',
      ),
    );

    $data['profile_field_data']['type'] = array(
      'title' => t('Type'),
      'help' => t('The type of profile of the item.'),
      'field' => array(
        'id' => 'standard',
      ),
      'argument' => array(
        'id' => 'string',
      ),
      'sort' => array(
        'id' => 'standard',
      ),
      'filter' => array(
        'id' => 'bundle',
      ),
    );

    $data['profile_field_data']['status'] = array(
      'title' => t('Status'),
      'help' => t('Whether or not the profile is active.'),
      'field' => array(
        'id' => 'boolean',
        'output formats' => array(
          'active-notactive' => array(t('Active'), t('Not active')),
        ),
      ),
      'filter' => array(
        'id' => 'boolean',
        'label' => t('Status'),
        'type' => 'yes-no',
        // Use status = 1 instead of status <> 0 in WHERE statement.
        'use_equal' => TRUE,
      ),
      'sort' => array(
        'id' => 'standard',
      ),
    );

    $data['profile_field_data']['uid'] = array(
      'title' => t('Owner UID'),
      'help' => t('The owner of the profile.'),
      'field' => array(
        'id' => 'numeric',
      ),
      'filter' => array(
        'id' => 'numeric',
      ),
      'argument' => array(
        'id' => 'numeric',
      ),
      'sort' => array(
        'id' => 'standard',
      ),
      'relationship' => array(
        'title' => t('User'),
        'help' => t('The owner of the profile.'),
        'base' => 'users',
        'base field' => 'uid',
        'id' => 'standard',
      ),
    );

    $data['profile_field_data']['created'] = array(
      'title' => t('Created'),
      'help' => t('The date the profile item was created.'),
      'field' => array(
        'id' => 'date',
      ),
      'sort' => array(
        'id' => 'date',
      ),
      'filter' => array(
        'id' => 'date',
      ),
      'argument' => array(
        'id' => 'date',
      ),
    );

    $data['profile_field_data']['changed'] = array(
      'title' => t('Updated'),
      'help' => t('The date the profile item was last updated.'),
      'field' => array(
        'id' => 'date',
      ),
      'sort' => array(
        'id' => 'date',
      ),
      'filter' => array(
        'id' => 'date',
      ),
      'argument' => array(
        'id' => 'date',
      ),
    );

    $data['profile']['view_profile'] = array(
      'field' => array(
        'title' => t('Link to profile'),
        'help' => t('Provide a simple link to the profile.'),
        'id' => 'profile_link',
      ),
    );

    $data['profile']['edit_profile'] = array(
      'field' => array(
        'title' => t('Link to edit profile'),
        'help' => t('Provide a simple link to edit the profile.'),
        'id' => 'profile_link_edit',
      ),
    );

    $data['profile']['delete_profile'] = array(
      'field' => array(
        'title' => t('Link to delete profile'),
        'help' => t('Provide a simple link to delete the profile.'),
        'id' => 'profile_link_delete',
      ),
    );

    $data['profile']['profile_bulk_form'] = array(
      'title' => t('Profile operations bulk form'),
      'help' => t('Add a form element that lets you run operations on multiple profiles.'),
      'field' => array(
        'id' => 'profile_bulk_form',
      ),
    );

    return $data;
  }

}
