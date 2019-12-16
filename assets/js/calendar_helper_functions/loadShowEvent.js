import setSpinner from "./setSpinner";
import $ from "jquery";
import reTargetLinks from "./reTargetLinks";
import {$calendarDetail} from "../calendar";

export default function loadShowEvent(event_id)
{
    setSpinner();
    console.log('loadShowEvent');
    let url = '/event/show/' + event_id;
    $.ajax({
        url: url,
        method: 'GET',
        success: function(data) {
            $calendarDetail.html(data);
            reTargetLinks();
        }
    });
}