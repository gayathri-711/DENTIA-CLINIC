const fs = require('fs');
const path = require('path');

const srcDir = path.join(__dirname, 'Dentia', 'Dentia HTML');
const destDir = path.join(__dirname, 'public');

console.log('[BUILD] Preparing public/ directory for Vercel deployment...');

function copyRecursive(src, dest) {
  if (!fs.existsSync(src)) return;
  const stat = fs.statSync(src);
  if (stat.isDirectory()) {
    if (!fs.existsSync(dest)) fs.mkdirSync(dest, { recursive: true });
    for (const item of fs.readdirSync(src)) {
      copyRecursive(path.join(src, item), path.join(dest, item));
    }
  } else {
    fs.copyFileSync(src, dest);
  }
}

if (fs.existsSync(destDir)) {
  fs.rmSync(destDir, { recursive: true, force: true });
}
fs.mkdirSync(destDir, { recursive: true });

copyRecursive(srcDir, destDir);
console.log('[BUILD SUCCESS] Static assets copied to public/ successfully!');
