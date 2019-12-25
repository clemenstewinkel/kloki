import {userId} from "../calendar";

export default function isAssignedHelper(event)
{
    return (
        ((event.extendedProps['helperEinlassEins']  !== null )  && event.extendedProps['helperEinlassEins']['id']  === userId) ||
        ((event.extendedProps['helperEinlassZwei']  !== null )  && event.extendedProps['helperEinlassZwei']['id']  === userId) ||
        ((event.extendedProps['helperKasse']        !== null )  && event.extendedProps['helperKasse']['id']        === userId) ||
        ((event.extendedProps['helperSpringerEins'] !== null )  && event.extendedProps['helperSpringerEins']['id'] === userId) ||
        ((event.extendedProps['helperSpringerZwei'] !== null )  && event.extendedProps['helperSpringerZwei']['id'] === userId)
    )
}
