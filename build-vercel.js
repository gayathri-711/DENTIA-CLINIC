const fs = require('fs');
const path = require('path');

const srcDir = path.join(__dirname, 'Dentia', 'Dentia HTML');
const destDir = path.join(__dirname, 'public');

console.log('[BUILD] Preparing public/ directory for Vercel deployment...');

function copyRecursive(src, dest) {
  if (!fs.existsSync(src)) return;
  const stat = fs.statSync(src);
  if (stat.isDirectory()) {
    const dirName = path.basename(src).toLowerCase();
    // Exclude backend api directory so Vercel forwards /api/* to the serverless function
    if (dirName === 'api') {
      return;
    }
    if (!fs.existsSync(dest)) fs.mkdirSync(dest, { recursive: true });
    for (const item of fs.readdirSync(src)) {
      copyRecursive(path.join(src, item), path.join(dest, item));
    }
  } else {
    // Exclude server-side PHP scripts from public/ so Vercel doesn't serve them as static files with 405 errors
    if (src.endsWith('.php')) {
      return;
    }
    fs.copyFileSync(src, dest);
  }
}

if (fs.existsSync(destDir)) {
  fs.rmSync(destDir, { recursive: true, force: true });
}
fs.mkdirSync(destDir, { recursive: true });

copyRecursive(srcDir, destDir);

// Safety cleanup: ensure no public/api or public/*.php files remain
const publicApi = path.join(destDir, 'api');
if (fs.existsSync(publicApi)) {
  fs.rmSync(publicApi, { recursive: true, force: true });
}

console.log('[BUILD SUCCESS] Static assets copied to public/ successfully (server-side api/ and *.php files excluded)!');
