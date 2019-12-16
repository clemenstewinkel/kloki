import {userId} from "../calendar";

export default function isAssignedHelper(event)
{
    return (
        ((event._def.extendedProps.helperEinlassEins  !== null )  && event._def.extendedProps.helperEinlassEins.id  === userId) ||
        ((event._def.extendedProps.helperEinlassZwei  !== null )  && event._def.extendedProps.helperEinlassZwei.id  === userId) ||
        ((event._def.extendedProps.helperKasse        !== null )  && event._def.extendedProps.helperKasse.id        === userId) ||
        ((event._def.extendedProps.helperSpringerEins !== null )  && event._def.extendedProps.helperSpringerEins.id === userId) ||
        ((event._def.extendedProps.helperSpringerZwei !== null )  && event._def.extendedProps.helperSpringerZwei.id === userId)
    )
}
