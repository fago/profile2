<?php

/**
 * @file
 * Contains \Drupal\profile\ProfileTypeFormController.
 */

namespace Drupal\profile;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field_ui\FieldUI;
use Drupal\profile\Entity\ProfileType;


/**
 * Form controller for profile type forms.
 */
class ProfileTypeFormController extends EntityForm {

  /**
   * {@inheritdoc}
   */
  function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $type = $this->entity;

    if ($this->operation == 'add') {
      $form['#title'] = SafeMarkup::checkPlain($this->t('Add profile type'));
    }
    else {
      $form['#title'] = $this->t('Edit %label profile type', array('%label' => $type->label()));
    }

    $form['label'] = array(
      '#title' => t('Label'),
      '#type' => 'textfield',
      '#default_value' => $type->label(),
      '#description' => t('The human-readable name of this profile type.'),
      '#required' => TRUE,
      '#size' => 30,
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => array(
        'exists' => array($this, 'exists'),
      ),
    );
    $form['registration'] = array(
      '#type' => 'checkbox',
      '#title' => t('Include in user registration form'),
      '#default_value' => $type->getRegistration(),
    );
    $form['multiple'] = array(
      '#type' => 'checkbox',
      '#title' => t('Allow multiple profiles'),
      '#default_value' => $type->getMultiple(),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    if (\Drupal::moduleHandler()->moduleExists('field_ui') &&
      $this->getEntity()->isNew()
    ) {
      $actions['save_continue'] = $actions['submit'];
      $actions['save_continue']['#value'] = t('Save and manage fields');
      $actions['save_continue']['#submit'][] = array(
        $this,
        'redirectToFieldUI'
      );
    }
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $type = $this->entity;
    $status = $type->save();

    if ($status == SAVED_UPDATED) {
      drupal_set_message(t('%label profile type has been updated.', array('%label' => $type->label())));
    }
    else {
      drupal_set_message(t('%label profile type has been created.', array('%label' => $type->label())));
    }
    $form_state->setRedirect('entity.profile_type.collection');
  }

  /**
   * Form submission handler to redirect to Manage fields page of Field UI.
   */
  public function redirectToFieldUI(array $form, FormStateInterface $form_state) {
    if ($form_state->getTriggeringElement()['#parents'][0] === 'save_continue' && $route_info = FieldUI::getOverviewRouteInfo('profile', $this->entity->id())) {
      $form_state->setRedirectUrl($route_info);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function delete(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('entity.profile_type.delete_form', array(
      'profile_type' => $this->entity->id()
    ));
  }

  /**
   * Check whether the profile type exists.
   *
   * @param $id
   * @return bool
   */
  public function exists($id) {
    $profile_type = ProfileType::load($id);
    return !empty($profile_type);
  }

}
