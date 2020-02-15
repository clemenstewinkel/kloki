import $ from "jquery";
import isAssignedHelper from "./isAssignedHelper";
import isInAvailableHelpers from "./isInAvailableHelpers";
import {userRoles} from "../calendar";

export default function eventRenderFunction(info)
{
    let $timeSpan = $(info.el).find('span.fc-time').detach();
    if($timeSpan.length > 0 )
    {
        $(info.el).find('div.fc-content').append('<br/>');
        $(info.el).find('div.fc-content').append($timeSpan);
    }

    if(userRoles.includes('ROLE_FOOD') || userRoles.includes('ROLE_ADMIN') || userRoles.includes('ROLE_LANDLORD') ) {
        if (info.event.extendedProps['isFixed']) // Wenn das Event fest ist, bekommt es ein Schloss-Symbol
        {
            $(info.el).find('div.fc-content').append(' <i class="fas fa-lock"></i>');
        }
        if (info.event.extendedProps['isLichtBenoetigt']) // Wenn das Event Licht benötigt, bekommt es ein Glühbirnen-Symbol
        {
            $(info.el).find('div.fc-content').append(' <i class="fas fa-lightbulb"></i>');
        }
        if (info.event.extendedProps['isTonBenoetigt']) // Wenn das Event Ton benötigt, bekommt es ein Noten-Symbol
        {
            $(info.el).find('div.fc-content').append(' <i class="fas fa-music"></i>');
        }
        if (info.event.extendedProps['isBestBenoetigt']) // Wenn das Event Ton benötigt, bekommt es ein Noten-Symbol
        {
            $(info.el).find('div.fc-content').append(' <i class="fas fa-chair"></i>');
        }
        if(info.event.extendedProps['helperRequired'])
        {
            $(info.el).find('div.fc-content').append(' <i class="fas fa-user"></i>');
        }
        $(info.el).find('div.fc-content').append('<span style="float: right; font-weight: bold;">' + info.event.extendedProps.room.name[0] + '</span>');
    }
    if(userRoles.includes('ROLE_HELPER'))
    {
        if(!info.event.extendedProps['helperRequired'])
        {
            return false;
        }
        if(isAssignedHelper(info.event)) // check if user is assigned to job
        {
            $(info.el).find('div.fc-content').append(' <i class="fas fa-user-check"></i>');
        }
        else if(isInAvailableHelpers(info.event))
        {
            $(info.el).find('div.fc-content').append(' <i class="fas fa-user"></i>');
        }
    }

    $(info.el).attr('data-event-id', info.event.id);
    $(info.el).addClass('js-kloki-event');
}
