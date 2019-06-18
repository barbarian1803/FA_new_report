<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ? 'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Print Sales Orders
// ----------------------------------------------------------------
$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");
include_once($path_to_root . "/modules/new_report_examples/reporting/MyHeader.php");

print_sales_orders();

function print_sales_orders() {
    global $path_to_root, $SysPrefs;

    include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $currency = $_POST['PARAM_2'];
    $email = $_POST['PARAM_3'];
    $print_as_quote = $_POST['PARAM_4'];
    $comments = $_POST['PARAM_5'];
    $orientation = $_POST['PARAM_6'];

    if (!$from || !$to)
        return;

    $orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();

    

    
    $cur = get_company_Pref('curr_default');

    if ($email == 0) {

        if ($print_as_quote == 0)
            $rep = new FrontReport(_("SALES ORDER"), "SalesOrderBulk", user_pagesize(), 9, $orientation);
        else
            $rep = new FrontReport(_("QUOTE"), "QuoteBulk", user_pagesize(), 9, $orientation);
    }


    for ($i = $from; $i <= $to; $i++) {
        $myrow = get_sales_order_header($i, ST_SALESORDER);
        
        if ($currency != ALL_TEXT && $myrow['curr_code'] != $currency) {
            continue;
        }
        
        $baccount = get_default_bank_account($myrow['curr_code']);
        
        $params['bankaccount'] = $baccount['id'];
        
        $branch = get_branch($myrow["branch_code"]);
        if ($email == 1){
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
        }
        
        $rep->currency = $cur;
        
        if ($print_as_quote == 1) {
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
            if ($print_as_quote == 1) {
                $rep->title = _('QUOTE');
                $rep->filename = "Quote" . $i . ".pdf";
            } else {
                $rep->title = _("SALES ORDER");
                $rep->filename = "SalesOrder" . $i . ".pdf";
            }
        } else {
            $rep->title = ($print_as_quote == 1 ? _("QUOTE") : _("SALES ORDER"));
        }

        $rep->Info($params, array(), null, array());

        $contacts = get_branch_contacts($branch['branch_code'], 'order', $branch['debtor_no'], true);
        $rep->SetCommonData($myrow, $branch, $myrow, $baccount, ST_SALESORDER, $contacts);
        
        $header = new MyHeader($rep);
        
        $rep->SetHeaderType($header);
        
        $rep->NewPage();
        
        
        $result = get_sales_order_details($i, ST_SALESORDER);
        $SubTotal = 0;
        $items = $prices = array();
        while ($myrow2 = db_fetch($result)) {
            $Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]), user_price_dec());
            $prices[] = $Net;
            $items[] = $myrow2['stk_code'];
            $SubTotal += $Net;
            $DisplayPrice = number_format2($myrow2["unit_price"], $dec);
            $DisplayQty = number_format2($myrow2["quantity"], get_qty_dec($myrow2['stk_code']));
            $DisplayNet = number_format2($Net, $dec);
            if ($myrow2["discount_percent"] == 0){
                $DisplayDiscount = "";
            }else{
                $DisplayDiscount = number_format2($myrow2["discount_percent"] * 100, user_percent_dec()) . "%";
            }
            
            if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
                $rep->NewPage();
        }

        $doctype = ST_SALESORDER;

        $tax_items = get_tax_for_items($items, $prices, $myrow["freight_cost"], $myrow['tax_group_id'], $myrow['tax_included'], null);
        $first = true;
        foreach ($tax_items as $tax_item) {
            if ($tax_item['Value'] == 0)
                continue;
            $DisplayTax = number_format2($tax_item['Value'], $dec);

            $tax_type_name = $tax_item['tax_type_name'];

            if ($myrow['tax_included']) {
                if ($SysPrefs->alternative_tax_include_on_docs() == 1) {
                    if ($first) {

                    }
                    $first = false;
                } else {
                    
                }
            } else {
                
                
            }
        }


        $rep->Font();
        if ($email == 1) {
            $rep->End($email);
        }
    }
    if ($email == 0)
        $rep->End();
}
