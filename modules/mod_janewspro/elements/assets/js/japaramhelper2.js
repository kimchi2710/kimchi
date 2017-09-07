/**
 * ------------------------------------------------------------------------
 * JA News Pro Module for Joomla 2.5
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
var requesting = false;
var themerequesting = false;
var jSonRequest = null;
var jSonRequestTheme = null;
var JAPARAM2 = new Class({	
	
	initialize: function(optionid) {
		this.group = 'jaform';
		this.el = $(optionid);
		var ul = $('module-sliders').getElement('ul.adminformlist');
		if(ul!=null){
			var li = new Element('li', {'class':'clearfix level2'});
			li.inject(ul);
		}
		if($('jform_params_groupbysubcat0')!=null){
			var disabled = false;
			if($('jform_params_groupbysubcat0').checked){
				disabled = true;
			}
			/* Set show/hide for sub Joomla Category and sub K2 Category */
			this.hideChildren('jform_params_k2catsid', disabled);
			this.hideChildren('jform_params_catsid', disabled);
			
			
			$('jform_params_groupbysubcat0').addEvent('click', function(){
				this.hideChildren('jform_params_k2catsid', true);
				this.hideChildren('jform_params_catsid', true);
				
			}.bind(this));	
			
			$('jform_params_groupbysubcat1').addEvent('click', function(){
				this.hideChildren('jform_params_k2catsid', false);
				this.hideChildren('jform_params_catsid', false);
			}.bind(this));	
				
		}
		var selection = $('jaformparamsthemes');
		this.changeTheme(selection.value, false);	
	},
	hideChildren: function(catname, disabled){
		var els = $(catname).options;
		if(els==null) return;
		var el = null;
		for(var i=0; i<els.length; i++){
			el = els[i];
			if($(el).hasClass('subcat')){
				if(disabled){
					el.selected = false;
				}
				el.disabled = disabled;
			}
		}
	},
	changeProfile: function(profile){
		if(profile=='') return;
		
		this.profileactive = profile;
		
		this.fillData();	
	},
	
	showForm: function (){
		
		/*if($('japrams-form')!=null){										
			$('ja-layout-container').inject($('japrams-form')); 
			$('ja-layout-container').show();	
									
		}*/
			
	},
	
	serializeArray: function(){
		var els = new Array();
		var allelements = $(document.adminForm).elements;
		var k = 0;
		for (i=0;i<allelements.length;i++) {
		    var el = $(allelements[i]);
		    if (el.name && ( el.name.test (this.group+'\\[params\\]\\[.*\\]' || el.name.test (this.group+'\\[params\\]\\[.*\\]\\[\\]'))) ){
		    	els[k] = $(el);
		    	k++;
		    }
		}
		return els;
	},

	fillData: function (){
		profile = this.profileactive;
		var els = this.serializeArray(this.group);
		if(els.length==0) return;
		if (profiles[profile] == undefined) return;
		var cprofile = profiles[profile];
		els.each( function(el){	
			var name = this.getName(el);
			var value = (cprofile!=null && cprofile[name] != undefined)?cprofile[name]:'';
			
			el.setValue(value);
			if(name=='themes'){
				this.changeTheme(value, false);
			}
			
		}, this);
	},	
	
	
	getName: function (el){
		if (matches = el.name.match(this.group+'\\[params\\]\\[([^\\]]*)\\]')){
			return matches[1];
		}
		return '';
	},
	
	/****  Functions of Profile  ----------------------------------------------   ****/
	deleteProfile: function(){
		profile = this.profileactive;
		if(confirm(lg_confirm_delete_profile)){			
			var url = mod_url+'?japaramaction=deleteProfile&profile='+profile + '&template='+ templateactive;		;							
			this.submitForm(url, {}, 'profile');
		}		
	},
	
	cloneProfile: function (){
		var profilename = prompt(lg_enter_profile_name);
		
		if($type(profilename)){	
			if(profilename.clean()==''){
				alert(lg_please_enter_profile_name);
				return this.cloneProfile();
			}
			
			profilename = profilename.clean().replace(' ', '').toLowerCase().trim();
			
			profiles[profilename] = profiles[this.profileactive];
			var url = mod_url+'?japaramaction=cloneProfile&profile='+profilename+'&fromprofile='+this.profileactive+'&template='+templateactive;				
			this.submitForm(url, {}, 'profile');
		}
		
	},
	
	saveProfile: function (task){
		/* Rebuild data */		
		
		if(task){
			profiles[this.profileactive] = this.rebuildData();	
			var url = mod_url+'?japaramaction=saveProfile&profile='+this.profileactive;				
			this.submitForm(url, profiles[this.profileactive], 'profile',task);
		}
	},
	
	/****  Functions of Theme  ----------------------------------------------   ****/
	changeTheme: function(theme, dochangeProfile){
		
		if(themerequesting){
			jSonRequestTheme.cancel();
			themerequesting = false;
    	}		  
		if(theme == "linear"){
		    
		   japaramhelper.toggle_el($("jform_params_groupbysubcat"),false,true);
		   japaramhelper.toggle_el($("jform_params_maxSubCats"),false,true);
		}else{
		   japaramhelper.toggle_el($("jform_params_groupbysubcat"),true,true);
		   japaramhelper.toggle_el($("jform_params_maxSubCats"),true,true); 
		} 
		themerequesting = true;
			
		var selection = $('jaformparamsthemes');
	
		var link = mod_url+'?japaramaction=changeTheme&theme='+theme + '&template='+ templateactive; 
	
		jSonRequestTheme = new Request.JSON({url:link,
			
			onSuccess: function(result){
				themerequesting = false; 
				if(result.theme){
					switch (result.type){	
						case 'change':{
							
							if($('ext_params_from_template')==null ){	
								var li = new Element('li', {'class':'level3', 'id':'ext_params_from_template'});
								li.injectAfter(selection.getParent()); 		
							}
							if(result.html!=''){
								$('ext_params_from_template').show();								
							}
							else{
								$('ext_params_from_template').hide();
							}
												
							$('ext_params_from_template').innerHTML = result.html;
							this.fillDataTheme();
						}
					}
				}
				
				if(dochangeProfile) this.showForm();
				var japarams = new JAFormController();
				japarams.updateHeight();
				/*tooltip*/
				window.addEvent('domready', function() {
				   $$('.hasTip').each(function(el) {
					var title = el.get('title');
					if (title) {
					 var parts = title.split('::', 2);
					 el.store('tip:title', parts[0]);
					 el.store('tip:text', parts[1]);
					}
				   });
				   var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});
				  });
               	
			}.bind(this)
		}).post();
	},
	
	fillDataTheme: function (){
		
		var els = $('ext_params_from_template').getElements ('*[id^=jaform_params]');
		if(els.length==0) return;
	
		if (profiles[profile] == undefined) return;
		var cprofile = profiles[profile];
		
		els.each( function(el){	
			if(el.tagName.toLowerCase()!='label'){
				var name = this.getName(el);
				var value = (cprofile!=null && cprofile[name] != undefined)?cprofile[name]:'';
				
				el.setValue(value);
			}
			
		}, this);
		$$("#ext_params_from_template .validate-numeric").each(function(){
			$(this).addEvent("change",function(){
				document.formvalidator.isValid(document.id('module-form'));
		    });
		});
	},
	
	submitForm: function(url, request,  type,task) {
		
		if(requesting){
			jSonRequest.cancel();
    		requesting = false;
    	}		    	
    	requesting = true;
    	
		jSonRequest = new  Request.JSON({url:url, 
			onComplete: function(result){
				if(result =="") return;
				requesting = false;
				
				var contentHTML = '';
				if (result.successful && result.type!=null && result.type!='new') {
					contentHTML += "<div class=\"success-message\"><span class=\"success-icon\">"+result.successful+"</span></div>";
				}
				if (result.error) {
					contentHTML += "<div class=\"error-message\"><span class=\"error-icon\">"+result.error+"</span></div>";
				}
				
				if($type($('toolbar-box'))){
					if(!$type($('system-message'))){
						var msgobj = new Element('div', {'id': 'system-message', 'class':'clearfix'});
						msgobj.injectAfter($('toolbar-box'));
					}
					$('system-message').innerHTML = contentHTML;
					if (!this.msgslider) {
						this.msgslider = new Fx.Slide('system-message');
					}
					$clear(this.timer);
					this.msgslider.hide ();
					this.msgslider.slideIn.delay (100, this.msgslider, 'vertical');
					this.timer = this.msgslider.slideOut.delay (10000, this.msgslider, 'vertical');
				}
				
				if(result.profile){
					switch (result.type){	
						case 'new':{
							Joomla.submitbutton(document.adminForm.task.value);
						}break;
						case 'delete':{
							if(result.template==0){
								for(var j=0; j<this.el.options.length; j++){
									if(this.el.options[j].value==result.profile){
										this.el.remove(j);
									}
								}
							}
							else{
								profiles[result.profile] = Tempprofiles[result.profile];
							}
							this.el.options[0].selected = true;					
							this.changeProfile(this.el.options[0].value);
						}break;
						
						case 'clone':{							
							this.el.options[this.el.options.length] = new Option(result.profile, result.profile);							
							this.el.options[this.el.options.length-1].selected = true;
							this.changeProfile(result.profile);
						}break;
						
						default:
							//nothing
					}
					
				}
				else if(result.theme){
					switch (result.type){	
						case 'change':{
							
							if($('ext_params_from_template')==null){
								var ul = new Element('ul', {'class':'level3'});
								ul.injectAfter(this.selThemes.getParent().getParent()); 
								var li = new Element('li', {'colspan':'2', 'id':'ext_params_from_template'});
								li.inject(ul);			
							}
							if(result.html!=''){
								$('ext_params_from_template').getParent().show('table-row');
								
							}
							else{
								$('ext_params_from_template').getParent().hide();
							}
												
							$('ext_params_from_template').innerHTML = result.html;
							this.fillDataTheme();
						}
					}
				}
				
			}.bind(this),
			onSuccess: function(){
				if(task){
					Joomla.submitform(task, document.getElementById('module-form'));
				}
			}
		}).post(request);
	},
	
	rebuildData: function (){
		var els = this.serializeArray(this.group);
		var json = {};
		els.each(function(el){
			var name = this.getName(el);
			
			if( name!='' ){
				json[name] = el.getValue().toString().replace (/\n/g, '\\n').replace (/\t/g, '\\t').replace (/\r/g, '');
			}
			
		}, this);
		
		return json;
	}
	
	
});


