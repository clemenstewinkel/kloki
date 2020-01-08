import {userRoles} from "../calendar";
import isEventCreatedByFoodRole from "./isEventCreatedByFoodRole";

export default function eventDataTransform(eventData)
{
    eventData.title = eventData.name;
    eventData.editable = (
        (userRoles.includes('ROLE_ADMIN') && (!eventData.isFixed)) ||
        (userRoles.includes('ROLE_FOOD') && (!eventData.isFixed) && isEventCreatedByFoodRole(eventData))
    );
    return eventData;
}

