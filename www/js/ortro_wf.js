/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Javascript functions required by Ortro
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category  Javascript
 * @package   Ortro
 * @author    Luca Corbo <lucor@ortro.net>
 * @link      http://www.ortro.net
 */

//Create jquery tabs
$(function() {
    $("#tabs").tabs();
});

//Create jquery accordion
$(function() {
    $("#wf_graph").accordion({
        collapsible: true
    });
});

//Update the workflow chart using Ajax request
function update_wf_graph() {
    //alert('update graph');
    var id_wf   = $('#id_wf').val();
    var id_node = $('#id_wf_node').val();

    $.getJSON("index.php",
    {
        id_workflow: id_wf,
        id_node: id_node,
        action: "update_wf_graph",
        cat: "workflows",
        ajax: true
    },
    function(data){
        $('#wf_svg').attr('data', './' + id_wf + '.svg');
    }
    );
    return true;
}

function update_wf_box(id_node) {
    
    if (id_node == undefined) {
        id_node = $('#id_wf_node').val();
    } else {
        $('#id_wf_node').val(id_node);
    }

    var id_wf   = $('#id_wf').val();
    
    $.getJSON("index.php",
    {
        id_workflow: id_wf,
        id_node: id_node,
        action: "node_info",
        cat: "workflows",
        ajax: true
    },
    function(data){
        $('#wf_exec_properties_current').val(data.exec_properties);
        $('#wf_exec_condition_current').val(data.exec_condition);
        $('#wf_current_node_label span').text(data.label);
        if(data.id_parent_node == '0') {
            $('#wf_current_edit').hide();
        } else {
            $('#wf_current_edit').show();
        }
    }
    );


    update_wf_graph();
    return true;
}

function add_node() {
    if (validate_frm_wf_node(document.frm_wf_node)) {
        $.ajax({
            type: "POST",
            url: "index.php",
            data: $('#frm_wf_node').serialize(),
            success: function(data){
                //alert(data);
                alert("Data Saved");
            }
        });
    };
    return true;
}

function edit_node() {
    var id_wf   = $('#id_wf').val();
    var id_node = $('#id_wf_node').val();
    
    $.getJSON("index.php",
    {
        id_workflow: id_wf,
        id_node: id_node,
        action: "edit_node",
        cat: "workflows",
        exec_properties: $('#wf_exec_properties_current').val(),
        exec_condition: $('#wf_exec_condition_current').val(),
        ajax: true
    },
    function(data){
        alert(data.msg);
    }

    );
    return true;
}


function delete_node() {
    var id_wf   = $('#id_wf').val();
    var id_node = $('#id_wf_node').val();

    $.getJSON("index.php",
           {id_workflow: id_wf,
            id_node: id_node,
            action: "delete_node",
            cat: "workflows",
            ajax: true
           },
           function(data){
                alert(data.msg);
           }
         );
    return true;
}

function update_wf_properties() {
    if (validate_frm(document.frm)) {
        $.ajax({
            type: "POST",
            url: "index.php",
            data: $('#frm').serialize(),
            success: function(){
                alert("Data Saved");
            }
        });
    };
    return true;
}

//Update the workflow chart and box onload
$(document).ready(function(){
    update_wf_box();
    $("#add_node").click(function(event){
        add_node();
        update_wf_graph();
        });
    $("#edit_node").click(function(event){
        edit_node();
        update_wf_graph();
        });
    $("#delete_node").click(function(event){
        delete_node()
        update_wf_graph()
        });
    $("#update_properties").click(function(event){
        update_wf_properties()
        });
});