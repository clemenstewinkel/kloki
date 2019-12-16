import Swal from "sweetalert2";
import $ from "jquery";
import {$calendarDetail} from "../calendar";

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
                    console.dir(data);
                    Swal.fire('Das war nix!');
                },
                success: function(data) {
                    fullcalendar.refetchEvents();
                    $calendarDetail.html(data);
                },
            });
        }
    });
}
