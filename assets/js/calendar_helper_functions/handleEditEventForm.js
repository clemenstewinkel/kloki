import $ from "jquery";
import reTargetLinks from "./reTargetLinks";
import {$calendarDetail} from "../calendar";

export default function handleEditFormSubmit(e) {
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
