import $ from 'jquery';
import prepare_address_autocomplete from "./helper_functions/prepare_address_autocomplete";

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
                    $('input.js-address-autocomplete').val(data.substr(3));
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

$(document).ready(function() {
    prepare_address_autocomplete();
    $('#js-new-address-button').on('click', function(){
        const $modalBody = $('div.modal-body');
        $('h4.modal-title').html('Neue Adresse');

        $.ajax({
            url: '../../addresse/new',
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
    });
});
