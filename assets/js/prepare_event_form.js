import $ from "jquery";
import 'autocomplete.js/dist/autocomplete.jquery';
import 'jquery-datetimepicker';
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import '../css/algolia-autocomplete.css';
import modal_address_dialog from "./modal_address_dialog";

export default function prepare_event_form()
{
    console.log('DEBUG: prepare_event_form called!');

    if ($('form[name="klo_ki_event"]').length === 0)
    {
        console.log('DEBUG: prepare_event_form: No Form!');
        return;
    }

    const $fullDaySwitch = $('#klo_ki_event_isFullDay');
    const $helperSwitch = $('#klo_ki_event_helperRequired');
    const $endDate   = $('#klo_ki_event_endDate');
    const $startDate = $('#klo_ki_event_startDate');
    const $endTime   = $('#klo_ki_event_endTime');
    const $startTime = $('#klo_ki_event_startTime');

    $startDate.datetimepicker({
        inline: false,
        timepicker: false,
        format: 'Y-m-d',
        onShow: function( ct ) {
            console.dir(ct);
            this.setOptions({
                maxDate: $endDate.val() ? $endDate.val() : false
            })
        },
        onSelectDate: function() {
            if(! $endDate.val())
            {
                console.dir($startDate.val());
                $endDate.val($startDate.val());
            }
        }
    });

    $endDate.datetimepicker({
        inline: false,
        timepicker: false,
        format: 'Y-m-d',
        onShow: function( ct ) {
            console.dir(ct);
            this.setOptions({
                minDate: $startDate.val() ? $startDate.val() : false
            })
        },
        onSelectDate: function() {
            if(! $startDate.val())
            {
                $startDate.val($endDate.val());
            }
        }
    });

    $endTime.datetimepicker({
        inline: false,
        datepicker: false,
        step: 30,
        format: 'H:i',
        onSelectTime: function(ct){
            console.dir(ct);
        }
    });

    $startTime.datetimepicker({
        inline: false,
        datepicker: false,
        step: 30,
        format: 'H:i',
    });

    $fullDaySwitch.on('change', function() {
        $('.js-event-time-field').toggle(!this.checked);
    });
    $helperSwitch.on('change', function() {
        $('#js-event-helper-section').toggle(this.checked);
    });

    $helperSwitch.trigger('change');
    $fullDaySwitch.trigger('change');


    ClassicEditor
        .create( document.querySelector( '#klo_ki_event_bemerkung' ) )
        .catch( error => {
            //console.error( error );
        } );

    $('.js-address-autocomplete').each(function() {
        var autoCompleteUrl = $(this).data('autocomplete-url');
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

    $('#js-new-address-button').on('click', function(){
       modal_address_dialog();
    });
}

