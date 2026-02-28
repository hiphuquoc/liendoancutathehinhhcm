const fs = require('fs');
const path = require('path');

const dir = path.resolve(__dirname, '..');
const filePath = path.join(dir, 'resources', 'sources', 'admin', 'admin-layout-redesign.scss');
const orig = fs.readFileSync(filePath, 'utf8');

// Find all "indent &:hover {" and their matching "indent }"
const openPat = /^(\s+)&:hover\s*\{/gm;
const matches = [];
let m;
while ((m = openPat.exec(orig)) !== null) {
  matches.push({ start: m.index, indent: m[1], openEnd: m.index + m[0].length });
}

function findClose(s, from, indent) {
  let depth = 1;
  let pos = from;
  let lineStart = from;
  const len = s.length;
  while (pos < len && depth > 0) {
    const c = s[pos];
    if (c === '\n') lineStart = pos + 1;
    else if (c === '{') depth++;
    else if (c === '}') {
      if (depth === 1) {
        const lineIndent = ((s.slice(lineStart).match(/^\s*/) || [''])[0] || '');
        if (lineIndent === indent) return { closeStart: lineStart, closeEnd: pos + 1 };
      }
      depth--;
    }
    pos++;
  }
  return null;
}

let out = '';
let last = 0;
for (const { start, indent, openEnd } of matches) {
  const close = findClose(orig, openEnd, indent);
  if (!close) continue;
  out += orig.slice(last, start);
  out += indent + '@include hover-only {\n' + indent + '    &:hover {';
  out += orig.slice(openEnd, close.closeStart);
  out += indent + '    }\n' + indent + '}';
  last = close.closeEnd;
}
out += orig.slice(last);
fs.writeFileSync(filePath, out, 'utf8');
console.log('Wrapped', matches.length, 'hover blocks');
