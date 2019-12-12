import 'bootstrap/dist/css/bootstrap.css'
import '@fullcalendar/core/main.css';
import '@fullcalendar/daygrid/main.css';
import '@fullcalendar/timegrid/main.css';
import 'jquery-contextmenu/dist/jquery.contextMenu.min.css';

import {Calendar}        from '@fullcalendar/core';
import dayGridPlugin     from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import bootstrapPlugin   from "@fullcalendar/bootstrap";
import timeGridPlugin    from "@fullcalendar/timegrid";
import resourceTimeGridPlugin from '@fullcalendar/resource-timegrid';

import $ from 'jquery';
import Swal from 'sweetalert2';
import 'jquery-contextmenu';
import 'jquery-datetimepicker';
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
// bootstrap
import 'bootstrap'


function prepareEventForm()
{
    console.log('prepareEventForm');
    let $test = $('#klo_ki_event_beginAt').datetimepicker({
        inline: false,
        step: 30,
        format: 'Y-m-d H:i',
        onShow: function( ct ) {
            this.setOptions({
                maxDate: $('#klo_ki_event_endAt').val() ? $('#klo_ki_event_endAt').val() : false
            })
        },
        onSelectDate: function(ct) {
            alert(ct);
        }
    });
    console.dir($test);

    $('#klo_ki_event_endAt').datetimepicker({
        inline: false,
        step: 30,
        format: 'Y-m-d H:i',
        onShow: function( ct ) {
            this.setOptions({
                minDate:   $('#klo_ki_event_beginAt').val() ? $('#klo_ki_event_beginAt').val() : false,
                startDate: $('#klo_ki_event_beginAt').val() ? $('#klo_ki_event_beginAt').val() : false
            })
        }

    });
    $('#klo_ki_event_takeNewAddress').on(
        'change',
        () => {
            let checked = $('#klo_ki_event_takeNewAddress').is(':checked');
            if(checked)
            {
                $('.js-take-existing-address').hide();
                $('.js-take-new-address').show();
            }
            else
            {
                $('.js-take-existing-address').show();
                $('.js-take-new-address').hide();
            }
        });

    $('#klo_ki_event_takeNewAddress').trigger('change');

    ClassicEditor
        .create( document.querySelector( '#klo_ki_event_bemerkung, #klo_ki_event_edit_bemerkung' ) )
        .catch( error => {
            //console.error( error );
        } );
}


function reTargetLinks()
{
    $calendarDetail.find('a:not(.js-no-retarget)').bind('click', function() {
        const url = $(this).attr('href');
        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                $calendarDetail.html(data);
                reTargetLinks();
            }
        });
        return false;
    });
    console.log($calendarDetail.find('form').attr('action'));
    $calendarDetail.find('form').on('submit', function(e){
        e.preventDefault();
        let $form = $(e.currentTarget);
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function(data){
                $calendarDetail.html(data);
                reTargetLinks();
                fullcalendar.refetchEvents();
            }
        });
    });
}

function setSpinner()
{
    $calendarDetail.html('<div class="d-flex justify-content-center">\n' +
        '  <div class="spinner-border" role="status">\n' +
        '    <span class="sr-only">Loading...</span>\n' +
        '  </div>\n' +
        '</div>');
}

function setErrorNotAllowed()
{
    $calendarDetail.html('<div class="alert alert-success" role="alert">\n' +
        '  <h4 class="alert-heading">Zugriff verboten!</h4>\n' +
        '  <p>Der Zugriff auf diese Funktion ist Ihnen nicht erlaubt!</p>\n' +
        '  <hr>\n' +
        '  <p class="mb-0">Tut mir leid...</p>\n' +
        '</div>');
}

function loadNewEventForm(info)
{
    setSpinner();
    $.ajax({
        url: '/event/new',
        method: 'GET',
        success: function(data) {
            $calendarDetail.html(data);
            prepareEventForm();
            $('#klo_ki_event_beginAt').val(info.dateStr + (info.allDay? ' 09:00' : ''));
            $('form[name="klo_ki_event"]').on('submit', handleNewFormSubmit);
        },
        error: function(data) {
            if (data.status === 403)
            {
                setErrorNotAllowed();
            }
        }
    });
}

function loadEditEventForm(event_id)
{
    console.log('loadEditEventForm');
    setSpinner();
    let url = '/event/'+event_id+'/edit';
    $.ajax({
        url: url,
        method: 'GET',
        success: function(data) {
            $calendarDetail.html(data);
            prepareEventForm();
            $('form[name="klo_ki_event"]').attr('action', url);
            console.dir($('form[name="klo_ki_event"]').on('submit', handleEditFormSubmit));
        },
        error: function(data) {
            console.dir(data);
        }
    });
    //$calendarDetailHeader.html('Event bearbeiten');
}

