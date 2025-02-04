# Inscripción a Cursos de Formación

## Descripción
Este proyecto es una aplicación web desarrollada en PHP que permite gestionar la inscripción de profesores a cursos de formación. La asignación de plazas se realiza de manera automatizada según los méritos de cada solicitante una vez finalizado el plazo de inscripción.

## Tecnologías Utilizadas
- **PHP** para la lógica del servidor.
- **MySQL** como sistema de gestión de base de datos.
- **HTML, CSS y JavaScript** para la interfaz de usuario.

## Base de Datos
La aplicación utiliza una base de datos llamada `cursoscp` con las siguientes tablas:

<div align="center">
  <img src="imgGithub/GestionProfesoradoPHPDB.PNG" alt="Base de datos" width="500">
</div>

## Funcionalidades
- **Autenticación de usuarios**.
<div align="center">
  <img src="imgGithub/GestionProfesoradoPHPLogin.PNG" alt="Base de datos" width="500">
</div>
- **Activar/Desactivar cursos** (Administrador).
- **Listar cursos activos**.
- **Realizar inscripción en un curso**.
- **Baremación automática de solicitantes tras cierre de inscripción** (Administrador).
- **Listar admitidos en un curso** (Administrador).
- **Añadir/Eliminar cursos** (Administrador).
- **Notificación por correo tras la inscripción**.

## Criterios de Baremación
La asignación de plazas se realiza según los siguientes méritos:

| Mérito | Puntos |
|--------|--------|
| Coordinador TIC | 3 |
| Grupo relacionado con las TICs | 3 |
| Programa bilingüe | 3 |
| Cargo de director | 1 |
| Cargo de Jefe de Estudios | 1 |
| Cargo de Secretario | 1 |
| Cargo de Jefe de Departamento | 1 |
| Antigüedad | 15 |
| Profesor en activo | 3 |

## Instalación y Configuración
1. Clonar este repositorio:
   ```bash
   git clone https://github.com/sam324sam/inscripcion-cursos.git
   ```
2. Configurar el servidor web con PHP y MySQL.
3. Importar la base de datos desde `cursoscp.sql`.
4. Configurar las credenciales de la base de datos en `config/db.php`.
5. Ejecutar la aplicación desde el navegador.

## Autor
Este proyecto ha sido desarrollado por [tu nombre].

## Licencia
Este proyecto se distribuye bajo la licencia MIT.

