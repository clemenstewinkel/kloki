import {userId} from "../calendar";

export default function isInAvailableHelpers(event)
{
    let isInHelpers = false;
    if(event._def.extendedProps['availableHelpers'].length)
    {
        for(let helper of event._def.extendedProps['availableHelpers'])
        {
            if(helper.id === userId)
            {
                isInHelpers = true;
                break;
            }
        }
    }
    return isInHelpers;
}
