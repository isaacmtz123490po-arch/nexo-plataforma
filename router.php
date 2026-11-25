<?php
// Nexo Apps: PHP web gateway -> local Python service.
// Development router; for production use PHP-FPM with the documented route rules.
declare(strict_types=1);
$root = __DIR__;
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($path === '/' || $path === '/index.html') {
    header('Content-Type: text/html; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    readfile($root . '/index.html');
    return true;
}
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
if ($path !== '/api/apps') {
    http_response_code(404);
    echo json_encode(['error' => 'No encontrado']);
    return true;
}
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (!in_array($method, ['GET', 'POST'], true)) {
    header('Allow: GET, POST');
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    return true;
}
if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 8192) {
    http_response_code(413);
    echo json_encode(['error' => 'Solicitud demasiado grande']);
    return true;
}
$body = $method === 'POST' ? file_get_contents('php://input', false, null, 0, 8193) : '';
if ($body === false || strlen($body) > 8192) {
    http_response_code(413);
    echo json_encode(['error' => 'Solicitud demasiado grande']);
    return true;
}
// Fixed private address: never accepts a destination supplied by a request.
$headers = "Accept: application/json\r\nContent-Type: application/json\r\n";
$context = stream_context_create(['http' => [
    'method' => $method,
    'header' => $headers,
    'content' => $body,
    'timeout' => 5,
    'ignore_errors' => true,
    'follow_location' => 0,
]]);
$response = @file_get_contents('http://127.0.0.1:8001/api/apps', false, $context);
if ($response === false) {
    http_response_code(503);
    echo json_encode(['error' => 'El servicio Python no está disponible']);
    return true;
}
$status = 502;
if (isset($http_response_header[0]) && preg_match('~^HTTP/\S+\s+(\d{3})~', $http_response_header[0], $matches)) {
    $status = (int)$matches[1];
}
http_response_code($status);
echo $response;
return true;
