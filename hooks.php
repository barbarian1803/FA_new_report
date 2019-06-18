<?php

class hooks_new_report_examples extends hooks {

    var $module_name = 'Report examples';

    /*
      Install additonal menu options provided by module
     */

    function install_options($app) {
        global $path_to_root;

        switch ($app->id) {
            case 'orders':
                $app->add_lapp_function(0, _('sales order new report'), $path_to_root . '/modules/new_report_examples/sales_orders_view.php', 'SA_SALESTRANSVIEW');
        }
    }

    function install_access() {
        
    }

}
