import {userId} from "../calendar";

export default function isInAvailableHelpers(event)
{
    let isInHelpers = false;
    for(let helper of event._def.extendedProps.availableHelpers)
    {
        if(helper.id === userId) isInHelpers = true;
    }
    return isInHelpers;
}
