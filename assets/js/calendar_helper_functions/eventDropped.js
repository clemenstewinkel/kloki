import $ from 'jquery';
import loadShowEvent from "./loadShowEvent";

/**
 * Wird aufgerufen, wenn ein Event verschoben wurde
 * @param e
 */
export default function eventDropped(e)
{
    let formData = {};

    console.dir(e);
    formData['id'] = e.event._def.publicId;
    formData['startdate'] = e.event.start;
    //formData['resourceId'] =
    let endTime = null;
    if(!e.event.allDay)
    {
        if (e.event.end === null)
        {
            endTime = e.event.start;
            endTime.setHours(e.event.start.getHours() + 1)
        }
        else
        {
            endTime = e.event.end;
        }
    }
    formData['enddate'] = endTime;


    console.dir(formData);
    $.ajax({
        url: '/event/replace',
        method: 'POST',
        data: JSON.stringify(formData),
        success: function(){
            console.log('Event Drop AJAX Call: success!');
            loadShowEvent(formData['id']);
        }
    });
}
