=== Calendario de Reservas ===
Contributors: José Daniel Man Castellanos
Tags: calendar, booking, WooCommerce
Requires at least: 4.0
Tested up to: 5.8
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html



Documentación del Plugin Calendario para WooCommerce
Este plugin de WordPress, llamado "Calendario Plugin", permite a los usuarios de una tienda WooCommerce seleccionar fechas en un calendario para la reserva de productos. Esta guía paso a paso te mostrará cómo usarlo y qué componentes necesitas tener instalados para su correcto funcionamiento.

Requisitos
WordPress: El plugin está diseñado para trabajar con WordPress, un sistema de gestión de contenido (CMS) popular.

WooCommerce: Este plugin requiere que el plugin WooCommerce esté instalado y activado en tu sitio de WordPress. WooCommerce es una plataforma de comercio electrónico gratuita que permite vender productos y servicios desde tu sitio de WordPress.

Servidor PHP: Dado que este plugin está escrito en PHP, necesitarás un servidor que pueda interpretar y ejecutar código PHP.

Navegador moderno: El plugin utiliza JavaScript y CSS que pueden no ser compatibles con navegadores muy antiguos.

Instalación
Para instalar el plugin, sigue estos pasos:

Copia el código del plugin en un archivo con extensión .php, por ejemplo, calendario-plugin.php.

Sube este archivo a la carpeta wp-content/plugins de tu instalación de WordPress. Puedes hacerlo a través del panel de control de tu proveedor de hosting o mediante un cliente FTP.

Ve al panel de administración de WordPress y navega hasta la sección de plugins (Plugins -> Installed Plugins).

Localiza "Calendario Plugin" en la lista de plugins y haz clic en "Activate".

Si WooCommerce no está activado, verás un mensaje de alerta. En ese caso, asegúrate de instalar y activar WooCommerce antes de seguir.

Uso
Una vez activado el plugin, puedes insertar el calendario en cualquier página o publicación utilizando el siguiente shortcode:

[show_calendar]

Este shortcode muestra un calendario interactivo que permite a los usuarios seleccionar una fecha de inicio y una fecha de fin para la reserva de un producto. Los usuarios pueden seleccionar las fechas y luego proceder a realizar la reserva.

Cuando los usuarios seleccionan las fechas y hacen clic en "Realizar reserva", el producto se agrega automáticamente a su carrito de WooCommerce con las fechas seleccionadas. Luego son redirigidos a la página de pago de WooCommerce para completar su compra.

Funcionamiento Interno
El plugin consta de varias partes:

Verificación de WooCommerce: El plugin verifica que WooCommerce esté instalado y activado. Si no lo está, muestra una alerta en el pie de página del sitio.

Enqueue de scripts y estilos: El plugin incluye varios scripts y estilos necesarios para su funcionamiento, incluyendo jQuery, FullCalendar, y varios scripts y estilos propios.

Shortcode y clase de calendario: El plugin define un shortcode que muestra el calendario. Cuando se utiliza el shortcode, se crea una instancia de la clase Calendar y se llama al método show() para generar el HTML del calendario.

Inicialización de calendario: El plugin agrega un script al pie de página del sitio que inicializa el calendario y maneja la interacción del usuario con él.

Agregar reserva al carrito: El plugin define una función AJAX que agrega la reserva al carrito de WooCommerce. Esta función se llama cuando el usuario hace clic en "Realizar reserva".