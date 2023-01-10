<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once 'TCPDF/tcpdf.php';

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
    
    //Page header
    public function Header() {
        // Logo
        // $this->SetY(3);
        $this->SetFont('times', '', '8');
        $date = date('d/m/Y');
        $html = <<<EOD
        <table cellspacing="0" cellpadding="1" style="width: 100%;"><tr><td style="text-align: left; font-weight: normal;">DMIS | Dispensary Management Information System </td><td style="text-align: right; font-weight: normal;"> Date: {$date} </td></tr></table>
        EOD; 
        $this->writeHTML($html, true, false, false, false, '');
    }
    
    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('times', '',8);
        
        $html = <<<EOD
        <hr /><table cellspacing="0" cellpadding="1" style="width: 100%;"><tr><td colspan="2" style="text-align: left; font-weight: normal;">Mabibo, Ubungo, P.O Box 705, Tel: +255-22-2400148, +255-22-2400260, Fax: +255-22-2443149, Email: rector@nit.ac.tz, Website: www.nit.ac.tz </td><td style="text-align: right; font-weight: normal;">Page {$this->getAliasNumPage()} of  {$this->getAliasNbPages()}</td></tr></table> 
        EOD;  
        
        $this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    }
}


?>