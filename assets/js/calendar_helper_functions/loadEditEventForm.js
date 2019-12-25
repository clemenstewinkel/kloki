import setSpinner from "./setSpinner";
import $ from "jquery";
import {$calendarDetail} from "../calendar";
import reTargetLinks from "./reTargetLinks";
import setErrorNotAllowed from "./setErrorNotAllowed";
import setErrorInternalServer from "./setErrorInternalServer";

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
            if (data.status === 403)
            {
                setErrorNotAllowed();
            }
            else if(data.status / 100 === 5)
            {
                setErrorInternalServer();
            }
        }
    });
}
