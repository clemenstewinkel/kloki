import $ from "jquery";

export default function modal_address_dialog()
{
    const $modalBody = $('div.modal-body');
    $('h4.modal-title').html('Neue Adresse');

    $.ajax({
        url: '../addresse/new',
        method: 'GET',
        success: function(data) {
            $modalBody.html(data);
            retargetModal();
        },
        error: function(data) {
            if (data.status === 403)
            {
                $modalBody.html("Funktion nicht erlaubt!");
            }
        }
    });
    $('#klokiModal').modal();
}

function retargetModal()
{
    const $modalBody = $('div.modal-body');
    $modalBody.find('form').on('submit', function(e){
        e.preventDefault();
        let $form = $(e.currentTarget);
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function(data){
                if(data.substr(0,3) === 'OK;')
                {
                    $('#klo_ki_event_kontakt').val(data.substr(3));
                    $('#user_address').val(data.substr(3));
                    $('#klokiModal').modal('hide');
                }
                else
                {
                    $modalBody.html(data);
                    retargetModal();
                }
            }
        });
    });
}