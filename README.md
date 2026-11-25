# Nexo Apps

Catálogo responsive en español para descubrir proyectos de software abierto y navegar a sus repositorios oficiales. Basado visualmente en una interfaz propia; las aplicaciones se enlazan desde sus fuentes originales. La lista [Awesome Selfhosted](https://github.com/awesome-selfhosted/awesome-selfhosted) es una referencia pública para ampliar el directorio.

## Sitios

- Demo estática (GitHub Pages): https://isaacmtz123490po-arch.github.io/nexo-plataforma/
- Repositorio: https://github.com/isaacmtz123490po-arch/nexo-plataforma

## Qué incluye

- `index.html`: frontend responsive HTML/CSS/JavaScript con búsqueda, filtros y catálogo de demostración.
- `app.py`: API Python/Flask + SQLite (`GET /api/apps`, `POST /api/apps`).
- `router.php`: implementación equivalente en PHP + PDO SQLite.
- `requirements.txt`: dependencia de Python.

Ambos backends usan el mismo esquema SQLite y muestran Nextcloud, Jellyfin e Immich como ejemplos con enlaces a sus repositorios oficiales. Las propuestas enviadas por POST quedan `pending`; no se publican automáticamente. El frontend en Pages usa sus datos embebidos de respaldo, porque GitHub Pages no ejecuta Python/PHP. Para usar API dinámica hay que alojar frontend y backend en el mismo origen, o configurar CORS de forma segura.

## Ejecutar la demo local

### Python

```sh
python -m venv .venv
. .venv/bin/activate  # Windows: .venv\\Scripts\\activate
pip install -r requirements.txt
python app.py
```

Abre http://127.0.0.1:8000. Para producción, usa un WSGI server y un proxy HTTPS; no uses `app.run` como servidor público.

### PHP

Requiere PHP con PDO SQLite habilitado.

```sh
php -S 127.0.0.1:8000 -t . router.php
```

Abre http://127.0.0.1:8000. Configura `storage/` con permisos apropiados en un host real.

## Seguridad y límites

El API guarda solo metadatos; restringe los repositorios a URLs HTTPS de GitHub y deja nuevas propuestas en revisión. No clona, construye ni ejecuta código enviado por usuarios. No publiques estas APIs directamente sin agregar autenticación/moderación, rate limiting, protección contra CSRF donde aplique, logs y respaldos.

**Ejecutar repositorios de terceros no está implementado.** Para eso hace falta un servicio de sandbox aislado (contenedores/VMs efímeros, límites de recursos, red restringida, sin secretos, separación entre usuarios y eliminación de instancias). No ejecutes código desconocido en el proceso web ni en el mismo host con tus credenciales.

## Fuente de catálogo

- [Awesome Selfhosted](https://github.com/awesome-selfhosted/awesome-selfhosted) — directorio comunitario de servicios de software libre y aplicaciones web autoalojables. Comprueba licencia y mantenimiento de cada proyecto antes de usarlo.
- [Nextcloud Server](https://github.com/nextcloud/server)
- [Jellyfin](https://github.com/jellyfin/jellyfin)
- [Immich](https://github.com/immich-app/immich)

Nexo Apps no está afiliado a esos proyectos ni redistribuye su código.
