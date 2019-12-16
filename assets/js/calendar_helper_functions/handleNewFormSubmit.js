import $ from "jquery";
import reTargetLinks from "./reTargetLinks";
import {$calendarDetail} from "../calendar";

export default function handleNewFormSubmit(e) {
    e.preventDefault();
    let $form = $(e.currentTarget);
    $.ajax({
        url: '/event/new',
        method: 'POST',
        data: $form.serialize(),
        success: function(data){
            $calendarDetail.html(data);
            reTargetLinks();
            $('form[name="klo_ki_event"]').on('submit', handleNewFormSubmit);
            fullcalendar.refetchEvents();
        },
        error: function(data){
            // Ein Validierungsfehler führt NICHT zu einem error...
        }
    });
}
