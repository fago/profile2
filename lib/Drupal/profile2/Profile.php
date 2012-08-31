<?php

/**
 * @file
 * Contains Drupal\profile2\Profile.
 */

namespace Drupal\profile2;

use Drupal\entity\Entity;

/**
 * The class used for profile entities.
 */
class Profile extends Entity {

  /**
   * The profile id.
   *
   * @var integer
   */
  public $pid;

  /**
   * The name of the profile type.
   *
   * @var string
   */
  public $type;

  /**
   * The profile label.
   *
   * @var string
   */
  public $label;

  /**
   * The user id of the profile owner.
   *
   * @var integer
   */
  public $uid;

  /**
   * The Unix timestamp when the profile was created.
   *
   * @var integer
   */
  public $created;

  /**
   * The Unix timestamp when the profile was most recently saved.
   *
   * @var integer
   */
  public $changed;

  public function __construct(array $values = array(), $entity_type) {
    if (isset($values['user'])) {
      $this->setUser($values['user']);
      unset($values['user']);
    }
    if (isset($values['type']) && is_object($values['type'])) {
      $values['type'] = $values['type']->type;
    }
    if (!isset($values['label']) && isset($values['type']) && $type = profile2_type_load($values['type'])) {
      // Initialize the label with the type label, so newly created profiles
      // have that as interim label.
      $values['label'] = $type->label;
    }
    parent::__construct($values, $entity_type);
  }

  /**
   * Returns the user owning this profile.
   */
  public function user() {
    return user_load($this->uid);
  }

  /**
   * Sets a new user owning this profile.
   *
   * @param $account
   *   The user account object or the user account id (uid).
   */
  public function setUser($account) {
    $this->uid = is_object($account) ? $account->uid : $account;
  }

  /**
   * Gets the associated profile type object.
   *
   * @return ProfileType
   */
  public function type() {
    return profile2_type_load($this->type);
  }

  /**
   * Overwrites EntityInterface::id().
   */
  public function id() {
    return isset($this->pid) ? $this->pid : NULL;
  }

  /**
   * Overwrites EntityInterface::bundle().
   */
  public function bundle() {
    return $this->type;
  }

  /**
   * Returns the full url() for the profile.
   */
  public function url() {
    $uri = $this->uri();
    return url($uri['path'], $uri);
  }

  /**
   * Returns the drupal path to this profile.
   */
  public function path() {
    $uri = $this->uri();
    return $uri['path'];
  }

  public function defaultUri() {
    return array(
      'path' => 'user/' . $this->uid,
      'options' => array('fragment' => 'profile-' . $this->type),
    );
  }

  public function defaultLabel() {
    if (module_exists('profile2_i18n')) {
      // Run the label through i18n_string() using the profile2_type label
      // context, so the default label (= the type's label) gets translated.
      return entity_i18n_string('profile2:profile2_type:' . $this->type . ':label', $this->label);
    }
    return $this->label;
  }

  public function buildContent($view_mode = 'full', $langcode = NULL) {
    $content = array();

    // Assume newly create objects are still empty.
    if (!empty($this->is_new)) {
      $content['empty']['#markup'] = '<em class="profile2-no-data">' . t('There is no profile data yet.') . '</em>';
    }

    if (!isset($langcode)) {
      $langcode = language(LANGUAGE_TYPE_CONTENT)->langcode;
    }

    // Allow modules to change the view mode.
    $context = array('langcode' => $langcode);
    drupal_alter('entity_view_mode', $view_mode, $this, $context);

    // Build fields content.
    // In case of a multiple view, node_view_multiple() already ran the
    // 'prepare_view' step. An internal flag prevents the operation from running
    // twice.
    field_attach_prepare_view('profile2', array($this->id() => $this), $view_mode, $langcode);
    entity_prepare_view('profile2', array($this->id() => $this), $langcode);
    $content += field_attach_view('profile2', $this, $view_mode, $langcode);

    return $content;
  }

  public function save() {
    // Care about setting created and changed values. But do not automatically
    // set a created values for already existing profiles.
    if (empty($this->created) && (!empty($this->is_new) || !$this->pid)) {
      $this->created = REQUEST_TIME;
    }
    $this->changed = REQUEST_TIME;

    parent::save();
    // Update the static cache from profile2_load_by_user().
    $cache = &drupal_static('profile2_load_by_user', array());
    if (isset($cache[$this->uid])) {
      $cache[$this->uid][$this->type] = $this->pid;
    }
  }
}

