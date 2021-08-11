import setSpinner from "./setSpinner";
import $ from "jquery";
import reTargetLinks from "./reTargetLinks";
import {$calendarDetail, userRoles} from "../calendar";

export default function loadShowEvent(event_id)
{
    let url='';
    if (userRoles.includes('ROLE_ADMIN') || userRoles.includes('ROLE_FOOD') || userRoles.includes('ROLE_TECH'))
    {
        url = 'show/' + event_id;
    }
    else if (userRoles.includes('ROLE_HELPER'))
    {
        url = 'showHelper/' + event_id;
    }

    setSpinner();
    console.log('loadShowEvent');
    $.ajax({
        url: url,
        method: 'GET',
        success: function(data) {
            $calendarDetail.html(data);
            reTargetLinks();
        }
    });
}
