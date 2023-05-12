<?php
/*
Plugin Name: Calendario Plugin
Description: Este es un plugin de calendario.
Author: Tu nombre
Version: 1.0
*/

// enqueue scripts and styles for FullCalendar
function enqueue_fullcalendar(){
    // enqueue all our scripts
    wp_enqueue_style('fullcalendar-css', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css');
    wp_enqueue_script('moment-js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js', array('jquery'), null, true);
    wp_enqueue_script('fullcalendar-js', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js', array('jquery', 'moment-js'), null, true);
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_style('jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css');
    wp_enqueue_script('jquery-ui-datepicker');
}
add_action('wp_enqueue_scripts', 'enqueue_fullcalendar');

class Calendar {
    public function show() {
        return '<div id="calendar"></div><div id="dialog" title="Reservar" style="display:none;"><p>Fecha de inicio: <input type="text" id="from"></p><p>Fecha de fin: <input type="text" id="to"></p><p>Realizar pago...</p></div>';
    }
}

// Define the shortcode and specify the function to be run when found
function calendar_shortcode() {
    $calendar = new Calendar();
    return $calendar->show();
}
add_shortcode('show_calendar', 'calendar_shortcode');

// Add the calendar initialization script to the footer
function add_calendar_script() {
    echo '
    <div id="dialog-form" title="Reserva">
        <form>
            <fieldset>
                <label for="from">Desde</label>
                <input type="text" name="from" id="from" readonly>
                <label for="to">Hasta</label>
                <input type="text" name="to" id="to" readonly>
            </fieldset>
        </form>
    </div>
    <script>
        jQuery(document).ready(function($) {
            var selectingStartDate = true;
            var selectedStartDate;

            $("#calendar").fullCalendar({
                header: {
                    left: "prev,next today",
                    center: "title",
                    right: "month"
                },
                defaultDate: "'. date('Y-m-d') .'",
                navLinks: false, 
                selectable: true,
                selectHelper: true,
                select: function(start, end) {
                    if (start.isBefore(moment())) {
                        $("#calendar").fullCalendar("unselect");
                        return;
                    }

                    if (selectingStartDate) {
                        selectedStartDate = start;
                        selectingStartDate = false;
                    } else {
                        if (start.isBefore(selectedStartDate)) {
                            alert("La fecha de fin no puede ser anterior a la fecha de inicio.");
                        } else {
                            $("#dialog-form #from").val(selectedStartDate.format("YYYY-MM-DD")); // Cambiado aquí
                            $("#dialog-form #to").val(start.format("YYYY-MM-DD")); // Cambiado aquí
                            $("#dialog-form").dialog("open");
                        }
                        selectingStartDate = true;
                    }
                },
                eventLimit: true, 
            });

            $( "#dialog-form" ).dialog({
                autoOpen: false,
                height: 400,
                width: 350,
                modal: true,
                buttons: {
                    "Realizar reserva": function() {
                        // Aquí puedes agregar la lógica para guardar la reserva
                        $( this ).dialog( "close" );
                    },
                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
                },
                close: function() {
                    // Aquí puedes agregar lógica adicional para cuando se cierre el diálogo
                }
            });
        });
    </script>';

}
add_action('wp_footer', 'add_calendar_script');
?>

