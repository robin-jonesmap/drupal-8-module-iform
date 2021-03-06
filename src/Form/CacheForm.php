<?php

/**
 * @file
 * Contains \Drupal\iform\Form\SettingsForm.
 */

namespace Drupal\iform\Form;

use Drupal\Core\Form\FormBase;

class CacheForm extends FormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'iform_cache_form';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = array();
    $form['instruction'] = array(
      '#markup' => '<p>' . t('When Indicia requests data from the Warehouse, it can cache a local copy of ' .
          'these data to help speed up future requests for the same data. Although this makes a significant ' .
          'improvement to your website\'s performance, it can mean that changes to data are not visible ' .
          'on your website for several hours. Clear the cache to ensure that the latest copy of all data ' .
          'is loaded.') . '</p>'
    );
    $query = \Drupal::entityQuery('node')->condition('type', 'iform_page')->range(0, 1);
    $nids = $query->execute();
    if (count($nids)) {
      $nid = array_pop($nids);
      global $base_url;
      $url = $base_url . \Drupal::service('path.alias_manager')->getAliasByPath("/node/$nid");
      $urlWithoutCache = $url . (strpos($url, '?') === FALSE ? '?' : '&') . 'nocache';
      $example = ' ' . t('For example, you can change the URL <a href="!url">!url</a> to ' .
          '<a href="!urlWithoutCache">!urlWithoutCache</a> to render the page without using the Indicia cache.',
          array('!url' => $url, '!urlWithoutCache' => $urlWithoutCache));
    } else {
      $example = '--';
    }
    $form['nocache'] = array(
      '#markup' => '<p>' . t('If you want to test changes to an Indicia page without forcing a hard-reset ' .
          'of the entire cache, then you can add a parameter called <strong>nocache</strong> to the URL of the page to reload ' .
          'it without using the Indicia cache.') . $example . '</p>'
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Clear Indicia cache'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    iform_load_helpers(array('data_entry_helper'));
    \data_entry_helper::clear_cache();
    drupal_set_message(t('The Indicia cache has been cleared.'), 'status');
  }

}