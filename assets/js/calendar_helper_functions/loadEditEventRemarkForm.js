import setSpinner from "./setSpinner";
import $ from "jquery";
import {$calendarDetail} from "../calendar";
import reTargetLinks from "./reTargetLinks";
import setErrorNotAllowed from "./setErrorNotAllowed";
import setErrorInternalServer from "./setErrorInternalServer";

export default function loadEditEventRemarkForm(event_id)
{
    console.log('loadEditEventRemarkForm');
    setSpinner();
    let url = '' + event_id + '/editRemark';
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
