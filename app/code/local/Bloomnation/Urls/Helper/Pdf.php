<?php
class Bloomnation_Urls_Helper_Pdf extends Mage_Core_Helper_Abstract
{
    public function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);
        //$drawingString = $string;
        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
        return $stringWidth;
    }

    public function getFontSizeForWidth($text, $font, $desiredSize, $maxWidth)
    {
        do {
            $stringWidth = $this->widthForStringUsingFontSize($text, $font, $desiredSize);
            $desiredSize = $desiredSize - 0.5;
        } while ($stringWidth > $maxWidth);
        return $desiredSize + 0.5;
    }

    public function getWrappedText($string, $max_width, $font = '', $fontsize = 14)
    {
        $font = Zend_Pdf_Font::fontWithName($font);
        $wrappedText = '';
        $nLines = 1;
        $lines = explode("\n", $string);
        foreach ($lines as $line) {
            $words = explode(' ', $line);
            $word_count = count($words);
            $i = 0;
            $wrappedLine = '';
            while ($i < $word_count) {
                /* if adding a new word isn't wider than $max_width,
                we add the word */
                if ($this->widthForStringUsingFontSize($wrappedLine . ' ' . $words[$i]
                    , $font
                    , $fontsize) < $max_width
                ) {
                    if (!empty($wrappedLine)) {
                        $wrappedLine .= ' ';
                    }
                    $wrappedLine .= $words[$i];
                } else {
                    $wrappedText .= $wrappedLine . "\n";
                    $nLines++;
                    $wrappedLine = $words[$i];
                }
                $i++;
            }
            $wrappedText .= $wrappedLine . "\n";
        }
        return array('text'=>$wrappedText,'n'=>$nLines);
    }
}