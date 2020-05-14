import $ from 'jquery';
import Swal from "sweetalert2";
export default function attachMailFunctions()
{
    $('#js-mail2helper').on('click', function(e) {
        Swal.fire({
            title: 'Alle eingeteilten Helfer über dieses Event per E-Mail informieren?',
            input: 'textarea',
            inputPlaceholder: 'Zusätzliche Nachricht...',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ja'
        }).then((result) => {
            console.dir(result);
            if ('value' in result) {
                $.ajax({
                    url: '/event/' + e.currentTarget.dataset.eventId +'/sendHelperMail',
                    method: 'POST',
                    data: {message: result.value},
                    error: function(data) {
                        alert("Da ist etwas schief gegangen! Es wurde wahrscheinlich keine E-Mail versendet!");
                        console.dir(data);
                    },
                    success: function(data) {
                        console.dir(data);
                        Swal.fire({title: 'OK', text: 'Die Mail wurde an ' + data.sent + ' Empfänger versendet.', icon: 'success', timer: 3000});
                    },
                });
            }
       });
    });

    $('#js-mail2techs').on('click', function(e) {
        Swal.fire({
            title: 'Alle eingeteilten Techniker über dieses Event per E-Mail informieren?',
            input: 'textarea',
            inputPlaceholder: 'Zusätzliche Nachricht...',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ja'
        }).then((result) => {
            console.dir(result);
            if ('value' in result) {
                $.ajax({
                    url: '/event/' + e.currentTarget.dataset.eventId +'/sendTechMail',
                    method: 'POST',
                    data: {message: result.value},
                    error: function(data) {
                        alert("Da ist etwas schief gegangen! Es wurde wahrscheinlich keine E-Mail versendet!");
                        console.dir(data);
                    },
                    success: function(data) {
                        console.dir(data);
                        Swal.fire({title: 'OK', text: 'Die Mail wurde an ' + data.sent + ' Empfänger versendet.', icon: 'success', timer: 3000});
                    },
                });
            }
        });
    });

}