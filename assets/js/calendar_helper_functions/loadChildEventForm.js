import $ from "jquery";
import setSpinner from "./setSpinner";
import setErrorNotAllowed from "./setErrorNotAllowed";
import {$calendarDetail} from "../calendar";
import reTargetLinks from "./reTargetLinks";

export default function loadChildEventForm(parentId)
{
    setSpinner();
    $.ajax({
        url: 'new?parentIdForNewChild='+parentId,
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
        }
    });
}
