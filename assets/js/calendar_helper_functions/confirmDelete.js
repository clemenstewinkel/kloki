import Swal from "sweetalert2";
import $ from "jquery";
import {$calendarDetail} from "../calendar";
import setErrorNotAllowed from "./setErrorNotAllowed";
import setErrorInternalServer from "./setErrorInternalServer";

export default function confirmDelete(event_id)
{
    let url = '/event/'+event_id+'/delete';
    Swal.fire({
        title: 'Dieses Event wirklich löschen?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ja'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: url,
                method: 'DELETE',
                error: function(data) {
                    if (data.status === 403)
                    {
                        setErrorNotAllowed();
                    }
                    else if(data.status / 100 === 5)
                    {
                        setErrorInternalServer();
                    }
                },
                success: function(data) {
                    fullcalendar.refetchEvents();
                    $calendarDetail.html(data);
                },
            });
        }
    });
}
