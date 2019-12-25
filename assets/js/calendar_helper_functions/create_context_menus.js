import $ from "jquery";
import 'jquery-contextmenu';
import loadEditEventForm from "./loadEditEventForm";
import confirmDelete from "./confirmDelete";
import loadNewEventForm from "./loadNewEventForm";
import {userRoles} from "../calendar";

export default function create_context_menus()
{
    if(userRoles.includes('ROLE_ADMIN'))
    {
        $.contextMenu({
            selector: '.js-kloki-event',
            callback: function(key) {
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
    }


//    $.contextMenu({
//        selector: '.fc-day',
//        callback: function(key, options) {
//            switch(key){
//                case 'create': alert("Hallo"); break;
//            }
//        },
//        items: {
//            "create": {name: "Create", icon: "edit"},
//        }
//    });
}
