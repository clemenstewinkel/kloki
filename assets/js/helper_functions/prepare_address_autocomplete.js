import $ from "jquery";
import 'autocomplete.js/dist/autocomplete.jquery';
import '../../css/algolia-autocomplete.css';

export default function prepare_address_autocomplete()
{
    $('.js-address-autocomplete').each(function() {
        const autoCompleteUrl = $(this).data('autocomplete-url');
        $(this).autocomplete({hint: false}, [
            {
                source: function(query, cb) {
                    $.ajax({
                        url: autoCompleteUrl+'?query='+query
                    }).then(function(data){
                        cb(data.addresses);
                    })

                },
                displayKey: 'forAutoComplete',
                debounce: 500
            }
        ]);
    });
}
