import $ from "jquery";
import setSpinner from "./setSpinner";
import setErrorNotAllowed from "./setErrorNotAllowed";
import {$calendarDetail, userRoles} from "../calendar";
import reTargetLinks from "./reTargetLinks";

export default function loadChildEventForm(parentId)
{
    setSpinner();
    let url2call = '';
    if (userRoles.includes('ROLE_FOOD')) url2call = 'newfood?parentIdForNewChild='+parentId;
    if (userRoles.includes('ROLE_ADMIN')) url2call = 'new?parentIdForNewChild='+parentId;

    $.ajax({
        url: url2call,
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
