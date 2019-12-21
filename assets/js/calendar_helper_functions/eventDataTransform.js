import {userRoles} from "../calendar";

export default function eventDataTransform(eventData)
{
    eventData.title = eventData.name;
    eventData.editable = userRoles.includes('ROLE_ADMIN') || (userRoles.includes('ROLE_FOOD') && (!eventData.isFixed));
    return eventData;
}