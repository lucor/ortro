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

function ArraySortAscending(a, b)  //Sort array in ascending order
{
	return (a-b);
}

function ArraySortDescending(a, b)  //Sort array in descending order
{
	return (b-a);
}

$(function(){
    // Datepicker
    var multiDateSelect = [];
    var calInitValue = $("#calendar").attr('value');
    if (calInitValue != '') {
        multiDateSelect = (calInitValue.split('#'));
        multiDateSelect.shift();
        multiDateSelect.pop();
        multiDateSelect = jQuery.map(multiDateSelect, function(n, i){
                                                        return (parseInt(n));
                                                      });
    }
    $('#datepicker').datepicker({
        multiDateSelect: multiDateSelect,
        inline: true,
        numberOfMonths: 3,
        showButtonPanel: true,

        onSelect: function(dateText, inst) {
            dateSelected = (new Date(dateText).getTime())/1000;
            var position = $.inArray(dateSelected, multiDateSelect);
            if (position == -1) {
                multiDateSelect.push(dateSelected);
            } else {
                multiDateSelect.splice(position, 1);
            }
            var calValue = '';
            if (multiDateSelect.length > 0) {
                multiDateSelect.sort(ArraySortAscending);
                calValue = '#' + multiDateSelect.join('#') + '#';
            }
            $('#datepicker').datepicker("option", 'multiDateSelect', multiDateSelect);
            $("#calendar").attr("value", calValue);
        }
    });
});