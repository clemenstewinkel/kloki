import $ from "jquery";
import 'jquery-contextmenu';
import loadEditEventForm from "./loadEditEventForm";
import confirmDelete from "./confirmDelete";

export default function create_context_menus()
{
    $.contextMenu({
        selector: '.js-kloki-event',
        callback: function(key, options) {
            let event_id = $(this).data('event-id');
            switch(key){
                case 'edit': loadEditEventForm(event_id); break;
                case 'delete': confirmDelete(event_id); break;
            }
        },
        items: {
            "edit": {name: "Edit", icon: "edit"},
            "delete": {name: "Delete", icon: "delete"},
        }
    });

    $.contextMenu({
        selector: '.fc-day',
        callback: function(key, options) {
            switch(key){
                case 'create': alert("Hallo"); break;
            }
        },
        items: {
            "create": {name: "Create", icon: "edit"},
        }
    });
}
