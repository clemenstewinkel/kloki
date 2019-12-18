import setSpinner from "./setSpinner";
import $ from "jquery";
import {$calendarDetail} from "../calendar";
import reTargetLinks from "./reTargetLinks";

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
            reTargetLinks();
        },
        error: function(data) {
            console.dir(data);
        }
    });
}
