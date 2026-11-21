
<?php

use App\Services\Cv\DocxToText;

function createDocx(string $documentXml): string
{
    $base = tempnam(sys_get_temp_dir(), 'cv');

    unlink($base);

    $path = $base . '.docx';

    $zip = new ZipArchive();

    $result = $zip->open($path, ZipArchive::CREATE);

    if ($result !== true) {
        throw new RuntimeException('Could not create DOCX');
    }

    $zip->addFromString(
        '[Content_Types].xml',
        '<?xml version="1.0"?>
        <Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
            <Default Extension="rels"
                ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
            <Default Extension="xml" ContentType="application/xml"/>
            <Override PartName="/word/document.xml"
                ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
        </Types>'
    );

    $zip->addFromString(
        '_rels/.rels',
        '<?xml version="1.0"?>
        <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
            <Relationship Id="rId1"
                Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument"
                Target="word/document.xml"/>
        </Relationships>'
    );

    $zip->addFromString(
        'word/document.xml',
        $documentXml
    );

    $zip->close();

    return $path;
}

function removeDocx(string $path): void
{
    if (file_exists($path)) {
        unlink($path);
    }
}

test('DocxToText extracts paragraph text from a docx file', function () {
    $path = createDocx('
        <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
            <w:body>
                <w:p>
                    <w:r><w:t>Universidad Central de Venezuela</w:t></w:r>
                </w:p>
                <w:p>
                    <w:r><w:t>Ingeniero en Sistemas</w:t></w:r>
                </w:p>
                <w:p>
                    <w:r><w:t>Acme Corp, Desarrollador</w:t></w:r>
                </w:p>
                <w:sectPr/>
            </w:body>
        </w:document>
    ');

    try {
        $text = (new DocxToText)->convert($path);

        expect($text)
            ->toContain('Universidad Central de Venezuela')
            ->toContain('Ingeniero en Sistemas')
            ->toContain('Acme Corp, Desarrollador');
    } finally {
        removeDocx($path);
    }
});

test('DocxToText returns empty string when file does not exist', function () {
    $path = sys_get_temp_dir() . '/non-existent-file.docx';

    expect((new DocxToText)->convert($path))->toBe('');
});

test('DocxToText extracts text from tables', function () {
    $path = createDocx('
        <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
            <w:body>
                <w:tbl>
                    <w:tr>
                        <w:tc>
                            <w:p><w:r><w:t>Empresa</w:t></w:r></w:p>
                        </w:tc>
                        <w:tc>
                            <w:p><w:r><w:t>Cargo</w:t></w:r></w:p>
                        </w:tc>
                    </w:tr>
                    <w:tr>
                        <w:tc>
                            <w:p><w:r><w:t>Acme Corp</w:t></w:r></w:p>
                        </w:tc>
                        <w:tc>
                            <w:p><w:r><w:t>Desarrollador PHP</w:t></w:r></w:p>
                        </w:tc>
                    </w:tr>
                </w:tbl>
                <w:sectPr/>
            </w:body>
        </w:document>
    ');

    try {
        $text = (new DocxToText)->convert($path);

        expect($text)
            ->toContain('Empresa')
            ->toContain('Cargo')
            ->toContain('Acme Corp')
            ->toContain('Desarrollador PHP');
    } finally {
        removeDocx($path);
    }
});

test('DocxToText extracts text with line breaks', function () {
    $path = createDocx('
        <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
            <w:body>
                <w:p>
                    <w:r><w:t>Primera línea</w:t></w:r>
                    <w:r><w:br/></w:r>
                    <w:r><w:t>Segunda línea</w:t></w:r>
                </w:p>
                <w:sectPr/>
            </w:body>
        </w:document>
    ');

    try {
        $text = (new DocxToText)->convert($path);

        expect($text)
            ->toContain('Primera línea')
            ->toContain('Segunda línea');
    } finally {
        removeDocx($path);
    }
});

test('DocxToText returns empty string when document has no text', function () {
    $path = createDocx('
        <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
            <w:body>
                <w:sectPr/>
            </w:body>
        </w:document>
    ');

    try {
        expect((new DocxToText)->convert($path))->toBe('');
    } finally {
        removeDocx($path);
    }
});

test('DocxToText throws an exception when document is invalid', function () {
    $path = tempnam(sys_get_temp_dir(), 'invalid_cv');

    file_put_contents($path, 'This is not a valid DOCX file');

    try {
        expect(fn() => (new DocxToText)->convert($path))
            ->toThrow(\RuntimeException::class);
    } finally {
        removeDocx($path);
    }
});
