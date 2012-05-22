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

var toolbar;
var toolbar_menu_y;
var toolbar_lock;
window.onload=init;
function init(){
	if (document.frm != undefined){
		if (document.frm.cat.value != 'install'){
			var prefix = '';
			if (document.frm.cat.value == 'jobs') {
				prefix = 'plugin_';
			}
			if (document.frm.cat.value == 'notify') {
				prefix = 'notify_';
			}
			if (document.frm.cat.value == 'workflows') {
				prefix = 'plugin_';
			}
			
			switch_mode = document.frm.mode.value;
			if (switch_mode == 'edit' && document.frm.cat.value == 'workflows') {
				//enable the toolbar in the edit mode for the task category
				switch_mode == 'view';
			}
			
			switch(switch_mode) {
				case 'add':
					if (document.frm.cat.value != 'autodiscovery' && document.frm.id_job_type.value != 0) {
						showFormFields(document.frm.id_job_type.value, prefix, document.frm.id_job_type.value);
					}
				break;
				case 'view':
				case 'details':
					checkRoles();
			    	toolbar_menu = new getObj('toolbar_menu');	
			    	toolbar = new getObj('toolbar');
			        toolbar_menu_box = new getObj('menu-toolbar-box');
			   		toolbar_menu_y = parseInt(findPos(toolbar_menu_box.obj)[1]);
					if (getCookie('ortro_toolbar')=='' || getCookie('ortro_toolbar')!='locked'){
			        	setCookie('ortro_toolbar','unlocked',365);
			        	toolbar_lock=true;
			        } else {
			        	blockToolbar();
			        }
			       	moveToolbar();
				break;
				default:
					if (prefix != '') {
						showFormFields(1, prefix, '1');
						enableSchedule(document.frm.schedule_type.value);
					}
				break;
			}
		}
	}
}

function setCookie(sName, sValue, iDay) {
  var dtNow = new Date();
  var dtExpires = new Date();
  dtExpires.setTime(dtNow.getTime() + 24 * iDay * 3600000);
  document.cookie = sName + "=" + escape(sValue) + "; expires=" + dtExpires.toGMTString();
}

function getCookie(sName) {
  var asCookies = document.cookie.split("; ");
  for (var iCnt = 0; iCnt < asCookies.length; iCnt++) {
    var asCookie = asCookies[iCnt].split("=");
    if (sName == asCookie[0]) { 
      return (unescape(asCookie[1]));
    }
  }
  return("");
}

// remove the cookie
function delCookie(sName) {
  setCookie(sName, "");
}

/* Start Toolbar scripts */

function blockToolbar(){
	if (document['toolbar_img'].src.indexOf("toolbar-locked.png")>0){
		document['toolbar_img'].src='img/toolbar-unlocked.png';
		toolbar_lock=true;
		setCookie('ortro_toolbar','unlocked',365);
	} else {
		document['toolbar_img'].src='img/toolbar-locked.png';
		toolbar_lock=false;
		setCookie('ortro_toolbar','locked',365);
	}
	
}

function moveToolbar() {
	refreshTime=1000;
	if (getCookie('ortro_toolbar')=='unlocked'){
		toolbar_lock=true;
		if (window.innerHeight) {
	    	pos = window.pageYOffset;
	    } else if (document.documentElement && document.documentElement.scrollTop) {
	        pos = document.documentElement.scrollTop;
	    } else if (document.body) {
	        pos = document.body.scrollTop;
	    }
	    if (pos > toolbar_menu_y && toolbar_lock) {
	   		toolbar_menu.style.top = eval('"' + pos + 'px"');
	   		toolbar_menu.style.display = 'block';
	    } else {
	    	toolbar_menu.style.display = 'none';
	    }
    }
    temp = setTimeout('moveToolbar()',refreshTime);
}

function getObj(name) {
  if (document.getElementById) {
        this.obj = document.getElementById(name);
        this.style = document.getElementById(name).style;
  } else if (document.all) {
        this.obj = document.all[name];
        this.style = document.all[name].style;
  } else if (document.layers) {
        this.obj = document.layers[name];
        this.style = document.layers[name];
  }
}

function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}
	}
	return [curleft,curtop];
//	return curtop;
}

/* End Toolbar scripts */

//Jobs view
//Get the refresh time in seconds if setted
function getRefreshTime(){
	refresh_time = document.frm.refresh.value;
	if (refresh_time != 'none'){
		setTimeout("refreshPage()",refresh_time * 1000);
	}
	init();
}

//Page refresh
function refreshPage(){
	document.frm.submit();
	return false;
}

function highlightRow(row){
	row.oldClassName = row.className;
    row.className = 'highlight';
    row.onmouseout = function() {
        this.className = this.oldClassName;
    }

	row.onmousedown = function() {
		mycel = row.getElementsByTagName("td")[0];
		myinput = mycel.getElementsByTagName("input")[0];
		if (myinput) {
		    myinput.checked =! myinput.checked;
		}

        if ( myinput ) {
            myinput.onclick = function() {
                this.checked = ! this.checked;
            }
        }
        
        if ( myinput.checked ) {
            row.oldClassName = row.oldClassName.replace( '_marked', '' );
            row.oldClassName += '_marked';
        } else {
            row.oldClassName = row.oldClassName.replace( '_marked', '' );
        }
        
        checkRoles();
    }

}

