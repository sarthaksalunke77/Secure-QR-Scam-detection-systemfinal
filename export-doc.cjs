const fs = require('fs');
const marked = require('marked');

const markdownPath = 'C:\\Users\\salun\\.gemini\\antigravity\\brain\\40b2f647-cffb-444e-aefd-11d618bb999e\\blockchain_banking_case_study.md';
const docPath = 'c:\\Users\\salun\\OneDrive\\Desktop\\pro copy\\Blockchain_Banking_Case_Study_Styled.doc';

const mdContent = fs.readFileSync(markdownPath, 'utf8');
const htmlContent = marked.parse(mdContent);

const styledHtml = `
<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Times New Roman', serif;
            font-size: 14pt;
        }
        p, li, td {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
        }
    </style>
</head>
<body>
    ${htmlContent}
</body>
</html>
`;

fs.writeFileSync(docPath, styledHtml, 'utf8');
console.log('Successfully generated DOC file with custom styling.');
