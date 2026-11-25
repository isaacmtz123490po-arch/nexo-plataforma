# Nexo — plataforma creativa

Prototipo visual responsive en español para organizar proyectos, recursos, enlaces y contenido en un espacio de trabajo.

## Vista previa

- Página publicada: https://site.zapia.com/mkwdhnyr
- Código fuente: https://github.com/isaacmtz123490po-arch/nexo-plataforma

## Estado actual

La página es estática: contiene diseño adaptable y secciones de muestra para espacios, proyectos, enlaces, recursos y videos. Las cifras y tarjetas son ilustrativas.

**Aún no incluye** cuentas de usuario, base de datos, cargas reales de archivos, permisos, colaboración ni sincronización en vivo entre visitantes. Los botones son navegación de la maqueta; no crean ni guardan datos. No se debe presentar como una aplicación multiusuario lista para producción. Para esas funciones hace falta escoger y conectar backend, base de datos y almacenamiento; ese paso queda pendiente de definir.

## Publicar en GitHub Pages

En el repositorio, entra a **Settings → Pages**, elige **Deploy from a branch**, selecciona `main` y la carpeta `/ (root)`, y guarda. GitHub Pages actualizará la página cuando se publique un cambio en esa rama. GitHub Pages solo aloja el frontend estático; no ejecuta Python ni PHP ni proporciona backend en tiempo real.

## Referencias revisadas

Estas referencias sirvieron para comparar enfoques; este prototipo se escribió aparte y no copia sus archivos:

- [Flowbite Admin Dashboard](https://github.com/themesberg/flowbite-admin-dashboard) — dashboard gratuito, licencia MIT, con layouts y componentes CRUD.
- [Django + React SaaS Boilerplate](https://github.com/charithmadhuranga/Django-React-saas-boilerplate) — base SaaS más amplia, licencia MIT; requiere configurar backend e infraestructura.

## Tecnologías

HTML y CSS vanilla en `index.html`. No se incluye backend Python o PHP todavía.