function confirmDelete(event_id)
{
    let url = '/event/'+event_id+'/delete';
    Swal.fire({
        title: 'Dieses Event wirklich löschen?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ja'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: url,
                method: 'DELETE',
                error: function(data) {
                    console.dir(data);
                    Swal.fire('Das war nix!');
                    },
                success: function(data) {
                    fullcalendar.refetchEvents();
                    $calendarDetail.html(data);
                },
            });
        }
    });
}



function loadShowEvent(event_id)
{
    setSpinner();
    console.log('loadShowEvent');
    let url = '/event/show/' + event_id;
    $.ajax({
        url: url,
        method: 'GET',
        success: function(data) {
            $calendarDetail.html(data);
            reTargetLinks();
        }
    });
}


function handleNewFormSubmit(e) {
    e.preventDefault();
    let $form = $(e.currentTarget);
    $.ajax({
        url: '/event/new',
        method: 'POST',
        data: $form.serialize(),
        success: function(data){
            $calendarDetail.html(data);
            prepareEventForm();
            reTargetLinks();
            $('form[name="klo_ki_event"]').on('submit', handleNewFormSubmit);
            fullcalendar.refetchEvents();
        },
        error: function(data){
            // Ein Validierungsfehler führt NICHT zu einem error...
        }
    });
}

function handleEditFormSubmit(e) {
    e.preventDefault();
    let $form = $(e.currentTarget);
    $.ajax({
        url: $form.attr('action'),
        method: 'POST',
        data: $form.serialize(),
        success: function(data){
            $calendarDetail.html(data);
            reTargetLinks();
            $('form[name="klo_ki_event"]').on('submit', handleEditFormSubmit);
            fullcalendar.refetchEvents();
        }
    });
}

/**
 * Wird aufgerufen, wenn ein Event verschoben wurde
 * @param e
 */
function eventDropped(e)
{
    let formData = {};

    console.dir(e);
    formData['id'] = e.event._def.publicId;
    formData['startdate'] = e.event.start;
    //formData['resourceId'] =
    let endTime = null;
    if(!e.event.allDay)
    {
        if (e.event.end === null)
        {
            endTime = e.event.start;
            endTime.setHours(e.event.start.getHours() + 1)
        }
        else
        {
            endTime = e.event.end;
        }
    }
    formData['enddate'] = endTime;


    console.dir(formData);
    $.ajax({
        url: '/event/replace',
        method: 'POST',
        data: JSON.stringify(formData),
        success: function(data){
            console.log('Event Drop AJAX Call: success!');
            loadShowEvent(formData['id']);
        }
    });
}

/**
 * Wird aufgerufen, wenn ein Event in der Länge verändert wurde.
 * @param e
 */
function eventResized(e)
{
    console.log("eventResized");
    console.dir(e);
    let formData = {};
    formData['id'] = e.event._def.publicId;
    formData['enddate'] = e.event.end;
    console.dir(formData);
    $.ajax({
        url: '/event/resize',
        method: 'POST',
        data: JSON.stringify(formData),
        success: function(data){
            console.log('Event Resize AJAX Call: success!');
            loadShowEvent(formData['id']);
        }
    });
}



function eventRenderFunction(info)
{

    if(userRoles.includes('ROLE_FOOD') || userRoles.includes('ROLE_ADMIN') || userRoles.includes('ROLE_LANDLORD') ) {
        if (info.event._def.extendedProps.isFixed) // Wenn das Event fest ist, bekommt es ein Schloss-Symbol
        {
            $(info.el).find('div.fc-content').append(' <i class="fas fa-lock"></i>');
        }
        if (info.event._def.extendedProps.isLichtBenoetigt) // Wenn das Event Licht benötigt, bekommt es ein Glühbirnen-Symbol
        {
            $(info.el).find('div.fc-content').append(' <i class="fas fa-lightbulb"></i>');
        }
        if (info.event._def.extendedProps.isTonBenoetigt) // Wenn das Event Ton benötigt, bekommt es ein Noten-Symbol
        {
            $(info.el).find('div.fc-content').append(' <i class="fas fa-music"></i>');
        }
        if (info.event._def.extendedProps.isBestBenoetigt) // Wenn das Event Ton benötigt, bekommt es ein Noten-Symbol
        {
            $(info.el).find('div.fc-content').append(' <i class="fas fa-chair"></i>');
        }
    }
    if(userRoles.includes('ROLE_HELPER'))
    {
        if(!info.event._def.extendedProps.helperRequired)
        {
            return false;
        }

        if(isAssignedHelper(info.event)) // check if user is assigned to job
        {
            $(info.el).find('div.fc-content').append(' <i class="fas fa-user-check"></i>');
        }
        else if(isInAvailableHelpers(info.event))
        {
            $(info.el).find('div.fc-content').append(' <i class="fas fa-user"></i>');
        }
    }


    $(info.el).attr('data-event-id', info.event.id);
    $(info.el).addClass('js-kloki-event');
}

