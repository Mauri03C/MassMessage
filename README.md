# Sistema de Mensajería Masiva (MassMessage)

## Descripción
MassMessage es un sistema completo de mensajería masiva que permite enviar mensajes a múltiples destinatarios a través de diferentes canales (email, WhatsApp, SMS), con características avanzadas de gestión, monitoreo y análisis.

## Características Principales

### 1. Gestión de Mensajes
- Envío de mensajes masivos por múltiples canales
- Sistema de plantillas personalizables
- Cola de procesamiento en segundo plano
- Seguimiento de estado de envío

### 2. Sistema de Plantillas
- Creación y gestión de plantillas para diferentes tipos de mensajes
- Variables dinámicas personalizables
- Previsualización de plantillas
- Organización por categorías

### 3. Sistema de Roles y Permisos
- Roles predefinidos (admin, editor, viewer)
- Permisos personalizables por rol
- Control de acceso granular
- Gestión de usuarios y asignación de roles

### 4. Reportes y Estadísticas
- Dashboard interactivo
- Widgets personalizables
- Análisis de tendencias
- Exportación en múltiples formatos (PDF, Excel)
- Programación de reportes automáticos

### 5. Monitoreo y Alertas
- Sistema de notificaciones en tiempo real
- Alertas configurables
- Monitoreo de actividad de usuarios
- Registro de eventos del sistema

## Requisitos Técnicos

### Servidor
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Composer (Gestor de dependencias)
- Servidor web (Apache/Nginx)

### Dependencias Principales
- PHPMailer (Envío de emails)
- TCPDF (Generación de PDFs)
- PhpSpreadsheet (Exportación Excel)
- Bootstrap 5 (Framework CSS)
- Chart.js (Gráficos)
- Ratchet (WebSockets)

## Estructura de la Base de Datos

### Tablas Principales
1. `users`: Gestión de usuarios
2. `messages`: Mensajes y su estado
3. `recipients`: Destinatarios y estado de entrega
4. `templates`: Plantillas de mensajes
5. `roles`: Roles del sistema
6. `user_roles`: Asignación de roles a usuarios
7. `role_permissions`: Permisos por rol
8. `notifications`: Sistema de notificaciones
9. `report_schedules`: Programación de reportes
10. `user_widgets`: Widgets personalizados
11. `user_activities`: Registro de actividades
12. `alerts`: Configuración de alertas
13. `notification_templates`: Plantillas de notificaciones

## Funcionalidades por Módulo

### Módulo de Mensajería
- Creación y envío de mensajes masivos
- Gestión de destinatarios
- Seguimiento de estado de envío
- Sistema de cola para procesamiento

### Módulo de Plantillas
- Editor de plantillas con variables
- Previsualización en tiempo real
- Historial de versiones
- Categorización de plantillas

### Módulo de Reportes
- Generación de reportes personalizados
- Programación de envío automático
- Múltiples formatos de exportación
- Análisis estadístico avanzado

### Módulo de Administración
- Gestión de usuarios y roles
- Configuración del sistema
- Monitoreo de rendimiento
- Logs del sistema

## Instalación

1. Clonar el repositorio
```bash
git clone [url-del-repositorio]
```

## Configuración de la Base de Datos

### Crear la Base de Datos

1. Accede a MySQL:
```bash
mysql -u root -p
```