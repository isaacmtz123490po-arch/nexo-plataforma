<?php
// Nexo Apps demo API. Stores metadata only; never executes repository code.
declare(strict_types=1);
$root = __DIR__;
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($path === '/' || $path === '/index.html') { readfile($root . '/index.html'); return true; }
if ($path !== '/api/apps') { http_response_code(404); header('Content-Type: application/json; charset=utf-8'); echo json_encode(['error'=>'No encontrado']); return true; }
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
$dbDir = $root . '/storage';
if (!is_dir($dbDir)) { mkdir($dbDir, 0750, true); }
try {
  $pdo = new PDO('sqlite:' . $dbDir . '/catalog.sqlite3', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  $pdo->exec("CREATE TABLE IF NOT EXISTS apps (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, category TEXT NOT NULL, description TEXT NOT NULL, repo_url TEXT NOT NULL, tags TEXT NOT NULL, status TEXT NOT NULL DEFAULT 'approved')");
  if ((int)$pdo->query('SELECT COUNT(*) FROM apps')->fetchColumn() === 0) {
    $seed = [
      ['Nextcloud','Archivos','Plataforma para almacenar y sincronizar archivos, contactos y calendarios en tu propio servidor.','https://github.com/nextcloud/server','Archivos,Colaboración,PHP'],
      ['Jellyfin','Multimedia','Servidor multimedia de software libre para organizar y reproducir tu biblioteca personal.','https://github.com/jellyfin/jellyfin','Multimedia,Servidor,.NET'],
      ['Immich','Fotos y video','Solución autoalojable para administrar y explorar colecciones personales de fotos y videos.','https://github.com/immich-app/immich','Fotos,Video,Autoalojable']
    ];
    $ins=$pdo->prepare("INSERT INTO apps(name,category,description,repo_url,tags,status) VALUES(?,?,?,?,?,'approved')"); foreach($seed as $row){$ins->execute($row);}
  }
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $rows=$pdo->query("SELECT name,category,description,repo_url,tags FROM apps WHERE status='approved' ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as &$row){$row['tags']=array_filter(explode(',', $row['tags']));} unset($row); echo json_encode($rows, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); return true;
  }
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d=json_decode(file_get_contents('php://input'), true); if(!is_array($d)){http_response_code(400);echo json_encode(['error'=>'JSON inválido']);return true;}
    $name=trim((string)($d['name']??''));$category=trim((string)($d['category']??''));$description=trim((string)($d['description']??''));$repo=trim((string)($d['repo_url']??''));$tags=$d['tags']??[];
    if($name===''||strlen($name)>80||$category===''||strlen($category)>50||$description===''||strlen($description)>500){http_response_code(400);echo json_encode(['error'=>'Completa nombre, categoría y descripción dentro del límite.']);return true;}
    if(!preg_match('~^https://github\.com/[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+/?$~',$repo)){http_response_code(400);echo json_encode(['error'=>'El repositorio debe ser una URL HTTPS de github.com/propietario/repositorio.']);return true;}
    if(!is_array($tags)||count($tags)>8){http_response_code(400);echo json_encode(['error'=>'Etiquetas inválidas.']);return true;}
    foreach($tags as $tag){if(!is_string($tag)||strlen($tag)>24){http_response_code(400);echo json_encode(['error'=>'Etiquetas inválidas.']);return true;}}
    $ins=$pdo->prepare("INSERT INTO apps(name,category,description,repo_url,tags,status) VALUES(?,?,?,?,?,'pending')");$ins->execute([$name,$category,$description,$repo,implode(',',$tags)]);
    http_response_code(201);echo json_encode(['message'=>'Sugerencia recibida para revisión; no se ejecutó código.','status'=>'pending'],JSON_UNESCAPED_UNICODE);return true;
  }
  header('Allow: GET, POST');http_response_code(405);echo json_encode(['error'=>'Método no permitido']);
} catch (Throwable $e) { http_response_code(500); echo json_encode(['error'=>'No se pudo abrir el catálogo SQLite. Verifica PDO SQLite y permisos de storage.']); }
return true;
