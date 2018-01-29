<?php

namespace Drupal\fullcalendar_scheduler\Plugin\fullcalendar\type;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\fullcalendar\Plugin\FullcalendarBase;

/**
 * Provides the Fullcalendar Scheduler integration.
 *
 * @FullcalendarOption(
 *   id = "fullcalendar_scheduler",
 *   module = "fullcalendar_scheduler",
 *   weight = "10",
 * )
 */
class FullcalendarScheduler extends FullcalendarBase {

  /**
   * {@inheritdoc}
   */
  public function defineOptions() {
    $options = [];
    $options['fullcalendar_scheduler'] = [
      'contains' => [
        'enabled' => ['default' => FALSE],
        'resource_field' => ['default' => NULL],
        'defaultView' => ['default' => 'timelineDay'],
        'views' => ['default' => [
          'timelineDay' => [
            'slotLabelFormat' => '',
          ],
          'agendaDay' => [
            'slotLabelFormat' => '',
          ],
          'timelineWeek' => [
            'slotLabelFormat' => '',
          ],
          'agendaWeek' => [
            'slotLabelFormat' => '',
          ],
          'timelineMonth' => [
            'slotLabelFormat' => '',
          ],
          'timelineYear' => [
            'slotLabelFormat' => '',
          ],
        ]],
        'schedulerLicenseKey' => ['default' => ''],
      ],
    ];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $form['fullcalendar_scheduler'] = [
      '#type' => 'details',
      '#title' => $this->t('Scheduler'),
      '#open' => TRUE,
    ];
    $form['fullcalendar_scheduler']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use the <em>Scheduler</em> display'),
      '#default_value' => $this->style->options['fullcalendar_scheduler']['enabled'],
      '#fieldset' => 'fullcalendar_scheduler',
    ];

    $field_labels = $this->style->displayHandler->getFieldLabels(TRUE);
    $form['fullcalendar_scheduler']['resource_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Resource field'),
      '#options' => $field_labels,
      '#default_value' => $this->style->options['fullcalendar_scheduler']['resource_field'],
      '#fieldset' => 'fullcalendar_scheduler',
    ];
    $form['fullcalendar_scheduler']['defaultView'] = [
      '#type' => 'select',
      '#title' => $this->t('Initial display'),
      '#options' => [
        'timelineDay' => $this->t('Day'),
        'agendaDay' => $this->t('Day (Agenda)'),
        'timelineWeek' => $this->t('Week'),
        'agendaWeek' => $this->t('Week (Agenda)'),
        'timelineMonth' => $this->t('Month'),
        'timelineYear' => $this->t('Year'),
      ],
      '#description' => Link::fromTextAndUrl($this->t('More info'), Url::fromUri('http://arshaw.com/fullcalendar/docs/views/Available_Views', ['attributes' => ['target' => '_blank']])),
      '#default_value' => $this->style->options['fullcalendar_scheduler']['defaultView'],
      '#fieldset' => 'fullcalendar_scheduler',
    ];
    $form['fullcalendar_scheduler']['views'] = [
      '#type' => 'details',
      '#title' => $this->t('Display configuration'),
    ];
    foreach ($form['fullcalendar_scheduler']['defaultView']['#options'] as $option => $label) {
      $form['fullcalendar_scheduler']['views'][$option] = [
        '#type' => 'details',
        '#title' => $label,
      ];
      $form['fullcalendar_scheduler']['views'][$option]['slotLabelFormat'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Header date format'),
        '#default_value' => $this->style->options['fullcalendar_scheduler']['views'][$option]['slotLabelFormat'],
        '#description' => Link::fromTextAndUrl($this->t('More info'), Url::fromUri('http://arshaw.com/fullcalendar/docs/agenda/slotLabelFormat', array('attributes' => array('target' => '_blank')))),
      ];
      $form['fullcalendar_scheduler']['views'][$option]['slotWidth'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Column width'),
        '#default_value' => $this->style->options['fullcalendar_scheduler']['views'][$option]['slotWidth'],
        '#field_suffix' => 'px',
        '#description' => Link::fromTextAndUrl($this->t('More info'), Url::fromUri('http://arshaw.com/fullcalendar/docs/agenda/slotWidth', array('attributes' => array('target' => '_blank')))),
      ];
    }

    $form['fullcalendar_scheduler']['schedulerLicenseKey'] = [
      '#type' => 'textfield',
      '#title' => $this->t('License key'),
      '#default_value' => $this->style->options['fullcalendar_scheduler']['schedulerLicenseKey'],
      '#description' => Link::fromTextAndUrl($this->t('More info'), Url::fromUri('https://fullcalendar.io/scheduler/license/', ['attributes' => ['target' => '_blank']])),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function process(&$settings) {
    if ($settings['fullcalendar_scheduler']['enabled']) {
      $default_view = $settings['fullcalendar_scheduler']['defaultView'];
      if (isset($settings['fullcalendar']['titleFormat']) && !is_array($settings['fullcalendar']['titleFormat'])) {
        $type = strtolower(substr($default_view, 8));
        $settings['fullcalendar']['titleFormat'] = $settings['fullcalendar']['titleFormat'][$type];
      }

      $resource_field = $settings['fullcalendar_scheduler']['resource_field'];

      $settings['events'] = [];
      $resources = [];
      foreach ($this->style->view->result as $delta => $row) {
        $field_value = $this->style->getFieldValue($delta, $resource_field);
        $field = (string) $this->style->getField($delta, $resource_field);

        $entity = $row->_entity;
        $settings['events'][$entity->id()] = [
          'resourceId' => $field_value,
        ];

        $resources[$field_value] = [
          'id' => $field_value,
          'title' => strip_tags(htmlspecialchars_decode($field)),
          'html' => $field,
        ];
      }
      $settings['fullcalendar_scheduler']['resources'] = array_values($resources);

      unset(
        $settings['fullcalendar_scheduler']['enabled'],
        $settings['fullcalendar_scheduler']['resource_field']
      );
    }
    else {
      unset($settings['fullcalendar_scheduler']);
    }
  }

}
