<?php
class PDF extends FPDF {
    // Current column
    var $col = 0;
    // Ordinate of column start
    var $y0;
    function SetCol($col) {
        // Set position at a given column
        $this->col = $col;
        $x = 10 + $col * 65;
        $this->SetLeftMargin($x);
        $this->SetX($x);
    }
    function AcceptPageBreak() {
        // Method accepting or not automatic page break
        if ($this->col < 2) {
            // Go to next column
            $this->SetCol($this->col + 1);
            // Set ordinate to top
            $this->SetY($this->y0);
            // Keep on page
            return false;
        }
        else {
            // Go back to first column
            $this->SetCol(0);
            // Page break
            return true;
        }
    }
    function Title($label) {
        // Title
        $this->SetFont('Arial', '', 12);
        $this->SetFillColor(200, 220, 255);
        $this->Cell(0, 6, "$label", 0, 1, 'L', true);
        $this->Ln(4);
        // Save ordinate
        $this->y0 = $this->GetY();
    }
    function Body($text) {
        foreach (explode("</brnewline>", $text) as $txt) {
            foreach (explode("<br>", $txt) as $content) {
                $this->MultiCell(60, 5, $content);
                $this->Ln(1);
            }
            $this->Ln(5);
        }
        $this->SetCol(0);
    }
    function Chapter($title, $file) {
        // Add chapter
        $this->AddPage();
        $this->Title($title);
        $this->Body($file);
    }
    function BodyLine($text) {
        foreach (explode("<br>", $text) as $content) {
            $this->MultiCell(60, 5, $content);
            $this->Ln(1);
        }
        $this->Ln(3);
    }
    function PrintLine($title, $file) {
        // Add chapter
        $this->AddPage();
        $this->Title($title);
        $this->BodyLine($file);
    }
}
?>