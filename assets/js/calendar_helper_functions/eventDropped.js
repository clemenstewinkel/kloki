import $ from 'jquery';
import loadShowEvent from "./loadShowEvent";
import setErrorInternalServer from "./setErrorInternalServer";
import setErrorNotAllowed from "./setErrorNotAllowed";

/**
 * Wird aufgerufen, wenn ein Event verschoben wurde
 * @param e
 */
export default function eventDropped(e)
{
    let formData = {};

    formData['id'] = e.event._def.publicId;
    formData['startdate'] = e.event.start;
    formData['allDay'] = e.event.allDay;
    formData['enddate'] = e.event.end;
    if(e.newResource !== null) formData['newResource'] = e.newResource.id;

    $.ajax({
        url: 'replace',
        method: 'POST',
        data: JSON.stringify(formData),
        success: function(){
            console.log('Event Drop AJAX Call: success!');
            loadShowEvent(formData['id']);
        },
        error: function(data) {
            e.revert();
            if (data.status === 403)
            {
                setErrorNotAllowed();
            }
            else if(data.status / 100 === 5)
            {
                setErrorInternalServer();
            }
        }
    });
}
