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

     // Localiza el script para proporcionar la URL de admin-ajax.php y el nonce de seguridad
     wp_localize_script('fullcalendar-js', 'MyAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'ajax_nonce' => wp_create_nonce('add_reservation_to_cart_nonce')
    ));
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
            <input type="hidden" name="product_id" id="product_id" value="16">
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
        var from;
        var to;

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
                        from = selectedStartDate.format("YYYY-MM-DD");
                        to = start.format("YYYY-MM-DD");
                        $("#dialog-form #from").val(from); 
                        $("#dialog-form #to").val(to); 
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
                    // Ahora puedes usar `from` y `to` aquí
                    console.log(from);
                    console.log(to);
                    var product_id = $("#product_id").val();
    
                    // Envía una solicitud AJAX para agregar el producto al carrito y crear la reserva
                    $.post(
                        MyAjax.ajaxurl, // URL del archivo admin-ajax.php de WordPress
                        {
                            action: "add_reservation_to_cart",
                            from: from,
                            to: to,
                            product_id: product_id,
                            security: MyAjax.ajax_nonce
                        },
                        function(response) {
                            if (response.success) {
                                // Redirige al usuario a la página de pago de WooCommerce
                                window.location.href = response.data.checkout_url;
                            } else {
                                // Muestra un mensaje de error si algo falla
                                alert("Error: " + response.data.error_message);
                            }
                        },
                        "json"
                    );
    
                    $( this ).dialog( "close" );
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    });
    </script>';

}
add_action('wp_footer', 'add_calendar_script');



function add_reservation_to_cart() {
    check_ajax_referer('add_reservation_to_cart_nonce', 'security');

    $from = sanitize_text_field($_POST['from']);
    $to = sanitize_text_field($_POST['to']);
    $product_id = intval($_POST['product_id']);

    $start_date = new DateTime($from);
    $end_date = new DateTime($to);
    $interval = date_diff($start_date, $end_date);
    $days = $interval->days + 1;

    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error(array('error_message' => 'El producto de reserva no existe.'));
        return;
    }
    if ('' === $product->get_price()) {
        wp_send_json_error(array('error_message' => 'El producto de reserva no tiene precio.'));
        return;
    }

    global $woocommerce;
    // Vacía el carrito antes de agregar el nuevo producto
    $woocommerce->cart->empty_cart();

    $added = $woocommerce->cart->add_to_cart($product_id, $days, 0, [], ['from_date' => $from, 'to_date' => $to]);
    if (!$added) {
        wp_send_json_error(array('error_message' => 'No se pudo agregar el producto de reserva al carrito.'));
        return;
    }

    wp_send_json_success(array(
        'checkout_url' => wc_get_checkout_url(),
    ));
}




add_action('wp_ajax_add_reservation_to_cart', 'add_reservation_to_cart');
add_action('wp_ajax_nopriv_add_reservation_to_cart', 'add_reservation_to_cart');

function save_reservation($order_id) {
    // Obtén el pedido
    $order = wc_get_order($order_id);

    // Para cada artículo en el pedido...
    foreach ($order->get_items() as $item_id => $item) {
        // Si el producto del artículo es tu producto de reserva...
        if ($item->get_product_id() == 16) {
            // Obtén las fechas de reserva desde las metadatos del artículo
            $from_date = $item->get_meta('from_date');
            $to_date = $item->get_meta('to_date');

            // Ahora puedes guardar la reserva utilizando los detalles del pedido
            // y las fechas de reserva. La implementación exacta dependerá de cómo
            // quieras almacenar las reservas. Por ejemplo, podrías querer guardarlas
            // en una tabla personalizada en la base de datos, o como un tipo de
            // publicación personalizada.

            // Aquí un ejemplo de cómo podrías guardar la reserva como una publicación personalizada:
            $reservation_id = wp_insert_post(array(
                'post_type' => 'reservation',
                'post_title' => sprintf('Reserva #%s', $order_id),
                'post_status' => 'publish',
                'meta_input' => array(
                    'order_id' => $order_id,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                ),
            ));
        }
    }
}

add_action('woocommerce_payment_complete', 'save_reservation');
function display_dates_in_cart($item_data, $cart_item) {
    if(isset($cart_item['from_date']) && isset($cart_item['to_date'])) {
        $item_data[] = array(
            'key' => 'From',
            'value' => $cart_item['from_date']
        );
        $item_data[] = array(
            'key' => 'To',
            'value' => $cart_item['to_date']
        );
    }
    return $item_data;
}
add_filter('woocommerce_get_item_data', 'display_dates_in_cart', 10, 2);
?>

