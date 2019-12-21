import $ from "jquery";
import setSpinner from "./setSpinner";
import setErrorNotAllowed from "./setErrorNotAllowed";
import {$calendarDetail} from "../calendar";
import reTargetLinks from "./reTargetLinks";

export default function loadNewEventForm(info)
{
    setSpinner();
    $.ajax({
        url: '/event/new',
        method: 'GET',
        success: function(data) {
            $calendarDetail.html(data);
            $('#klo_ki_event_startDate').val(info.dateStr.substring(0,10));
            $('#klo_ki_event_endDate').val(info.dateStr.substring(0,10));
            if(info.resource) $('#klo_ki_event_room').val(info.resource.id);
            if(info.dateStr.substring(11,16))
            {
                $('#klo_ki_event_startTime').val(info.dateStr.substring(11,16));
            }
            else
            {
                $('#klo_ki_event_startTime').val('10:00');
            }
            $('#klo_ki_event_endTime').val('23:30');
            reTargetLinks();
        },
        error: function(data) {
            if (data.status === 403)
            {
                setErrorNotAllowed();
            }
        }
    });
}
