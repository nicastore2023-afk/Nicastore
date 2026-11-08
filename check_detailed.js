const fs = require('fs');
const code = fs.readFileSync('C:\\xampp\\htdocs\\PROTOTIPO\\public\\js\\main.js', 'utf8');
let braceCount = 0;
let parenCount = 0;
let bracketCount = 0;
let inString = false;
let stringChar = '';
let escaped = false;
let line = 1;

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
            if (braceCount > 10) {
                console.log('Line', line, ': braceCount =', braceCount);
            }
        } else if (char === '}') {
            braceCount--;
            if (braceCount < 0) {
                console.log('Extra closing brace at line', line, 'pos', i);
            }
        } else if (char === '(') {
            parenCount++;
        } else if (char === ')') {
            parenCount--;
            if (parenCount < 0) {
                console.log('Extra closing paren at line', line, 'pos', i);
            }
        } else if (char === '[') {
            bracketCount++;
        } else if (char === ']') {
            bracketCount--;
            if (bracketCount < 0) {
                console.log('Extra closing bracket at line', line, 'pos', i);
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
    
    if (char === '\n') {
        // Track line changes for brace count
    }
}

console.log('Final counts - braces:', braceCount, 'parens:', parenCount, 'brackets:', bracketCount);