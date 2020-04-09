/*

Project     : DAdmin - Responsive Bootstrap HTML Admin Dashboard
Version     : 1.1
Author      : ThemeLooks
Author URI  : https://themeforest.net/user/themelooks

*/

(function ($) {
    "use strict";
    
    /* ------------------------------------------------------------------------- *
     * COMMON VARIABLES
     * ------------------------------------------------------------------------- */
    var $wn = $(window),
        $document = $(document),
        $body = $('body');

    $(window).on('load',function() {
	    $(".se-pre-con").fadeOut("slow");
    });
    
    $(function () {
    
         if($('.select2').length){
            var $selectmenu = $('[data-trigger="selectmenu"]');
    
            if ( $selectmenu.length ) {
                $selectmenu.select2();
            }
        
            $('.select2').select2({
                theme: "classic"
            });
        }
    
        var $formWizard = $('#formWizard');

        if ( $formWizard.length ) {
            $formWizard.validate({
                errorPlacement: function (error, element) {
                    if(element.hasClass('select2') && element.next('.select2-container').length) {
                        error.insertAfter(element.next('.select2-container'));
                    }else{
                        var elem = $(element);
                        error.insertAfter(element);
                    }
                    var error_element = $(error[0]).attr('id');
                    $('#'+error_element).html('');
                },
            });

            $('.select2').on('change',function(){
                var select_element_with_id = $(this).attr('id');
                $('#'+select_element_with_id).val($(this).val());
                formWizardcontainer.element(this);
            });

            $formWizard.steps({
                headerTag: 'h3',
                bodyTag: 'section',
                titleTemplate: '<span class="number">#index#</span> #title#',
                onStepChanging: function () {
                    $formWizard.validate().settings.ignore = ':disabled,:hidden';
                    return $formWizard.valid();
                },
                onFinishing: function () {
                    $formWizard.validate().settings.ignore = ":disabled";
                    return $formWizard.valid();
                }
            });
        }
        
        /* ------------------------------------------------------------------------- *
         * RECORDS LIST
         * ------------------------------------------------------------------------- */
        var $recordsList = $('.records--list'),
            $recordsListView = $('#recordsListView');

        if ( $recordsListView.length ) {
            $recordsListView.DataTable({
                responsive: true,
                language: {
                    "lengthMenu": "View _MENU_ records"
                },
                dom: '<"topbar"<"toolbar"><"right"li>>f<"table-responsive"t>p',
                order: [],
                columnDefs: [
                    {
                        targets: 'not-sortable',
                        orderable: false
                    }
                ]
            });
            $recordsList.find('.toolbar').text( $recordsList.data('title') );
        }


        /* ------------------------------------------------------------------------- *
         * SIDEBAR NAVIGATION
         * ------------------------------------------------------------------------- */
        var $sidebarNav = $('.sidebar--nav');

        $.each( $sidebarNav.find('li'), function () {
            var $li = $(this);

            if ( $li.children('a').length && $li.children('ul').length ) {
                $li.addClass('is-dropdown');
            }
        });

        $sidebarNav.on('click', '.is-dropdown > a', function (e) {
            e.preventDefault();

            var $el = $(this),
                $es = $el.siblings('ul'),
                $ep = $el.parent(),
                $ps = $ep.siblings('.open');

            if ( $ep.parent().parent('.sidebar--nav').length ) {
                $es.slideToggle();
                $ep.toggleClass('open');

                return;
            }

            $es.add( $ps.children('ul') ).slideToggle();
            $ep.add( $ps ).toggleClass('open');
        });

        /* ------------------------------------------------------------------------- *
         * TOGGLE SIDEBAR
         * ------------------------------------------------------------------------- */
        var $toggleSidebar = $('[data-toggle="sidebar"]');

        $toggleSidebar.on('click', function (e) {
            e.preventDefault();

            $body.toggleClass('sidebar-mini');
        });

            
        $('#purchase_quantity').on('keyup',function(){
            var quantity = $(this).val();
            var purchase_rate = $('#purchase_net_purchase_rate').val();
            calculate_total(quantity,purchase_rate);
        });

        $('#purchase_net_purchase_rate').on('keyup',function(){
            var quantity = $('#purchase_quantity').val();
            var purchase_rate = $(this).val();
            calculate_total(quantity,purchase_rate);
        });

        $('#purchase_markup').on('keyup',function(){
            var unit_price = $('#purchase_per_kg_piece').val();
            var markup = $(this).val();
            sales_price(unit_price,markup);
        });

    });

    function calculate_total(quantity,purchase_rate){
        var per_kg = 0;
        if(purchase_rate>0 && quantity>0){
            var per_kg = parseFloat(purchase_rate)/parseFloat(quantity);
        }
        $('#purchase_per_kg_piece,#purchase_sales_price').val(to_decimal(per_kg));
        if($('#purchase_markup').val()>0){
            var unit_price = $('#purchase_per_kg_piece').val();
            var markup = $('#purchase_markup').val();
            sales_price(unit_price,markup);
        }
    }

    function sales_price(unit_price,markup){
        var percentage_amount = (parseFloat(unit_price)/100)*parseFloat(markup);
        var total = parseFloat(unit_price)+parseFloat(percentage_amount);
        $('#purchase_sales_price').val(to_decimal(total));
    }

    function to_decimal(number){
        return number.toFixed(2)
    }



}(jQuery));
