const fs = require('fs');
const code = fs.readFileSync('C:\\xampp\\htdocs\\PROTOTIPO\\public\\js\\main.js', 'utf8');
let braceCount = 0;
let parenCount = 0;
let bracketCount = 0;
let inString = false;
let stringChar = '';
let escaped = false;

for (let i = 0; i < code.length; i++) {
    const char = code[i];
    
    if (!inString) {
        if (char === '{') braceCount++;
        else if (char === '}') braceCount--;
        else if (char === '(') parenCount++;
        else if (char === ')') parenCount--;
        else if (char === '[') bracketCount++;
        else if (char === ']') bracketCount--;
        else if (char === '"' || char === "'" || char === '`') {
            inString = true;
            stringChar = char;
        }
    } else {
        if (escaped) {
            escaped = false;
        } else if (char === '\\') {
            escaped = true;
        } else if (char === stringChar) {
            inString = false;
            stringChar = '';
        }
    }
    
    if (braceCount < 0) {
        console.log('Extra closing brace at position', i);
        console.log('Context:', code.substring(Math.max(0, i-50), i+50));
        break;
    }
    if (parenCount < 0) {
        console.log('Extra closing paren at position', i);
        console.log('Context:', code.substring(Math.max(0, i-50), i+50));
        break;
    }
    if (bracketCount < 0) {
        console.log('Extra closing bracket at position', i);
        console.log('Context:', code.substring(Math.max(0, i-50), i+50));
        break;
    }
}

console.log('Final counts - braces:', braceCount, 'parens:', parenCount, 'brackets:', bracketCount);