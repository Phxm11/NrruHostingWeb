"""Build a source-only handover archive from an explicit allowlist."""
from pathlib import Path
import hashlib
import json
import zipfile

ROOT = Path(__file__).resolve().parent.parent
OUTPUT = ROOT / 'dist' / 'NRRU-Hosting-Handover.zip'
OUTPUT.parent.mkdir(exist_ok=True)
files = set()
for directory in ('app', 'config', 'resources', 'routes', 'tests', 'docs', 'scripts'):
    for path in (ROOT / directory).rglob('*'):
        if path.is_file() and not path.is_symlink() and path.suffix in ('.php', '.css', '.js', '.md', '.py'):
            files.add(path)
for directory in ('database/migrations', 'database/factories', 'database/seeders'):
    files.update((ROOT / directory).glob('*.php'))
for directory in ('public',):
    for path in (ROOT / directory).rglob('*'):
        relative = path.relative_to(ROOT).as_posix()
        if any(relative.startswith(prefix) for prefix in ('public/storage/', 'public/build/')):
            continue
        if path.is_file() and not path.is_symlink() and (path.suffix.lower() in ('.php', '.png', '.ico', '.txt') or path.name == '.htaccess'):
            files.add(path)
for name in ('artisan', 'bootstrap/app.php', '.env.example', '.gitignore', '.editorconfig', '.gitattributes',
             'composer.json', 'composer.lock', 'package.json', 'package-lock.json', 'phpunit.xml',
             'vite.config.js', 'README.md', 'database.md'):
    files.add(ROOT / name)

manifest = {}
with zipfile.ZipFile(OUTPUT, 'w', zipfile.ZIP_DEFLATED) as archive:
    for path in sorted(files):
        relative = path.relative_to(ROOT).as_posix()
        assert relative != '.env' and not relative.startswith(('storage/', 'vendor/', 'node_modules/'))
        assert not path.is_symlink()
        content = path.read_bytes()
        archive.writestr(relative, content)
        manifest[relative] = hashlib.sha256(content).hexdigest()
    for directory in ('bootstrap/cache', 'storage/app/private', 'storage/app/public', 'storage/framework/cache/data',
                      'storage/framework/sessions', 'storage/framework/views', 'storage/logs'):
        archive.writestr(directory + '/.gitignore', '*\n!.gitignore\n')
    archive.writestr('HANDOVER-MANIFEST.json', json.dumps(manifest, ensure_ascii=False, indent=2))
with zipfile.ZipFile(OUTPUT) as archive:
    assert archive.testzip() is None
    for name, digest in manifest.items():
        assert hashlib.sha256(archive.read(name)).hexdigest() == digest
checksum = hashlib.sha256(OUTPUT.read_bytes()).hexdigest()
OUTPUT.with_suffix('.zip.sha256').write_text(checksum + '  ' + OUTPUT.name + '\n', encoding='ascii')
print(f'Built {OUTPUT.name}: {len(files)} source files, {OUTPUT.stat().st_size:,} bytes. Contents verified.')
