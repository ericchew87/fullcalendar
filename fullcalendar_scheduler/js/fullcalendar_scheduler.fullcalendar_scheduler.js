/**
 * @file
 * Processes the FullCalendar options and passes them to the integration.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.fullcalendar.plugins.fullcalendar_scheduler = {
    options: function (fullcalendar, settings) {

      fullcalendar.parseEvents = function (callback) {
        var events = [];
        var details = this.$calendar.find('.fullcalendar-event-details');
        for (var i = 0; i < details.length; i++) {
          var event = $(details[i]);
          var eid = event.data('eid');
          events.push({
            field: event.data('field'),
            index: event.data('index'),
            eid: eid,
            entity_type: event.data('entity-type'),
            title: event.attr('title'),
            start: event.data('start'),
            end: event.data('end'),
            url: event.attr('href'),
            allDay: (event.data('all-day') === 1),
            className: event.data('cn'),
            editable: (event.data('editable') === 1),
            dom_id: this.dom_id,
            resourceId: settings.events[eid].resourceId,
          });
        }
        callback(events);
      };

      var options = Drupal.fullcalendar.plugins.fullcalendar.options(fullcalendar, settings);

      // Merge in our settings.
      $.extend(options, settings.fullcalendar_scheduler);
      
      options.resourceRender = function (resourceObj, labelTds, bodyTds) {
        labelTds.find('.fc-cell-text').html(resourceObj.html);
      };

      return options;
    }

  };

})(jQuery, Drupal, drupalSettings);
