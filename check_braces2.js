const fs = require('fs');
const code = fs.readFileSync('C:\\xampp\\htdocs\\PROTOTIPO\\public\\js\\main.js', 'utf8');
let braceCount = 0;
let line = 1;
let inString = false;
let stringChar = '';
let escaped = false;
let lastBraceLine = 0;

for (let i = 0; i < code.length; i++) {
    const char = code[i];
    
    if (char === '\n') {
        line++;
    }
    
    if (!inString) {
        if (char === '"' || char === "'" || char === '`') {
            inString = true;
            stringChar = char;
        } else if (char === '{') {
            braceCount++;
        } else if (char === '}') {
            braceCount--;
            if (braceCount < 0) {
                console.log('Extra closing brace at position', i, 'line', line);
            }
        }
    } else {
        if (escaped) {
            escaped = false;
        } else if (char === '\\') {
            escaped = true;
        } else if (char === stringChar) {
            inString = false;
        }
    }
    
    if (code[i] === '{' && !inString) {
        // Track opening braces
    }
}

console.log('Final brace count:', braceCount);