<?php

namespace App\Services\Cv;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextBreak;
use PhpOffice\PhpWord\Element\Table;
use RuntimeException;
use Throwable;

class DocxToText
{
    public function convert(string $docxPath): string
    {
        // Check if the file exists
        if (!file_exists($docxPath)) {
            return '';
        }

        try {
            // Load the DOCX file
            $phpWord = IOFactory::load($docxPath);
        } catch (Throwable $e) {
            throw new RuntimeException(
                'Failed to load DOCX file: ' . $e->getMessage(),
                0,
                $e
            );
        }

        $text = '';

        // Iterate through sections and elements to extract text
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $text .= $this->extractElementText($element);
            }
        }

        return trim($text);
    }

    /**
     * Recursively extracts text from a PhpWord element.
     *
     * @param object $element The PhpWord element to extract text from.
     * @return string The extracted text.
     */
    private function extractElementText(object $element): string
    {
        // Check for line breaks
        if ($element instanceof TextBreak) {
            return "\n";
        }

        // Check for text elements
        if ($element instanceof Text) {
            return $element->getText() . "\n";
        }

        // Checks if element is a Table and extracts text from its cells
        if ($element instanceof Table) {
            $text = '';

            // Iterate through rows and cells to extract text
            foreach ($element->getRows() as $row) {
                // Iterate through cells in the row
                foreach ($row->getCells() as $cell) {
                    // Iterate through elements in the cell and extract text
                    foreach ($cell->getElements() as $cellElement) {
                        $text .= $this->extractElementText($cellElement);
                    }
                }

                $text .= "\n";
            }

            return $text;
        }

        // Check if the element has child elements
        if (method_exists($element, 'getElements')) {
            $text = '';

            // Recursively extract text from child elements
            foreach ($element->getElements() as $child) {
                $text .= $this->extractElementText($child);
            }

            return $text;
        }

        return '';
    }
}
