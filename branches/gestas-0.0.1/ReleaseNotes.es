 Notas de Publicación de Gestas
================================

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
