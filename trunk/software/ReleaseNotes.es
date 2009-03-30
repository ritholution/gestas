 Notas de Publicación de Gestas
================================

 Versión 0.0.2 (30/03/2009)
============================
 Esta versión incluye las siguientes mejoras:

 * Kernel
   - Gestión de permisos mejorada: En esta versión hemos implementado
     un nuevo método de comprobación de los permisos de cada usuario,
     reduciendo de forma significativael número de datos en la base de
     datos para la asignación de permisos en las distintas asociaciones
     en las que el usuario es socio.
   - Nuevo estilo: Hemos modificado el nuevo estilo por defectode la
     web por uno más moderno y sofisticado.
   - Nuevos tipos de excepciones y mensajes: Hemos creado dos nuevos
     tipos de excepción, GDatabaseException (para las excepciones de
     la base de datos) y GUserException (para las excepciones de
     usuario), y hemos añadido nuevos mensajes de error, mejorando
     también los que había anteriormente.
   - Modo depuración: Hemos creado un nuevo modo de depuración para
     mostrar o no la pila de ejecución en el mensaje de error. Esta
     pila muestra todas las funciones que se están ejecutando en el
     momento de la excepción, con lo que es muy útil para la
     depuración, pero resulta molesto para el usuario común.
   - Mejoas en la gestión de contraseñas: Actualmente hay un método
     exclusivo para comprobar la validez de una contraseña de usuario
     y hemos optimizado la acción de modificación de la contraseña.

 * Gestor de socios
   - Nuevas comprobaciones Javascript: Hemos desarrollado algunos
     avisos con Javascript en los formularios de todas las acciones
     del módulo, de manera que un usuario no tenga que enviar los
     datos para corregir los fallos que tenga.
   - Resolución de errores: Hemos resuelto diversos errores que había
     en las acciones ya desarrolladas, de manera que actualmente se
     considera la parte desarrollada bastante estable.

 * Gestor de asociaciones
   - Validación de peticiones: Se ha desarrollado una nueva acción
     para validar las peticiones de alta de nuevas asociaciones en la
     herramienta.
   - Modificación de asociación: Se ha desarrollado una nueva acción
     para modificar los datos relacionados con la asociación.
   - Mejoras en la petición de alta: Hemos mejorado la petición de
     alta de asociaciones habilitando a los usuarios registrados la
     posibilidad de dar de alta una nueva asociación sin la necesidad
     de que hayan ingresado en la herramienta. El proceso a seguir es
     mediante la inclusión de su contraseña en el formulario de alta
     de asociación, tal y como hacen los usuarios sin registro.

 Versión 0.0.1 (29/03/2009)
============================
 Esta versión tiene implementadas las siguientes funcionalidades:

 * Núcleo: El núcleo soporta actualmente las siguientes características:
   - Base de datos MySQL: Actualmente el programa únicamente soporta
     bases de datos MySQL, pero en el futuro soportara otros motores
     de bases de datos.
   - Uso de módulos: El núcleo soporta el uso de nuevos módulos para
     que sea más fácil la adaptación del software a las necesidades de
     cualquier asociación.
   - Uso de plantillas: La aplicación utiliza plantillas para generar
     la salida html, reemplazando las variables de la plantilla por su
     valor.
   - Soporte básico de logs: Hemos incluido un soporte básico para
     generar mensajes de log, pero debido a que no está completamente
     implementado no se ha habilitado.
   - Soporte básico de la traducción de idiomas: Hemos incluido un
     soporte básico de traducción a diferentes idiomas utilizando
     gettext, pero debido a que no está completamente
     implementado/probado no se ha activado.
   - Asignación dinámica de permisos: Hemos diseñado una asignación
     dinámica de permisos aplicados en un usuario tanto en una
     asociación en particular como en toda la aplicación.
   - Creación de nuevos tipos de usuario: Permitimos la creación
     dinámica de tipos de usuario, de manera que cada asociación puede
     tener sus propios tipos de usuario con una serie de permisos
     asignados dinámicamente.
   - Gestión básica de usuarios: Hemos desarrollado una gestión básica
     de usuarios, en la que se incluyen el alta de usuarios y el
     cambio de contraseña.

 * Gestor de Socios: Este módulo implementa las siguientes acciones:
   - Petición de alta de socio: Esta acción implementa la petición de
     hacerse socio a una asociación.
   - Listado de peticiones de alta: Esta acción muestra la lista de
     peticiones de nuevos socios a validar.
   - Validación de alta: Esta acción implementa la validación de alta
     de nuevos socios a la asociación.
   - Listado de socios: Esta acción muestra la lista de socios de la
     asociación.
   - Baja de socios: Esta acción elimina un socio de la asociación.
   - Cambio de los datos de socio: Esta acción permite el cambio de
     los datos relacionados con el socio que está dentro de la
     aplicación.

 * Gestor de asociación: Actualmente este módulo implementa únicamente 
   la petición de alta de una nueva asociación.
