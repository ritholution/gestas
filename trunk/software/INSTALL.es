Instalar GESTAS
===============
Actualmente GESTAS sólo se puede instalar desde el código fuente (no
hay paquete para ninguna distribución), con lo que los pasos a seguir
para instalar GESTAS son las siguientes:

1.- Descargar el código fuente: El código fuente del programa está
disponible en un repositorio alojado en http://opentia.net/GESTAS, por
lo que para descargarlo tendrás que tener instalado subversion y
ejecutar el siguiente comando:

	 $ svn co https://forja.rediris.es/svn/cusl3-gestas/trunk/software gestas

    Subversion (http://subversion.tigris.org) es un sistema de gestión de
versiones que esta disponible en la mayoría de las distribuciones
GNU/Linux como paquete binario, con lo que resulta sencillo
instalarselo a partir de un gestor de paquetes.

2.- Instalar dependencias: Actualmente GESTAS depende de los
siguientes paquetes Debian: mysql-server-5.0, mysql-client-5.0, apache2,
libapache2-mod-php5, php5, php5-mysql y php-gettext. Estos paquetes
son de uso común (aunque pueden tener otro nombre), con lo que la
mayoría de las distribuciones tienen un paquete binario para su fácil
instalación.

3.- Crear la base de datos: GESTAS trabaja con una base de datos, con
lo que tendremos que crear una y dar permisos a un usuario para acceder
a dicha base de datos. En MySQL las bases de datos se crean con el
siguiente comando:

	  $ mysqladmin -u <admin> create gestas

    donde <admin> es el administrador del servidor mysql (generalmente
el usuario root) y gestas es el nombre de la base de datos. Para crear
un nuevo usuario y darle permisos sobre la base de datos debemos de
ejecutar las siguientes instrucciones:

     	  $ mysql -u <admin>
	  sql> GRANT ALL PRIVILEGES ON gestas.* TO usuario_gestas IDENTIFIED BY
	  'contraseña_gestas';
          sql> FLUSH PRIVILEGES;
	  sql> exit

    donde admin, como en el caso anterior, es el administrador del
servidor mysql, usuario_gestas es el usuario que va a administrar la
base de datos de la aplicación y contraseña_gestas es la contraseña
que va a utilizar el administrador de la base de datos de la aplicación.

4.- Configuración de parámetros: Antes de cargar las tablas a la base
de datos hay que configurar algunos parámetros básicos que utiliza la
aplicación. Para ello hay que crear un fichero, mysql-configs.sql, en
el directorio sql de donde se haya descargado gestas. En ese mismo directorio
verás un fichero, mysql-configs.sql.default, con los parámetros que hay que 
configurar, con lo que lo puedes copiar a mysql-configs.sql y configurar los 
parámetros convenientemente. Actualmente el parámetro que debes de modificar es
dir_base, que indica el directorio base donde se encuentra la
aplicación (se representarÃ¡ a partir de ahora como <GESTAS>).

5.- Cargar el esquema: Despues de haber configurado los parámetros que
se van a insertar en la base de datos deberás cargar el esquema de
tablas y los datos en la base de datos, para lo cual deberás de
ejecutar las siguientes instrucciones:

     	  $ cd <GESTAS>/sql
          $ mysql -u <admin>
          sql> use gestas;
	  sql> source mysql.sql;
	  sql> exit;

Nota: esta orden borra al completo la base de datos gestas que hubiera
anteriormente.

6.- Configurar la conexión a la base de datos: Una vez tenemos cargado
el esquema de la base de datos vamos a crear un pequeño archivo,
const.php, en el que indicaremos los datos de conexión a la base de
datos. Para ello vamos al directorio raíz donde está el código fuente,
copiamos el fichero const.php.default a const.php y editamos dicho
fichero, indicando todos los datos de conexión a la base de datos
(usuario, contraseña, host, nombre de la base de datos y puerto). Como
base se puede copiar el fichero const.php.default que existe en dicho
directorio.

7.- Dar acceso a la aplicación: Para que la aplicación se vea desde
cualquier ordenador deberemos de configurar el servidor web
Apache. Para eso hay que crear un nuevo fichero en
/etc/apache2/sites-available con los datos del VirtualHost y enlazar
desde /etc/apache2/sites-enabled a este fichero, reiniciando el
servidor una vez este configurado. Si lo que quieres es probar la
aplicación de manera local basta con poner en un directorio o
subdirectorio que este ya configurado con Apache para poder acceder
desde local.

8.- Reiniciar apache con /etc/init.d/apache2 restart

Con estos sencillos pasos tendrás ya instalada una versión de GESTAS,
así que diviértete y ¡a hackear!

NOTA IMPORTANTE: actualmente existe una limitación por la cual GESTAS
no funciona con la versión oldestable de Debian (etch). En Debian
stable (lenny) sí funciona perfectamente. Parece ser un problema con
el php5 de dicha versión.
