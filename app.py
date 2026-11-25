"""Nexo Apps demo API. Stores catalog metadata only; never runs repository code."""
from pathlib import Path
import re
import sqlite3
from flask import Flask, jsonify, request, send_from_directory

app = Flask(__name__, static_folder=None)
DB = Path(__file__).with_name('storage') / 'catalog.sqlite3'
SEED = [
    ('Nextcloud','Archivos','Plataforma para almacenar y sincronizar archivos, contactos y calendarios en tu propio servidor.','https://github.com/nextcloud/server','Archivos,Colaboración,PHP'),
    ('Jellyfin','Multimedia','Servidor multimedia de software libre para organizar y reproducir tu biblioteca personal.','https://github.com/jellyfin/jellyfin','Multimedia,Servidor,.NET'),
    ('Immich','Fotos y video','Solución autoalojable para administrar y explorar colecciones personales de fotos y videos.','https://github.com/immich-app/immich','Fotos,Video,Autoalojable'),
]

def connect():
    DB.parent.mkdir(parents=True, exist_ok=True)
    con = sqlite3.connect(DB)
    con.row_factory = sqlite3.Row
    con.execute('''CREATE TABLE IF NOT EXISTS apps (
        id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, category TEXT NOT NULL,
        description TEXT NOT NULL, repo_url TEXT NOT NULL, tags TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'approved')''')
    if con.execute('SELECT COUNT(*) FROM apps').fetchone()[0] == 0:
        con.executemany('INSERT INTO apps(name,category,description,repo_url,tags,status) VALUES(?,?,?,?,?,?)',
                        [(*row, 'approved') for row in SEED])
        con.commit()
    return con

@app.get('/')
def home():
    return send_from_directory(Path(__file__).parent, 'index.html')

@app.get('/api/apps')
def list_apps():
    with connect() as con:
        rows = con.execute("SELECT name,category,description,repo_url,tags FROM apps WHERE status='approved' ORDER BY name").fetchall()
    return jsonify([dict(r) | {'tags': r['tags'].split(',')} for r in rows])

@app.post('/api/apps')
def suggest_app():
    data = request.get_json(silent=True) or {}
    name = str(data.get('name','')).strip()
    category = str(data.get('category','')).strip()
    description = str(data.get('description','')).strip()
    repo_url = str(data.get('repo_url','')).strip()
    tags = data.get('tags', [])
    if not name or len(name) > 80 or not category or len(category) > 50 or not description or len(description) > 500:
        return jsonify(error='Completa nombre, categoría y descripción dentro del límite.'), 400
    if not re.fullmatch(r'https://github\.com/[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+/?', repo_url):
        return jsonify(error='El repositorio debe ser una URL HTTPS de github.com/propietario/repositorio.'), 400
    if not isinstance(tags, list) or len(tags) > 8 or any(not isinstance(t,str) or len(t)>24 for t in tags):
        return jsonify(error='Etiquetas inválidas.'), 400
    with connect() as con:
        con.execute('INSERT INTO apps(name,category,description,repo_url,tags,status) VALUES(?,?,?,?,?,?)',
                    (name,category,description,repo_url,','.join(tags),'pending'))
    return jsonify(message='Sugerencia recibida para revisión; no se ejecutó código.', status='pending'), 201

if __name__ == '__main__':
    # Local development only; deploy behind a production WSGI server.
    app.run(host='127.0.0.1', port=8000, debug=False)
