    jQuery(document).ready(function(){
        if(jQuery('.wpscst-table').length != 0) {
            try {
                if(jQuery('#wpscst_nic_panel').length > 0) {
                    var myNicEditor = new nicEditor({buttonList : ['fontSize','bold','italic','underline','strikethrough','ul', 'subscript','superscript','image','link','unlink'], iconsPath:wpscstScriptParams.wpscstPluginsUrl + "/wpsc-support-tickets/js/nicedit/nicEditorIcons.gif"});
                    myNicEditor.setPanel("wpscst_nic_panel");
                    myNicEditor.addInstance("wpscst_initial_message");
                }
                if(jQuery('#wpscst_nic_panel2').length > 0) {
                    var myNicEditor2 = new nicEditor({buttonList : ['fontSize','bold','italic','underline','strikethrough','ul', 'subscript','superscript','image','link','unlink'], iconsPath:wpscstScriptParams.wpscstPluginsUrl + "/wpsc-support-tickets/js/nicedit/nicEditorIcons.gif"});
                    myNicEditor2.setPanel("wpscst_nic_panel2");
                    myNicEditor2.addInstance("wpscst_reply");
                }
            } catch(err) {
                
            }                
            jQuery(".wpscst-table").toggle();
            jQuery("#wpscst_edit_ticket").toggle();

        }
    });

            

    function loadTicket(primkey, resolution) {
        if(jQuery('.wpscst-table').length != 0) {
            jQuery(".wpscst-table").fadeOut("fast");
            jQuery("#wpscst_edit_div").fadeOut("fast");
            jQuery("#wpscst-new").fadeOut("fast");
            jQuery("#wpscst_edit_ticket").fadeIn("fast");
            jQuery("#wpscst_edit_ticket_inner").load(wpscstScriptParams.wpscstAjaxUrl, {"primkey":primkey, "action": "wpsct_save_issue"});
            jQuery("#wpscst_edit_primkey").val(primkey);
            jQuery("html, body").animate({scrollTop: jQuery("html").offset().top}, 100);
            if(resolution=="Closed") {
                jQuery("#wpscst_reply_editor_table_tr1").fadeOut("fast");
                jQuery("#wpscst_submit2").fadeOut("fast");
            }
            if(resolution=="Reopenable") {
                jQuery("#wpscst_reply_editor_table_tr1").fadeOut("fast");
                jQuery("#wpscst_set_status").val('Closed');
            }  
            if(resolution=="Open") {
                try {
                    jQuery("#wpscst_set_status").val('Open');
                } catch (e) {
                    
                }
            }
            try {
                jQuery("#wpscst-search-form").fadeOut("fast");
            } catch (e) {
                    
            }
            try {
                jQuery("#wpscst_search_results").fadeOut("fast");
            } catch (e) {
                    
            }            
        }
    }

    function cancelEdit() {
        if(jQuery('.wpscst-table').length != 0) {        
            jQuery("#wpscst_reply_editor_table_tr1").fadeIn("fast");
            jQuery("#wpscst_submit2").fadeIn("fast");
            jQuery("#wpscst_edit_div").fadeIn("fast");
            jQuery("#wpscst-new").fadeIn("fast");
            jQuery("#wpscst_edit_ticket").fadeOut("fast");
            jQuery("#wpscst_edit_primkey").val(0);
            jQuery("#wpscst_reply").html("");
            jQuery(".nicEdit-main").html("");
            jQuery("#wpscst_edit_ticket_inner").html('<center><img src="' + wpscstScriptParams.wpscstPluginsUrl + '/wpsc-support-tickets/images/loading.gif" alt="..." /></center>');
            jQuery("html, body").animate({scrollTop: jQuery("html").offset().top}, 100);
            try {
                jQuery("#wpscst-search-form").fadeIn("fast");
            } catch (e) {
                    
            }
            try {
                jQuery("#wpscst_search_results").fadeIn("fast");
            } catch (e) {
                    
            }             
        }
    }
           

    function cancelAdd() {
        if(jQuery('.wpscst-table').length != 0) {
            jQuery("#wpscst_edit_div").fadeIn("fast");
            jQuery("#wpscst-new").fadeIn("fast");
            jQuery(".wpscst-table").fadeOut("fast");
            jQuery("html, body").animate({scrollTop: jQuery("html").offset().top}, 100);
            try {
                jQuery("#wpscst-search-form").fadeIn("fast");
            } catch (e) {
                    
            }
            try {
                jQuery("#wpscst_search_results").fadeIn("fast");
            } catch (e) {
                    
            }              
        }
    }