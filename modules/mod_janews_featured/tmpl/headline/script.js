/**
 * ------------------------------------------------------------------------
 * JA News Featured Module for Joomla 2.5
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

JA_NewsHeadline = new Class ({
	initialize: function(options){
		this.options = $extend({
			autoroll: 0,
			total: 0,
			delaytime: 10,
			moduleid: 0
		}, options || {});
		this.elements = [];		
		this._next = 0;
	},

	start: function() {
		this.newshlcache = $('ja-newshlcache-'+this.options.moduleid);
		this.container = $('ja-zinhl-newsitem-'+this.options.moduleid);
		this.switcher = $('jahl-switcher-'+this.options.moduleid);
		this.btt_prev = $('jahl-prev-'+this.options.moduleid);
		this.btt_next = $('jahl-next-'+this.options.moduleid);
		this.btt_prev_title = $('jahl-prev-title-'+this.options.moduleid);
		this.btt_next_title = $('jahl-next-title-'+this.options.moduleid);
		this.zinhl_counter = $('ja-zinhl-counter-'+this.options.moduleid);
		
		//Get cache news to array
		if(!this.newshlcache || !this.newshlcache.getChildren()) return;		
		this.container.setStyles ({
			overflow: 'hidden',
			display: 'block',
			position: 'relative'
		});
		this.newshlcache.getChildren().each(function (el){
			el._title = el.getElement('.ja-newstitle')?el.getElement('.ja-newstitle').getProperty('title'):'';
			el.setStyles({
				opacity: 0,
				display: 'block',
				width: this.container.offsetWidth,
				position: 'absolute',
				top: 0,
				left: 0
			});
			el.dispose().inject (this.container);
			this.elements.push(el);
		},this);
		this.animfirst();
	},
	
	run: function() {
		if(!this.options.autoroll || this.options.total<2) return;
		this._next = this.jacurrent < this.options.total - 1?this.jacurrent+1:0;
		this.timer = setTimeout(this.swap.bind(this), this.options.delaytime*1000);	
	},
	
	getNext: function() {
		return (this.jacurrent < this.options.total - 1)?this.jacurrent+1:0;
	},

	getPrev: function() {
		return this.jacurrent > 0 ? this.jacurrent - 1 : this.options.total - 1;
	},

	next: function() {
		this._next = this.getNext();
		this.swap();
	},
	
	prev: function() {
		this._next = this.getPrev();
		this.swap();
	},
	
	toogle: function() {
		clearTimeout(this.timer);
		this.options.autoroll = this.options.autoroll?0:1;
		Cookie.write('JAHL-AUTOROLL',this.options.autoroll);
		if(this.switcher) {
			this.switcher.src = this.options.autoroll? this.switcher.src.replace('play','pause'):this.switcher.src.replace('pause','play');
			this.switcher.title = this.options.autoroll?'Pause':'Play';
		}
		this.run();
	},

	swap: function() {
		
		if(!this.elements.length) return;
		clearTimeout(this.timer);
		this.animrun();
	},

	animfirst: function (){
		this.jacurrent = 0;
		var el2 = this.elements[this.jacurrent];
		new Fx.Tween(el2).start('opacity', 0, 1);
		new Fx.Tween(this.container).start('height', 0, el2.offsetHeight);

		if(this.btt_prev) this.btt_prev.setProperty('title', this.elements[this.getPrev()]._title);
		if(this.btt_next) this.btt_next.setProperty('title', this.elements[this.getNext()]._title);
		if(this.btt_prev_title) {
			if (this.btt_prev_title.getChildren()) this.btt_prev_title.getChildren().each(function(el){el.dispose();});
			this.elements[this.getPrev()].getElement('.ja-newstitle').clone().inject(this.btt_prev_title);
		}
		if(this.btt_next_title) {
			if (this.btt_next_title.getChildren()) this.btt_next_title.getChildren().each(function(el){el.dispose();});
			this.elements[this.getNext()].getElement('.ja-newstitle').clone().inject(this.btt_next_title);
		}
		if(this.zinhl_counter) this.zinhl_counter.innerHTML = (this.jacurrent+1)+"/"+this.options.total;
		this.run();
	},
	
	animrun: function() {
		var el1 = this.elements[this.jacurrent];
		this.jacurrent = this._next;
		var el2 = this.elements[this.jacurrent];
		new Fx.Tween(el1).start('opacity', 1, 0);
		new Fx.Tween(el2).start('opacity', 0, 1);
		new Fx.Tween(this.container).start('height', el1.offsetHeight, el2.offsetHeight);

		if(this.btt_prev) this.btt_prev.setProperty('title', this.elements[this.getPrev()]._title);
		if(this.btt_next) this.btt_next.setProperty('title', this.elements[this.getNext()]._title);
		if(this.btt_prev_title) {
			if (this.btt_prev_title.getChildren()) this.btt_prev_title.getChildren().each(function(el){el.dispose();});
			this.elements[this.getPrev()].getElement('.ja-newstitle').clone().inject(this.btt_prev_title);
		}
		if(this.btt_next_title) {
			if (this.btt_next_title.getChildren()) this.btt_next_title.getChildren().each(function(el){el.dispose();});
			this.elements[this.getNext()].getElement('.ja-newstitle').clone().inject(this.btt_next_title);
		}
		if(this.zinhl_counter) this.zinhl_counter.innerHTML = (this.jacurrent+1)+"/"+this.options.total;
		this.run();
	}
});