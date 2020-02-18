import $ from "jquery";
import setSpinner from "./setSpinner";
import setErrorNotAllowed from "./setErrorNotAllowed";
import {$calendarDetail, userRoles} from "../calendar";
import reTargetLinks from "./reTargetLinks";

export default function loadNewEventForm(info)
{
    let url4new = '';
    if(userRoles.includes('ROLE_ADMIN')) url4new = 'new';
    else if (userRoles.includes('ROLE_FOOD')) url4new = 'newfood'
    else return false;
    setSpinner();
    $.ajax({
        url: url4new,
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
