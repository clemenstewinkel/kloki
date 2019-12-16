import $ from 'jquery';
import loadShowEvent from "./loadShowEvent";
/**
 * Wird aufgerufen, wenn ein Event in der Länge verändert wurde.
 * @param e
 */
export default function eventResized(e)
{
    console.log("eventResized");
    console.dir(e);
    let formData = {};
    formData['id'] = e.event._def.publicId;
    formData['enddate'] = e.event.end;
    console.dir(formData);
    $.ajax({
        url: '/event/resize',
        method: 'POST',
        data: JSON.stringify(formData),
        success: function(data){
            console.log('Event Resize AJAX Call: success!');
            loadShowEvent(formData['id']);
        }
    });
}
