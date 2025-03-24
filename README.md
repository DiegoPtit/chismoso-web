# El Chismoso (WebApp)

El Chismoso es una aplicación web basada en el [Yii 2 Basic Project Template](https://www.yiiframework.com), diseñada para facilitar el desarrollo rápido de proyectos pequeños y medianos. Este proyecto incluye funcionalidades básicas como inicio de sesión, envío de formularios de contacto y una estructura modular que te permite concentrarte en la incorporación de nuevas características.

## Estructura del Proyecto

El proyecto sigue la estructura estándar de Yii 2, organizada de la siguiente forma:

```
assets/           # Definición y gestión de recursos estáticos (CSS, JS, imágenes)
commands/         # Comandos de consola (controllers) para tareas específicas
components/       # Componentes reutilizables y clases auxiliares
config/           # Configuraciones de la aplicación (base de datos, web, etc.)
controllers/      # Controladores para manejar las solicitudes web
mail/             # Plantillas y vistas para los correos electrónicos
models/           # Clases de modelo que representan la lógica y estructura de datos
runtime/          # Archivos y logs generados en tiempo de ejecución
tests/            # Pruebas unitarias, funcionales y de aceptación
vagrant/          # Archivos de configuración para entornos Vagrant
views/            # Vistas y templates para la interfaz de usuario
web/              # Punto de entrada y recursos web (scripts, assets, etc.)
widgets/          # Widgets y componentes gráficos personalizados
```

## Requerimientos

- **PHP:** Versión 7.4 o superior.
- **Servidor Web:** Compatible con PHP (Apache, Nginx, etc.).
- **Base de Datos:** MySQL, PostgreSQL u otro motor soportado por Yii.
- **Composer:** Para la gestión de dependencias.

## Instalación

### Instalación vía Composer

Si aún no tienes instalado [Composer](https://getcomposer.org), sigue las instrucciones en su [sitio oficial](https://getcomposer.org).

Para instalar el proyecto, ejecuta el siguiente comando:

```bash
composer create-project --prefer-dist yiisoft/yii2-app-basic basic
```

Una vez instalado, asegúrate de que el directorio `basic` se encuentre directamente bajo la raíz de tu servidor web y accede a la aplicación a través de:

```
http://localhost/basic/web/
```

### Instalación desde un Archivo de Archivo

1. Descarga y extrae el archivo comprimido desde [yiiframework.com](https://www.yiiframework.com).
2. Renombra el directorio extraído a `basic` y colócalo en la raíz de tu servidor web.
3. Edita el archivo `config/web.php` y establece la clave de validación de cookies:

   ```php
   'request' => [
       // ¡Inserta una clave secreta aquí para la validación de cookies!
       'cookieValidationKey' => '<clave_secreta_aquí>',
   ],
   ```

4. Accede a la aplicación en:

```
http://localhost/basic/web/
```

### Instalación con Docker

1. Actualiza los paquetes del proyecto:

    ```bash
    docker-compose run --rm php composer update --prefer-dist
    ```

2. Ejecuta los triggers de instalación (para generar la clave de validación de cookies):

    ```bash
    docker-compose run --rm php composer install
    ```

3. Levanta el contenedor:

    ```bash
    docker-compose up -d
    ```

4. Accede a la aplicación a través de:

```
http://127.0.0.1:8000
```

*Notas:*
- Se requiere Docker Engine versión `17.04` o superior.
- La configuración predeterminada utiliza un volumen en el directorio personal (`.docker-composer`) para almacenar cachés de Composer.

## Configuración de la Base de Datos

Edita el archivo `config/db.php` con los datos reales de conexión. Por ejemplo:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

*Notas:*
- Yii no crea la base de datos automáticamente, deberás crearla manualmente.
- Revisa y ajusta los demás archivos de configuración en el directorio `config/` según las necesidades de tu proyecto.

## Pruebas (Testing)

Las pruebas se encuentran en el directorio `tests` y están desarrolladas con el [Framework de Pruebas Codeception](https://codeception.com). Existen tres suites principales:

- **unit:** Pruebas de componentes individuales.
- **functional:** Pruebas de interacción del usuario y flujo de la aplicación.
- **acceptance:** Pruebas de aceptación que simulan la interacción real en un navegador (requieren configuración adicional).

### Ejecutar las Pruebas

Para ejecutar las pruebas unitarias y funcionales, utiliza el siguiente comando:

```bash
vendor/bin/codecept run
```

### Pruebas de Aceptación

1. Renombra el archivo `tests/acceptance.suite.yml.example` a `tests/acceptance.suite.yml`.
2. En `composer.json`, reemplaza el paquete `codeception/base` por `codeception/codeception` para instalar la versión completa.
3. Actualiza las dependencias con Composer:

    ```bash
    composer update
    ```

4. Descarga y lanza [Selenium Server](https://www.seleniumhq.org):

    ```bash
    java -jar ~/selenium-server-standalone-x.xx.x.jar
    ```

   *Si usas Selenium Server 3.0 o superior con Firefox o Chrome, asegúrate de descargar [GeckoDriver](https://github.com/mozilla/geckodriver) o [ChromeDriver](https://sites.google.com) y lanzarlo adecuadamente:*

    ```bash
    # Para Firefox
    java -jar -Dwebdriver.gecko.driver=~/geckodriver ~/selenium-server-standalone-3.xx.x.jar

    # Para Google Chrome
    java -jar -Dwebdriver.chrome.driver=~/chromedriver ~/selenium-server-standalone-3.xx.x.jar
    ```

5. (Opcional) Crea la base de datos de pruebas `yii2basic_test` y aplícale las migraciones, si es necesario:

    ```bash
    tests/bin/yii migrate
    ```

6. Levanta el servidor web para pruebas:

    ```bash
    tests/bin/yii serve
    ```

7. Ejecuta todas las pruebas disponibles:

    ```bash
    # Todas las pruebas
    vendor/bin/codecept run

    # Solo pruebas de aceptación
    vendor/bin/codecept run acceptance

    # Pruebas unitarias y funcionales
    vendor/bin/codecept run unit,functional
    ```

### Cobertura de Código

Para generar reportes de cobertura, descomenta las líneas necesarias en `codeception.yml` y ejecuta:

```bash
# Cobertura para todas las pruebas
vendor/bin/codecept run --coverage --coverage-html --coverage-xml

# Cobertura solo para pruebas unitarias
vendor/bin/codecept run unit --coverage --coverage-html --coverage-xml

# Cobertura para pruebas unitarias y funcionales
vendor/bin/codecept run functional,unit --coverage --coverage-html --coverage-xml
```

Los reportes se encontrarán en el directorio `tests/_output`.

## Acerca del Proyecto

**El Chismoso (WebApp)** es una aplicación desarrollada para compartir información y noticias de manera dinámica. La aplicación se beneficia de la estructura y flexibilidad del framework Yii 2, permitiendo una rápida adaptación y escalabilidad en proyectos de diversa índole.

### Recursos Adicionales

- [Documentación de Yii 2](https://www.yiiframework.com/doc/guide/2.0/en)
- [Guía de Codeception](https://codeception.com/docs/)

## Licencia

Este proyecto se distribuye bajo la licencia [BSD-3-Clause](LICENSE.md).