function checkAll(isChecked){
	var chks = document.frm.id_chk;
	if (chks.length == undefined) {
		// only a check box create an array
		el = [ chks ];
	} else {
		el = chks;
	}
	for (var i = 0; i < el.length; i++) {
		el[i].checked = isChecked;
	}
	checkRoles();
}

function submitForm(mode){
    var	exit_code = 0;
	testChecked = false;
	
    switch(mode) {
		case 'forwardPage':
			history.go(1);
			return false;
			break;
		case 'backPage':
			history.go(-1);
			return false;
			break;
		case 'forward':
		case 'back':
		case 'reload_page':
		case 'add':
			//Do nothing
			break;
		default:
			testChecked = true;
		break;
	}
	
	if (document.frm.mode.value == 'details') {
		//No check is needed: skip it!
		testChecked = false;
		checked = 1;
		document.frm.action.value = '';
	}
	
    if (testChecked) {
    	//check for checkbox selection
	    var chks = document.frm.id_chk;
	    if (chks.length == undefined) {
			// only a check box create an array
			el = [ chks ];
		} else {
			el = chks;
		}
	
		var checked = 0;
		for (var i = 0; i < el.length; i++) {
			if (el[i].checked) {
				checked++;
				checked_id = i;
			}
		}
		if (checked == 0 && document.frm.mode.value != 'details') {
			alert(JS_MSG_SELECT_A_FIELD);
			return false;
		}
	}
	
	switch(mode) {
		case 'edit':
		case 'details':		    
			if (checked == 1) {
				if (document.frm.cat.value == 'notify'){
					document.frm.system_name.value = el[checked_id].getAttribute("system_name");
					document.frm.notify_label.value = el[checked_id].getAttribute("notify_label");
					document.frm.job_label.value = el[checked_id].getAttribute("job_label");					
				}
				if (document.frm.cat.value == 'user' && el[checked_id].value == "ldap"){
					alert(JS_MSG_CANNOT_EDIT_LDAP_USER);
					return false;
					break;
				}
				if (document.frm.cat.value == 'user' && mode == "userGroup"){
					mode = "edit";
				}
				document.frm.mode.value = mode;
				document.frm.submit();
				return false;
			} else {
				alert(JS_MSG_SELECT_ONLY_A_FIELD);
			}
	    break;
	    case 'userGroup':
	    	if (checked == 1) {
				document.frm.mode.value = "edit";
				document.frm.cat.value = mode;
				document.frm.submit();
				return false;
			} else {
				alert(JS_MSG_SELECT_ONLY_A_FIELD);
			}
	    break;
   		case 'delete':
		   		if(confirm(JS_MSG_CONFIRM_DELETE)){
					document.frm.action.value = mode;
					document.frm.submit();
					return false;
				}
   		break;
   		case 'kill':
                if(confirm(JS_MSG_CONFIRM_KILL)){
                    document.frm.action.value = mode;
                    document.frm.submit();
                    return false;
                }
        break;
   		case 'add':
	   		document.frm.mode.value = "add";
	   		if (document.frm.cat.value != 'autodiscovery') {
   			    document.frm.action.value = '';
   			} else {
   			    try { 
   			      var myValidator = validate_frm; 
   			    } catch(e) { 
   			      return false; 
   			    }
   			    if(!myValidator(document.frm)){
   			      return false;
   			    }
   			}
			document.frm.submit();
			return false;
   		break;
	  	case 'copy':
	    case 'run':
	    case 'lock':
   	    case 'unlock':
   	    case 'forward':
   	    case 'back':
			document.frm.action.value = mode;
			document.frm.submit();
			return false;
	    break;
	    default:
		    document.frm.submit();
		    return false;
	    break;
    }
    return true;
}

function enableSchedule(select_id){
	if (select_id != 'D') {
		//enable schedule form fields
		document.frm.crontab_m.disabled = false;
		document.frm.crontab_h.disabled = false;
		document.frm.crontab_dom.disabled = false;
		document.frm.crontab_mon.disabled = false;
		document.frm.crontab_dow.disabled = false;
		document.frm.calendar_id.disabled = false;
	} else {
		//disable schedule form fields		
		document.frm.crontab_m[0].selected = 1;
		showFormFields('0', 'minute_', 'custom');
		document.frm.crontab_m.disabled = true;
		document.frm.crontab_h[0].selected = 1;
		showFormFields('0', 'hour_', 'custom');
		document.frm.crontab_h.disabled = true;
		document.frm.crontab_dom[0].selected = 1;
		showFormFields('0', 'day_', 'custom');
		document.frm.crontab_dom.disabled = true;
		document.frm.crontab_mon[0].selected = 1;
		showFormFields('0', 'month_', 'custom');
		document.frm.crontab_mon.disabled = true;
		document.frm.crontab_dow[0].selected = 1;
		showFormFields('0', 'dayweek_', 'custom');
		document.frm.crontab_dow.disabled = true;
        document.frm.calendar_id.disabled = true;
        document.frm.calendar_id.selectedIndex = 0;
	}
}

