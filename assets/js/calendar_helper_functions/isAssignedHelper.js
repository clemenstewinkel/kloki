import {userId} from "../calendar";

export default function isAssignedHelper(event)
{
    console.dir(event);
    return (
        (event._def.extendedProps.helperEinlassEins    && event._def.extendedProps.helperEinlassEins.id  === userId) ||
        (event._def.extendedProps.helperEinlassZwei    && event._def.extendedProps.helperEinlassZwei.id  === userId) ||
        (event._def.extendedProps.helperKasse          && event._def.extendedProps.helperKasse.id        === userId) ||
        (event._def.extendedProps.helperSpringerEins   && event._def.extendedProps.helperSpringerEins.id === userId) ||
        (event._def.extendedProps.helperSpringerZwei   && event._def.extendedProps.helperSpringerZwei.id === userId)
    );
}
