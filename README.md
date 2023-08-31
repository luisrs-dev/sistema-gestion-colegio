# Sistema de Gestión de Colegio

Este proyecto es un sistema de gestión de colegio que utiliza React.js para el frontend y Symfony para el backend. El objetivo principal de esta aplicación es proporcionar una plataforma integral que permita a los usuarios administradores configurar el sistema, a los profesores ingresar las notas de los alumnos y a los apoderados revisar las notas de sus hijos en línea. A continuación, se detallan las características principales y la estructura del proyecto.

## Características

### Usuarios Administradores
- Iniciar sesión para los usuarios administradores.
- Configuración global del sistema, incluyendo cursos, asignaturas, periodos y notas por asignatua.
- Creación de profesores y usuarios apoderados.

### Usuarios Profesores
- Acceso al sistema mediante autenticación.
- Registro y actualización de las notas de los alumnos asignados.
- Visualización de notas con reportes por pdf.

### Usuarios Apoderados
- Autenticación para acceder a la plataforma.
- Visualización de las notas de sus hijos.
- TODO: Recepción de notificaciones relacionadas con el progreso académico.

## Estructura del Proyecto

El proyecto está dividido en dos partes principales: el frontend desarrollado con React.js y el backend implementado con Symfony.

## Requisitos de Instalación

Para ejecutar este proyecto en un entorno local, sigue estos pasos:

1. Clona este repositorio en tu máquina local.
2. Configura la base de datos en el archivo `.env` del directorio `/backend`.
3. En el directorio `/backend`, ejecuta `composer install` para instalar las dependencias de Symfony.
4. En el directorio `/frontend`, ejecuta `npm install` para instalar las dependencias de React.js.
5. Ejecuta el servidor de desarrollo de Symfony y el servidor de desarrollo de React.js por separado.

## Contribuciones

¡Las contribuciones son bienvenidas! Si deseas mejorar este proyecto, realiza un fork del repositorio y envía tus pull requests. Asegúrate de seguir las pautas de contribución y mantener el código limpio y bien documentado.

## Contacto

Si tienes alguna pregunta o comentario, no dudes en ponerte en contacto.
