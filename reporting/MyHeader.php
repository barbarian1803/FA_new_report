<?php

include_once __DIR__."/../../..".'/reporting/includes/HeaderBase.php';
include_once __DIR__."/../../.."."includes/date_functions.inc";
class MyHeader extends HeaderBase {

    
    
    function drawHeader(){
        $rep = $this->rep;
        
        $topBorder = $rep->pageHeight-$rep->topMargin;
        $bottomBorder = $rep->bottomMargin;
        $leftBorder = $rep->leftMargin;
        $rightBorder = $rep->pageWidth-$rep->rightMargin;
        $docWidth = $rep->pageWidth-($rep->leftMargin+$rep->rightMargin);
        
        //$rep->SetMargins($rep->leftMargin, $rep->topMargin, $rep->rightMargin);
        //$rep->SetAutoPageBreak(true, $rep->bottomMargin);
        
        
        $aux_info = array(
                    _("Customer's Reference") => $rep->formData["customer_ref"],
                    _("Sales Person") => get_salesman_name($rep->formData['salesman']),
                    _("Your VAT no.") => $rep->formData['tax_id'],
                    _("Our Order No") => $rep->formData['order_no'],
                    _("Delivery Date") => sql2date($rep->formData['delivery_date']),
                );
        
        $rep->SetCellPadding(0);
        
        $Addr1 = array(
            'title' => _("Charge To"),
            'name' => @$rep->formData['br_name'] ? $rep->formData['br_name'] : @$rep->formData['DebtorName'],
            'address' => @$rep->formData['br_address'] ? $rep->formData['br_address'] : @$rep->formData['address']
        );
        $Addr2 = array(
            'title' => _("Delivered To"),
            'name' => @$rep->formData['deliver_to'],
            'address' => @$rep->formData['delivery_address']
        );
        
        $rep->SetAbsXY($leftBorder, $rep->topMargin);
        
        $logo = company_path() . "/images/" . $rep->company['coy_logo'];
        
//        ob_start();
//        var_dump($rep->formData);
//        $result = ob_get_clean();
        
        $size = getimagesize($logo);
        $w = 0.8*$size[0];
        $h = 0.8*$size[1];
        $rep->Image($logo,$leftBorder, $rep->topMargin-20, $w, $h);
        
        $html1 = '
        <table border="0" width="100%" cellpadding="3">
        <tr>
            <td align="right"><h1 style="font-size: 18px; color: grey">'.$rep->title.'</h1></td>
        </tr>
        </table>
        ';
        
        $html2 = '
        <br><br>
        <style>
        td {font-size: 10px;}
        </style>
        <table border="0" width="100%" cellpadding="3">
        <tr>
            <td width="15%">Email</td>
            <td width="2%">:</td>
            <td width="33%">'.$rep->company["email"].'</td>
            <td width="15%">Date</td>
            <td width="2%">:</td>
            <td width="33%">'.sql2date($rep->formData['ord_date']).'</td>
        </tr>
        <tr>
            <td>Our VAT No.</td>
            <td>:</td>
            <td>'.$rep->company["gst_no"].'</td>
            <td>Order No</td>
            <td>:</td>
            <td>'.$rep->formData['order_no'].'</td>
        </tr>
        </table>
        ';
        
        $html3 ='
        <br><br>
        <style>
        td {font-size: 10px;}
        </style>
        <table border="0" width="100%" cellpadding="3">
        <tr>
            <td><b>Charge to</b></td>
            <td><b>Delivered to</b></td>
        </tr>
        <tr>
            <td>'.$Addr1['name'].'<br>'.$Addr1['address'].'</td>
            <td>'.$Addr2['name'].'<br>'.$Addr2['address'].'</td>
        </tr>
        </table>
        ';
        
        $html4 ='
        <br><br>
        <style>
        th {font-size: 9px; text-align: center; background-color: lightgrey; font-weight: bold;}
        td {font-size: 9px; text-align: center;}
        </style>
        <table border="1" width="100%" cellpadding="3">
        ';
        
        $html4 .= "<tr>";
        foreach($aux_info as $k=>$v){
            $html4 .= '<th>'.$k.'</th>';
        }
        $html4 .= "</tr>";
        $html4 .= "<tr>";
        foreach($aux_info as $k=>$v){
            $html4 .= '<td>'.$v.'</td>';
        }
        $html4 .= "</tr>";
        $html4 .= "</table>";
        
        $rep->writeHTML($html1, false, false, false, 'C');
        $rep->writeHTML($html2, false, false, false, 'C');
        $rep->writeHTML($html3, false, false, false, 'C');
        $rep->writeHTML($html4, false, false, false, 'C');
        
    }
    
    
}
