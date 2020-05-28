import {userRoles} from "../calendar";
import isEventCreatedByFoodRole from "./isEventCreatedByFoodRole";

export default function eventDataTransform(eventData)
{
    eventData.title = eventData.name;
    switch(eventData.art)
    {
        case 'show':
            eventData.backgroundColor = '#84cfff';
            eventData.borderColor = '#84cfff';
            break;
        case 'fair':
            eventData.backgroundColor = '#ceffde';
            eventData.borderColor = '#ceffde';
            break;
        case 'rental':
            eventData.backgroundColor = '#f7bd52';
            eventData.borderColor = '#f7bd52';
            break;
    }
    eventData.editable = (
        (userRoles.includes('ROLE_ADMIN') && (!eventData.isFixed)) ||
        (userRoles.includes('ROLE_FOOD') && (!eventData.isFixed) && isEventCreatedByFoodRole(eventData))
    );
    eventData.resourceEditable = eventData.editable;
    return eventData;
}

