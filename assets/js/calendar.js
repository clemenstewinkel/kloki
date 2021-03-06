import 'bootstrap/dist/css/bootstrap.css'
import '@fullcalendar/core/main.css';
import '@fullcalendar/daygrid/main.css';
import '@fullcalendar/timegrid/main.css';
import 'jquery-contextmenu/dist/jquery.contextMenu.min.css';
import create_context_menus from "./calendar_helper_functions/create_context_menus";
import loadShowEvent from "./calendar_helper_functions/loadShowEvent";
import loadNewEventForm from "./calendar_helper_functions/loadNewEventForm";
import eventRenderFunction from "./calendar_helper_functions/eventRenderFunction";
import eventDropped from "./calendar_helper_functions/eventDropped";
import eventResized from "./calendar_helper_functions/eventResized";
import eventDataTransform from "./calendar_helper_functions/eventDataTransform";

import {Calendar}             from '@fullcalendar/core';
import dayGridPlugin          from '@fullcalendar/daygrid';
import interactionPlugin      from '@fullcalendar/interaction';
import bootstrapPlugin        from "@fullcalendar/bootstrap";
import timeGridPlugin         from "@fullcalendar/timegrid";
import resourceTimeGridPlugin from '@fullcalendar/resource-timegrid';

import $ from 'jquery';
import 'bootstrap'

let fullcalendar;
let calendarEl;
let userIsAuthenticated;

export let $calendarDetail;
export let userId;
export let userRoles;

/**
 * Hauptprogramm
 */

$(document).ready(function() {
    // store resources here and create fullcalendar after successfull getting the resources.
    // Letting fullcalendar fetch the resources leads to errors sometimes if
    // fetching resources finishes after fetching first events.
    let resources;
    calendarEl = document.getElementById('calendar');
    $calendarDetail = $('#js-calendar-detail');

    $.datetimepicker.setLocale('de');

    userIsAuthenticated = $('body').data('isAuthenticated');
    userId              = $('body').data('userId');
    userRoles           = $('body').data('userRoles');
//    userRoles = ["ROLE_ADMIN", "ROLE_USER", "ROLE_HELPER"];
    console.log("Angemeldet: " + userIsAuthenticated + ', User-ID: ' + userId, ', Rollen: ' + userRoles.join(', '));

    $.ajax({
        url: '../room/getResources',
        type: 'GET',
        async: true,
        success: function(response){
            resources=response;
            console.log("resources fetched");
            fullcalendar = new Calendar(
                calendarEl,
                {
                    plugins: [ dayGridPlugin, interactionPlugin, bootstrapPlugin, timeGridPlugin, resourceTimeGridPlugin],
                    height: 'parent',
                    firstDay: 1,
                    fixedWeekCount: false,
                    navLinks: true,
                    // editable and resourceEditable are set in EventDataTransform for the single event!
                    editable: userRoles.includes('ROLE_ADMIN') || userRoles.includes('ROLE_FOOD'),
                    locale: 'de',
                    timeZone: 'UTC',
                    eventRender: eventRenderFunction,
                    eventOverlap: false,
                    resources: resources, 
                    dateClick: loadNewEventForm,
                    weekNumbers: true,
                    eventResize: eventResized,
                    eventDrop: eventDropped,
                    eventDragStop: function(info){console.log('DragStop'); return false;},
                    eventResizeStop: function(info){console.log('ResizeStop')},
                    eventClick: function(info){loadShowEvent(info.event._def.publicId);},
                    header: {left: 'resourceTimeGridDay timeGridWeek dayGridMonth', center: 'title'},
                    buttonText: {
                        today:    'heute',
                        month:    'Monat',
                        week:     'Woche',
                        day:      'Tag',
                        list:     'Liste'
                    },
                    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                    forceEventDuration: true,
                    defaultTimedEventDuration: '04:00',
                    eventSources: [{
                        url: "eventRange",
                        method: "GET",
                        eventDataTransform: eventDataTransform,
//                        extraParams: {
//                            filters: JSON.stringify({})
//                        },
                        failure: event_source_failure
                    }],
                });
            fullcalendar.render();
            create_context_menus();
        
            window.fullcalendar = fullcalendar;
            window.$ = $;
        }
    });
});

function event_source_failure() {
    alert("There was a problem.");
}
