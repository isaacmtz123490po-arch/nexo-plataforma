# Nexo Apps

Nexo es **una sola aplicación** con interfaz HTML/CSS/JavaScript y un backend conjunto: **PHP sirve la interfaz y recibe las peticiones web; Python/Flask procesa el catálogo y accede a SQLite**. PHP reenvía `/api/apps` al servicio Python privado, siempre en `127.0.0.1:8001`. No son dos alternativas; los dos participan en la misma aplicación dinámica.

- [Demo estática en GitHub Pages](https://isaacmtz123490po-arch.github.io/nexo-plataforma/)
- [Repositorio de Nexo](https://github.com/isaacmtz123490po-arch/nexo-plataforma)
- [Awesome Selfhosted](https://github.com/awesome-selfhosted/awesome-selfhosted), referencia pública para descubrir software autoalojable.

## Demo web

Inicio, exploración con filtros, guardados, dispositivos y sección informativa; animaciones CSS con soporte para reducción de movimiento; diseño adaptable. Favoritos y acceso son **simulados y locales al navegador**: no hay contraseña, autenticación real, cuentas compartidas ni sincronización. Los datos iniciales enlazan a Nextcloud, Jellyfin e Immich, no se ejecutan sus repositorios.

**GitHub Pages solo publica el frontend**. Busca y filtra el catálogo de ejemplo usando JavaScript en el navegador; no ejecuta PHP, Python ni la base de datos. Para tener la aplicación dinámica es necesario un host que ejecute ambos servicios. El frontend usa datos de respaldo si la API no está disponible.

## Ejecutar el programa completo en desarrollo

Requisitos: PHP con `allow_url_fopen` habilitado; Python 3.10+, SQLite y Flask. Ejecuta ambos procesos en la misma máquina.

En una terminal, inicia Python:

```sh
python -m venv .venv
# macOS/Linux: source .venv/bin/activate
# Windows PowerShell: .venv\Scripts\Activate.ps1
pip install -r requirements.txt
python app.py
```

En otra terminal, desde la misma carpeta, inicia PHP:

```sh
php -S 127.0.0.1:8000 -t . router.php
```

Abre `http://127.0.0.1:8000`. PHP entrega `index.html` y reenvía `/api/apps` a Python en el puerto local 8001. Python inicializa `storage/catalog.sqlite3` y guarda propuestas con estado pendiente. Ambos procesos son necesarios para el catálogo dinámico; el navegador puede mostrar los ejemplos si la API no está disponible.

### Contrato de API

- `GET /api/apps` devuelve fichas aprobadas con `name`, `category`, `description`, `repo_url` y `tags`.
- `POST /api/apps` recibe JSON con esos campos; valida los valores y las URL de GitHub y guarda la propuesta como `pending`. No se publica automáticamente. El formulario de propuestas aún no está conectado en la web.

### Producción y seguridad

Los servidores integrados anteriores son **solo para desarrollo**. En producción usa un servidor WSGI para Flask y PHP-FPM detrás de HTTPS, con rutas explícitas: `/api/apps` hacia PHP, `/` a la interfaz, y ningún acceso público a Python, código fuente o `storage/`. El script de router no constituye por sí mismo una configuración de producción. Añade autenticación, moderación, limitación de solicitudes, backups y controles antes de abrir el POST al público.

El sistema **no clona ni ejecuta repositorios**. Para hacerlo se necesitaría un sandbox seguro aislado, recursos y red limitados, y separación entre usuarios y secretos. No ejecutes código desconocido en el proceso web.

## Pruebas

La API Python se comprobó localmente con el cliente de pruebas de Flask. La interfaz estática debe verificarse en GitHub Pages. La cadena completa PHP→Python no se pudo comprobar en este entorno porque PHP no está instalado.
