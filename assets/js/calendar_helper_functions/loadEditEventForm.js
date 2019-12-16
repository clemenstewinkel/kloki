import setSpinner from "./setSpinner";
import $ from "jquery";
import prepare_event_form from "../prepare_event_form";
import handleEditFormSubmit from "./handleEditEventForm";
import {$calendarDetail} from "../calendar";

export default function loadEditEventForm(event_id)
{
    console.log('loadEditEventForm');
    setSpinner();
    let url = '/event/'+event_id+'/edit';
    $.ajax({
        url: url,
        method: 'GET',
        success: function(data) {
            $calendarDetail.html(data);
            prepare_event_form();
            const $eventForm = $('form[name="klo_ki_event"]');
            $eventForm.attr('action', url);
            $eventForm.on('submit', handleEditFormSubmit);
        },
        error: function(data) {
            console.dir(data);
        }
    });
}