/***************************************
 * Allow to show and hide the form fields
 ***************************************/
function showFormFields(select_id, prefix_id, suffix_id){
	if (isNaN(suffix_id)){
		counter = '1';
	} else {
		counter = suffix_id;
	}
 	for (var index = 0; index <= counter; index++) {
 		if (isNaN(suffix_id)){
			id = document.getElementById(prefix_id + suffix_id);
			forTest = suffix_id;
		} else {
			id = document.getElementById(prefix_id + index);
			forTest = index;
		}
		
	    if (id != null){
		    display="";
	        f_input = eval(id).getElementsByTagName("input");
	        f_textarea = eval(id).getElementsByTagName("textarea");
	       
			if (select_id == forTest) {
	      		for (var i = 0; i < f_input.length; i++) {
					f_input[i].disabled = false;
				}
				for (var i = 0; i < f_textarea.length; i++) {
					f_textarea[i].disabled = false;
					if (f_textarea[i].getAttribute("htmlarea") == "htmlarea"){
						f_textarea[i].setAttribute("htmlarea","created");
						var oFCKeditor = new FCKeditor( f_textarea[i].name ) ;
						oFCKeditor.ToolbarSet = 'Default';
						oFCKeditor.BasePath	= 'js/FCKeditor/';
						oFCKeditor.Config["AutoDetectLanguage"] = false ;
						oFCKeditor.Config["DefaultLanguage"] = JS_LANG ;
						oFCKeditor.ReplaceTextarea() ;
					}
				}
				display="";
	      	} else {
	      		
	      		for (var i = 0; i < f_input.length; i++) {
				f_input[i].disabled = true;
				}
				for (var i = 0; i < f_textarea.length; i++) {
					f_textarea[i].disabled = true;
				}
				display="none";
	      	}
	      	id.style.display=display;
		}
	}
}

function rangeValue(value,extradata){
	range=extradata.split("-");

	if (parseInt(range[0]) <= value && value <= parseInt(range[1])){	
		return true;
	} else {
		return false;
	}
}


function checkHier(value,selectName){
	if (document.getElementsByName(selectName)[0].value == 0){
		return false;
	} else {
		return true;
	}
}

function checkMultiSelectJob(select_value, multiselect_id){
	el = document.getElementById(multiselect_id);
	if (select_value != 'custom'){
		sel = true;
	} else {
		sel = false;
		for (var i = 0; i < el.length; i++) {
			if (el[i].selected){
				sel = true;
				break;
			}
		}
	}
	return sel;
}

function checkMultiSelect(select_value, multiselect_id){
    el = document.getElementById(multiselect_id);
    if (el.disabled){
        sel = true;
    } else {
        sel = false;
        for (var i = 0; i < el.length; i++) {
            if (el[i].selected){
                sel = true;
                break;
            }
        }
    }
    return sel;
}

function checkMultiSelectNormal(multiselect_id,message){
	el = document.getElementById(multiselect_id);
	sel = false;
	for (var i = 0; i < el.length; i++) {
		if (el[i].selected){
			sel = true;
			break;
		}
	}
	if (!sel) {
		alert(message);
	}
	return sel;
}

function openWindow(page,title,w,h) {
	var newWin = window.open(page, title, 'toolbar=no,location=no,status=no,resizable=no,top=100,left=200,width='+w+',height='+h+',scrollbars=yes');
	newWin.opener = self;
	newWin.focus();
}

function identityPicker(field_name) {

	if (document.getElementsByName("cat")[0].value == 'jobs'){
		el = document.getElementsByName("systemHostDb[0]")[0];//job category
	}

	if (document.getElementsByName("cat")[0].value == 'notify'){
		el = document.getElementsByName("systemHost[0]")[0];//job category -> mode add
		if (el == null || el == undefined) {
			el = document.getElementsByName("id_system")[0];//notification category -> mode edit
		}
	}
   
	var id_system = el.value;
	if (id_system == 0){
		//i18n
		alert(JS_MSG_SELECT_A_SYSTEM);
	} else {
		openWindow('index.php?action=identity_picker&cat=identity_management&id_system='+id_system+'&field_name='+field_name,
				   '',300,100);
	}
	
}

function fillIdentity(field){
	el = document.getElementsByName('identity')[0];
	selectedIndex = el.selectedIndex;
	opener.document.getElementsByName(field)[0].value=el.options[selectedIndex].text;
	opener.document.getElementsByName("identity")[0].value=el.options[selectedIndex].value;
	window.close();
}

/************************************************
 * In the add Group section enable/disable systems 
 * choice in according with the selected role
 ************************************************/
function disableMultiSelSystems(select_id){
    el = document.getElementsByName("id_systems[]")[0];
    switch(select_id) {
        case '1':
        case '3':
            el.disabled = true;
        break;
        case '2':
        case '4':
            el.disabled = false;
        break;
    }
    return true;
}