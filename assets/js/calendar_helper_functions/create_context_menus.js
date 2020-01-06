import $ from "jquery";
import 'jquery-contextmenu';
import loadEditEventForm from "./loadEditEventForm";
import confirmDelete from "./confirmDelete";
import {userRoles} from "../calendar";
import loadChildEventForm from "./loadChildEventForm";



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
                    case 'vertrag': window.open('createWord/' + event_id); break;
                    case 'child': console.dir(fullcalendar.getEventById(event_id).extendedProps.ParentEvent); loadChildEventForm(event_id); break;
                }
            },
            items: {
                edit: {name: "Edit", icon: "edit"},
                delete: {name: "Delete", icon: "delete"},
                vertrag: {name: "Mietvertrag", icon: "edit"},
                child: {
                    name: "zus. Event zu diesem Event anlegen",
                    icon: "edit",
                    visible: function(key, opt){
                        let event_id = $(this).data('event-id');
                        // Hide this item if the menu was triggered on a div
                        return (fullcalendar.getEventById(event_id).extendedProps.ParentEvent === null);
                    }
                }
            }
        });
    }


    $.contextMenu({
        selector: '.fc-day',
        callback: function(key, options) {
            switch(key){
                case 'dispo': window.open('dispo?dispoForDay=' + $(this).data('date'), '_blank'); break;
            }
        },
        items: {
            "dispo": {name: "Tages-Dispo", icon: "edit"},
        }
    });
}