if (MooTools.version >= '1.2') {
	Element._extend = Element.implement;
} else {
	Element._extend = Element.extend;
}

Element._extend ({
	getType: function() {
		var tag = this.tagName.toLowerCase();
		switch (tag) {
			case 'select':
			case 'textarea':
				return tag;	
			case 'input':
				if($type(this.type) && ( this.type=='text' || this.type=='password' || this.type=='hidden')){
					return this.type;
				}
				else{
					return  document.getElementsByName(this.name)[0].type;
				}
			default:
				return '';
		}
	},
	show: function(display){
		if(display==null) display = 'block';
		this.setStyle('display', display);
	},
	hide: function(){
		this.setStyle('display', 'none');
	},
	
	disable: function (){
		switch (this.getType().toLowerCase()) {
			case 'submit':
			case 'hidden':
			case 'password':
			case 'text':
			case 'textarea':
			case 'select':
				this.disabled = true;
				break;
			case 'checkbox':
			case 'radio':
				fields = document.getElementsByName(this.name);		
				$each(fields, function(option){
					option.disabled = true;
				});
			
		}
	},
		
	enable: function (){
		switch (this.getType().toLowerCase()) {
			case 'submit':
			case 'hidden':
			case 'password':
			case 'text':
			case 'textarea':
			case 'select':
				this.disabled = false;
				break;
			case 'checkbox':
			case 'radio':
				fields = document.getElementsByName(this.name);		
				$each(fields, function(option){
					option.disabled = false;						
				});
			
		}
	},
	
	setValue : function(newValue, rel) {
		
		switch (this.getType().toLowerCase()) {
			case 'submit':
			case 'hidden':
			case 'password':
			case 'text':
			case 'textarea':
				this.value=newValue;
				break;
			case 'checkbox':
				this.setInputCheckbox(newValue);
				break;
			case 'radio':
				this.setInputRadio(newValue);
				break;
			case 'select':	
				this.setSelect(newValue);
				break;
		}
		this.fireEvent('change');
		this.fireEvent('click');			
		
	},
	
	getValue: function (){
		
		switch (this.getType().toLowerCase()) {
			case 'submit':
			case 'hidden':
			case 'password':
			case 'text':
			case 'textarea':
				return this.value;
			case 'checkbox':
				return this.getInputCheckbox();
			case 'radio':
				return this.getInputRadio();
			case 'select':	
				return this.getSelect();
		}
		
		return false;
		
	},
	
	setInputCheckbox : function( newValue) {		
		fields = document.getElementsByName(this.name);
		arr_value = fields.length>1?newValue.split(','):new Array(newValue);
		
		for(var i=0; i<fields.length; i++){
			var option = fields[i];
			option.checked = false;
			if(arr_value.contains(option.value)){
				option.checked = true;
			}
		}		
	},
	
	setInputRadio : function( newValue) {
		fields = document.getElementsByName(this.name);		
		
		for(var i=0; i<fields.length; i++){
			var option = fields[i];
			option.checked = false;
			if(option.value==newValue){
				option.checked = true;
			}
		}			
	},

	setSelect : function(newValue) {
		arr_value = this.multiple? newValue.split(','):new Array(newValue);
		var selected = false;
		
		for(var i=0; i<this.options.length; i++){
			var option = this.options[i];
			option.selected = false;
			if (arr_value.contains (option.value)) {
				option.selected = true;
				selected = true;
			}
		}
		
		if(!selected){
			this.options[0].selected = true;
		}
	},

	getInputCheckbox : function() {
		var values = [];
		fields = document.getElementsByName(this.name);		
		for(var i=0; i<fields.length; i++){
			var option = fields[i];
			if (option.checked) values.push($pick(option.value, option.text));
		}		
		return values;
	},
	
	getInputRadio : function( ) {
		var values = [];
		fields = document.getElementsByName(this.name);		
		$each(fields, function(option){
			if (option.checked) values.push($pick(option.value, option.text));
		});
		return values;
	},

	getSelect : function() {
		var values = [];
		for(var i=0; i<this.options.length; i++){
			var option = this.options[i];
			if (option.selected) values.push($pick(option.value, option.text));
		}				
		return (this.multiple) ? values : values[0];
	}
	
});