function isAssignedHelper(event)
{
    console.dir(event);
    return (
        (event._def.extendedProps.helperEinlassEins    && event._def.extendedProps.helperEinlassEins.id  === userId) ||
        (event._def.extendedProps.helperEinlassZwei    && event._def.extendedProps.helperEinlassZwei.id  === userId) ||
        (event._def.extendedProps.helperKasse          && event._def.extendedProps.helperKasse.id        === userId) ||
        (event._def.extendedProps.helperSpringerEins   && event._def.extendedProps.helperSpringerEins.id === userId) ||
        (event._def.extendedProps.helperSpringerZwei   && event._def.extendedProps.helperSpringerZwei.id === userId)
    );
}

function isInAvailableHelpers(event)
{
    let isInHelpers = false;
    for(let helper of event._def.extendedProps.availableHelpers)
    {
        if(helper.id === userId) isInHelpers = true;
    }
    return isInHelpers;
}


/**
 * Hauptprogramm
 */

let calendarEl = document.getElementById('calendar');
let $calendarDetail = $('#js-calendar-detail');
let $calendarDetailHeader = $('#js-calendar-detail-header');
$.datetimepicker.setLocale('de');

let userIsAuthenticated = $('body').data('isAuthenticated');
let userId              = $('body').data('userId');
let userRoles           = $('body').data('userRoles');
console.log("Angemeldet: " + userIsAuthenticated + ', User-ID: ' + userId, ', Rollen: ' + userRoles.join(', '));



const fullcalendar = new Calendar(
    calendarEl,
    {
        plugins: [ dayGridPlugin, interactionPlugin, bootstrapPlugin, timeGridPlugin, resourceTimeGridPlugin],
        height: 'parent',
        editable: true,
        locale: 'de',
        timeZone: 'UTC',
        eventRender: eventRenderFunction,
        eventOverlap: false,
//        eventOverlap: function(stillEvent, movingEvent) {
//            console.dir(stillEvent.extendedProps());
//            console.dir(movingEvent.extendedProps());
//            return (stillEvent._def.extendedProps.resourceIds[0] !== movingEvent._def.extendedProps.resourceIds[0]);
//        },
        resources: '/room/getResources',
        dateClick: loadNewEventForm,
        weekNumbers: true,
        eventResize: eventResized,
        eventDrop: eventDropped,
        eventDragStop: function(info){console.log('DragStop')},
        eventResizeStop: function(info){console.log('ResizeStop')},
        eventClick: function(info){loadShowEvent(info.event._def.publicId);},
        //eventMouseEnter: showEventDetail,
        header: {left: 'resourceTimeGridDay timeGridWeek dayGridMonth', center: 'title'},
        buttonText: {
            today:    'heute',
            month:    'Monat',
            week:     'Woche',
            day:      'Tag',
            list:     'Liste'
        },
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        eventSources: [
            {
                url: "/event/eventRange",
                method: "GET",
                eventDataTransform: function(eventData){
                    eventData.title = eventData.name;
                    eventData.start = eventData.beginAt;
                    eventData.end = eventData.endAt;
                    //eventData.color = eventData.room.color;
                    eventData.allDay = (! eventData.endAt);
                    return eventData;
                },
                extraParams: {
                    filters: JSON.stringify({})
                },
                failure: () => {
                    alert("There was an error while fetching FullCalendar!");
                },
            }

        ],
    });
fullcalendar.render();

window.fullcalendar = fullcalendar;
window.$ = $;


console.dir(fullcalendar);

$(function() {
    $.contextMenu({
        selector: '.js-kloki-event',
        callback: function(key, options) {
            let event_id = $(this).data('event-id');
            switch(key){
                case 'edit': loadEditEventForm(event_id); break;
                case 'delete': confirmDelete(event_id); break;
            }
        },
        items: {
            "edit": {name: "Edit", icon: "edit"},
            "delete": {name: "Delete", icon: "delete"},
            //copy: {name: "Copy", icon: "copy"},
            //"paste": {name: "Paste", icon: "paste"},
        }
    });
});




