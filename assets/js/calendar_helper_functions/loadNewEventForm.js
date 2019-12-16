import $ from "jquery";
import setSpinner from "./setSpinner";
import prepare_event_form from "../prepare_event_form";
import setErrorNotAllowed from "./setErrorNotAllowed";
import handleNewFormSubmit from "./handleNewFormSubmit";
import {$calendarDetail} from "../calendar";

export default function loadNewEventForm(info)
{
    setSpinner();
    console.dir(info);
    console.log(info.dateStr.substring(0,10));
    console.log(info.dateStr.substring(11,16));
    $.ajax({
        url: '/event/new',
        method: 'GET',
        success: function(data) {
            $calendarDetail.html(data);
            prepare_event_form();
            $('#klo_ki_event_startDate').val(info.dateStr.substring(0,10));
            $('#klo_ki_event_endDate').val(info.dateStr.substring(0,10));
            if(info.dateStr.substring(11,16))
            {
                $('#klo_ki_event_startTime').val(info.dateStr.substring(11,16));
            }
            else
            {
                $('#klo_ki_event_startTime').val('10:00');
                $('#klo_ki_event_endTime').val('23:30');
            }

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
