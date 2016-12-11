/*! HMC - v0.0.1 - 2016-12-11 */
jQuery(document).ready(function ($) {
var previousHMC = window.HMC;

var HMC = {
	Models: {},
	Views: {},
	noConflict: noConflict = function() {
		window.HMC = previousHMC;
		return this;
	}
};

window.HMC = HMC;

HMC.COUNT_TYPE = {

	SOPRAVVIVENZA: {
		id: 0,
		label: "Sopravvivenza",
		color: "#298048"
	},
	SERVIZI_OPTIONAL: {
		id: 1,
		label: "Optional",
		color: "#ADD8E6"
	},
	HOBBIES_TEMPO_LIBERO: {
		id: 2,
		label: "Hobbies",
		color: "#FFA500"
	},
	IMPREVISTI_EXTRA: {
		id: 3,
		label: "Imprevisti",
		color: "#FF0000"
	},
	ENTRATE: {
		id: 4,
		label: "Entrate",
		color: "#008000"
	},
	USCITE_FISSE: {
		id: 5,
		label: "Uscite",
		color: "#0000FF"
	},
	BUDGET: {
		id: 6,
		label: "Budget",
		color: "#ffbc00"
	},
	getColor: function (id) {
		if (this.SOPRAVVIVENZA.id == id) {
			return this.SOPRAVVIVENZA.color;
		}else if (this.SERVIZI_OPTIONAL.id == id) {
			return this.SERVIZI_OPTIONAL.color;
		}else if (this.HOBBIES_TEMPO_LIBERO.id == id) {
			return this.HOBBIES_TEMPO_LIBERO.color;
		}else if (this.IMPREVISTI_EXTRA.id == id) {
			return this.IMPREVISTI_EXTRA.color;
		}else if (this.ENTRATE.id == id) {
			return this.ENTRATE.color;
		}else if (this.USCITE_FISSE.id == id) {
			return this.USCITE_FISSE.color;
		}
	},
	getLabel: function (id) {
		if (this.SOPRAVVIVENZA.id == id) {
			return this.SOPRAVVIVENZA.label;
		}else if (this.SERVIZI_OPTIONAL.id == id) {
			return this.SERVIZI_OPTIONAL.label;
		}else if (this.HOBBIES_TEMPO_LIBERO.id == id) {
			return this.HOBBIES_TEMPO_LIBERO.label;
		}else if (this.IMPREVISTI_EXTRA.id == id) {
			return this.IMPREVISTI_EXTRA.label;
		}else if (this.ENTRATE.id == id) {
			return this.ENTRATE.label;
		}else if (this.USCITE_FISSE.id == id) {
			return this.USCITE_FISSE.label;
		}
	}

};

/**
 * Created by astaniscia on 03/12/16.
 */

HMC.Models.Count = Backbone.Model.extend({
  defaults: function () {
    return {
      id: null,
      name: null,
      description: null,
      type: null
    };
  }

});
/**
 * Created by astaniscia on 03/12/16.
 */

HMC.Models.Count_Type = Backbone.Model.extend({
  defaults: function () {
    return {
      id: null,
      label: null
    };
  }

});
HMC.Models.Counts = Backbone.Collection.extend({
  model: HMC.Models.Count,
  url: '/wp-json/hmc/v1/voices'
});
/**
 * Created by astaniscia on 03/12/16.
 */


HMC.Models.Report = Backbone.Model.extend();

/**
 * Created by astaniscia on 03/12/16.
 */


HMC.Models.Reports = Backbone.Collection.extend({
  model: HMC.Models.Report,
  url: '/wp-json/hmc/v1/fields'
});

/**
 * Created by astaniscia on 03/12/16.
 */



HMC.Models.AllStat = Backbone.Model.extend({
  defaults: function (){
    return {
      from: '2016-10-01',
      to: '2016-10-31',
      count: '0',
      items : [],
    };
  },
  url: '/wp-json/hmc/v1/allstats'
});
/**
 * Created by astaniscia on 03/12/16.
 */



HMC.Models.Statistic = Backbone.Model.extend({
  defaults: function (){
    return {
      type: '6',
      from: '2016-10-01',
      to: '2016-10-31',
      count: '0',
      total: '0',
      items : [],
    };
  },
  url: '/wp-json/hmc/v1/stats'
});
/**
 * Created by astaniscia on 03/12/16.
 */


HMC.Models.TransactionStat = Backbone.Model.extend({
  defaults: function(){
    return {
      from: "2016-11-01",
      to: "2016-11-30",
      "totals":[
        {"total":"0","type":"4"},
        {"total":"0","type":"5"}
      ],
      "avgs":[
        {"avg":"0","type":"0"},
        {"avg":"0","type":"1"},
        {"avg":"0","type":"2"},
        {"avg":"0","type":"3"},
        {"avg":"0","type":"4"},
        {"avg":"0","type":"5"}
      ]
    }
  },
  url: '/wp-json/hmc/v1/stats/transactions'
});
/**
 * Created by astaniscia on 03/12/16.
 */

HMC.Views.CountTableField = Backbone.View.extend({

  //... is a list tag.
  tagName: "li",

  // Cache the template function for a single item.
  template: _.template($('#item-template').html()),

  // The DOM events specific to an item.
  events: {
    "click a.change": function () {
      this.set_edit_mode(true);
    },
    "dblclick": function () {
      this.set_edit_mode(true);
    },
    "click .close": function () {
      this.set_edit_mode(false);
    },
    "click a.destroy": "clear_model",
    "change input": "changed",
    "change select": "changed",
    "change textarea": "changed",
    "click a.save": "save"
  },

  initialize: function () {
    this.listenTo(this.model, 'change', this.render);
    this.listenTo(this.model, 'destroy', this.remove);
  },

  render: function () {
    var json_model=this.model.toJSON();
    this.$el.html(this.template(json_model));
    if (json_model['name'] === "unamed"){
      this.set_edit_mode(true);
    }
    return this;
  },

  set_edit_mode: function (is_edit_mode) {
    if (is_edit_mode) {
      this.$el.addClass("editing");
    } else {
      this.$el.removeClass("editing");
    }
  },


  changed:function (evt) {
    var changed = evt.currentTarget;
    var ivalue = $(evt.currentTarget).val();
    var iname = $(evt.currentTarget).attr('name');
    var obj = {};
    obj[iname] = ivalue;
    this.model.set(obj);

    this.model.save();

  },

  save:function (evt) {
    var changed = evt.currentTarget;
    var ivalue = $(evt.currentTarget).val();
    var iname = $(evt.currentTarget).attr('name');
    var obj = {};
    obj[iname] = ivalue;
    this.model.set(obj);

    this.model.save();

  },


  clear_model: function () {
    if (confirm('Are you sure you want to DELETE this Count from database?')) {
      this.model.destroy();
    }
  }

});
/**
 * Created by astaniscia on 03/12/16.
 */
// The Application
// ---------------

// Our overall **AppView** is the top-level piece of UI.
HMC.Views.CountsTable = Backbone.View.extend({

  // Instead of generating a new element, bind to the existing skeleton of
  // the App already present in the HTML.
  el: $("#countview"),


  // Delegated events for creating new items, and clearing completed ones.
  events: {
    "click a.create": "createOnCount"
  },

  // At initialization we bind to the relevant events on the `Todos`
  // collection, when items are added or changed. Kick things off by
  // loading any preexisting todos that might be saved in *localStorage*.
  initialize: function () {

    this.input = this.$("#new-todo");
    this.counts = new HMC.Models.Counts();


    this.listenTo(this.counts, 'add', this.addOne);
    this.listenTo(this.counts, 'remove', this.notifyRemove);
    this.listenTo(this.counts, 'error', this.notifyError);
    this.listenTo(this.counts, 'all', this.render);

    this.footer = this.$('footer');
    this.header = this.$('header');
    this.main = $('#main');

    this.counts.fetch();
  },

  // Re-rendering the App just means refreshing the statistics -- the rest
  // of the app doesn't change.
  render: function () {

    if (this.counts.length) {
      this.main.show();
    } else {
      this.main.hide();
    }

  },

  notifyRemove: function () {
    this.header.append('<div class="notice notice-info is-dismissible"><p>Delete Done!</p></div>')
  },

  notifyError: function () {
    this.header.append('<div class="notice notice-warning is-dismissible"><p>Error appears!</p></div>')
  },


  // Add a single todo item to the list by creating a view for it, and
  // appending its element to the `<ul>`.
  addOne: function (count) {
    console.log("called");
    var view = new HMC.Views.CountTableField({model: count});
    this.$("#count-list").prepend(view.render().el);
  },



  createOnCount: function (e) {
    var data = new HMC.Models.Count();
    data.set({ "name" : "unamed" });
    return this.counts.create(data);
  },


});


HMC.Views.ReportView = Backbone.View.extend({

	el: $("#report_dialog"),

	events: {
		"click #hmc-report-ok": "save",
		"click #hmc-report-delete": "destroy",
		"click .hmc-report-cancel": "close",
		"input #hmc_count": "searchBestCount",
		"change input[type=radio]": 'selectCount'
	},

	initialize: function() {
		_.bindAll(this, "render", "_sync_ui", "_sync_model", "close", "save", "destroy");
		this.value = null;
		this.description = null;
		this.category = null;
		this.domain=null;

		this._processing(false,"search");
		this._processing(false,"save");
		this._processing(false,"delete");
	},

	setDomainSearch: function (types){
		"use strict";
		this.domain=types;
	},

	/**
	 * Seaqrch che best count in according of name
	 */
	searchBestCount: function(){
		var me= this;
		var filter=null;
		var data=[];
		if ( filter != $("#hmc_count").val() ){

			filter=$("#hmc_count").val();


			if (this.domain !=null){
         data.push("type="+encodeURIComponent(this.domain));
			}

      if (filter != null){
				data.push("term="+encodeURIComponent(filter));
			}

			if (me.currentRequest != null){
				me.currentRequest.abort();
			}

			this.currentRequest=$.ajax({
				url: "/wp-json/hmc/v1/voices",
				method: "GET",
				data: data.join('&'),
				dataType: "json",
				beforeSend: function() {

				 me.hideCountList();
				 me._processing(true,"search");
				}
			}).done(function(data){
				data.forEach(function(item){
					$("#acc_"+ item.type ).show();
					$("#acc_label_"+ item.type ).html(HMC.COUNT_TYPE.getLabel(item.type));
					$("#accordion_"+ item.type ).append(
						"<label>"+
						"  <input type='radio' name='selected_count_id' value='' data-id='"+ item.id +"' data-name='"+ item.name +"' data-description='"+ item.description +"' />" +
						"  <span>"+  ( item.description !== null ?   ( item.name +  " - " + item.description ) : item.name   )  + "</span>"+
						"</label><br>"
					);
				});
				me._processing(false,"search");
			});
		}


	},

	hideCountList: function(){
		$("#acc_0").hide();
		$("#accordion_0" ).html("");
		$("#acc_1").hide();
		$("#accordion_1" ).html("");
		$("#acc_2").hide();
		$("#accordion_2" ).html("");
		$("#acc_3").hide();
		$("#accordion_3" ).html("");
		$("#acc_4").hide();
		$("#accordion_4" ).html("");
		$("#acc_5").hide();
		$("#accordion_5" ).html("");
	},

	selectCount: function(item){
		var obj=$(item.target);
		$("#hmc_count").val(obj.data('name'));
		$("#hmc_count_id").val(obj.data('id'));
		$("#hmc_count_description").html(obj.data('description'));
	},

	configureButtons: function(){
		"use strict";
		$("#hmc-report-ok").show();
		if (!this.model.isNew()) {
			$("#hmc-report-delete").show();
		}else{
			$("#hmc-report-delete").hide();
		}
		$(".hmc-report-cancel").show();
	},

	configureTitle: function(){
		"use strict";
		$(".hmc-modal-title").text((this.model.isNew() ? "New" : "Edit") + " Report");
	},

	render: function() {
		this._sync_ui();
		this.hideCountList();
		this.configureButtons();
		this.configureTitle();
		$("#hmc_value_date").datepicker();
		this.$el.show();
		return this;
	},

	_processing: function(isInProgress,type) {
		if (isInProgress) {
			this.$("#hmc-"+type+"-processing").show();
		} else {
			this.$("#hmc-"+type+"-processing").hide();
		}
	},

	_sync_ui: function() {
		this._processing(true,"save");

		this.$("#hmc_value_date").val(this.model.get("value_date"));

		if (!this.model.isNew()) {
			this.$("#hmc_value").val(this.model.get("value"));
			this.$("#hmc_description").val(this.model.get("description"));
			var category = this.model.get("category");
			this.$("#hmc_count_id").val(category["id"]);
			this.$("#hmc_count").val(category["name"]);
		} else {
			this.$("#hmc_value").val("");
			this.$("#hmc_description").val("");
			this.$("#hmc_count_id").val("0");
			this.$("#hmc_count").val("");
		}
		this._processing(false,"save");
	},

	_sync_model: function() {
		var category = {};

		category["id"] = this.$("#hmc_count_id").val();
		var report = {};
		report["value_date"] = this.$("#hmc_value_date").val();
		report["posting_date"] = moment().toISOString();
		report["value"] = this.$("#hmc_value").val();
		report["category"] = category;
		report["description"] = this.$("#hmc_description").val();

		this.model.set(report);
	},


	close: function() {
		this._processing(false,"delete");
		this._processing(false,"search");
		this._processing(false,"save");
		this.$el.hide();
	},

	save: function() {
		var me= this;
		this._sync_model();
		this._processing(true,"save");
		if (! this.model.isValid()){
			this._processing(false,"save");
			alert('Please fill all the attribute');
		}
		if (this.model.isNew()) {
			this.collection.create(this.model, {success: me.close});
		} else {
			this.model.save(null, {
				success: me.close
			});
		}

	},

	destroy: function() {

		this._processing(true,"delete");
		if (confirm("Are you sure you want to DELETE this Count from database?")) {
			this.model.destroy({success: this.close});
		}
	},

	onNew: function(startDate, collection) {
		this.collection = collection;
		this.onEdit(new HMC.Models.Report({"value_date": startDate}));
	},

	onEdit: function(model) {
		this.model = model;
		this.render();
	}
});

/**
 * Created by astaniscia on 03/12/16.
 */

HMC.Views.Calendar = Backbone.View.extend({

  tag: "div",

  events: {
    "click .hmc-caledar-new-report": "onNew"
  },

  // Instead of generating a new element, bind to the existing skeleton of
  // the App already present in the HTML.
  el: $("#hmc_calendar"),

  initialize: function(options) {
    this.collection = new HMC.Models.Reports();
    _.bindAll(this, 'render', 'select', '_addOne', '_addAll', 'eventClick', '_fetch_events');
    this.listenTo(this.collection, 'reset', this._addAll);
    this.listenTo(this.collection, 'add', this._addOne);
    this.listenTo(this.collection, 'change', this._change);
    this.listenTo(this.collection, 'destroy', this._destroy);
  },

  setReportHandler: function(obj){
    "use strict";
    this.reportView = obj;
  },

  render: function() {
    this.$el.fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'basicWeek,month'
      },
      lang: navigator.language || navigator.userLanguage,
      eventLimit: true,
      defaultView: 'basicWeek',
      selectable: true,
      selectHelper: true,
      editable: true,
      ignoreTimezone: false,
      select: this.select,
      eventClick: this.eventClick,
      //eventDrop: this.eventDropOrResize,
      //eventResize: this.eventDropOrResize,
      events: this._fetch_events
    });
  },

  _fetch_events: function(iStart, iEnd, iTimezone, callback) {
    var obj = {
      from: iStart.format("YYYY-MM-DD HH:mm:ss"),
      to: iEnd.format("YYYY-MM-DD HH:mm:ss"),
      type: '0,1,2,3'
    };
    this.collection.fetch({reset: true, data: $.param(obj)});
  },

  eventClick: function(fcEvent) {
    this.reportView.setDomainSearch(null);
    this.reportView.onEdit(this.collection.get(fcEvent.id));
  },

  eventDropOrResize: function(fcEvent) {
    this.collection.get(fcEvent.id).save({start: fcEvent.start, end: fcEvent.end});
  },

  onNew: function() {
    console.log("pluto");
    this.select(moment(), null, null)
  },

  select: function(startDate, endDate, allday) {
    this.reportView.setDomainSearch(null);
    this.reportView.onNew(startDate, this.collection);
  },

  _addAll: function() {
    var objs = this.collection.toJSON();

    objs.forEach(function(_this) {
      return function(obj) {
        if (_this._format_object(obj) != false) {
          _this.$el.fullCalendar('renderEvent', obj);
        }
      };
    }(this));
  },

  _addOne: function(model) {
    var obj = model.toJSON();
    if (this._format_object(obj) != false) {
      this.$el.fullCalendar('renderEvent', obj);
    }
  },

  _format_object: function(obj) {
    var ref;
    if (!((typeof obj !== "undefined" && obj !== null ? (ref = obj.category) != null ? ref.name : void 0 : void 0) != null)) {
      console.log("scartato", obj);
      return false;
    }
    obj["title"] = obj.value + " € " + obj.category.name;
    obj["start"] = obj.value_date;
    obj["allDay"] = true;
    obj['className'] = "count_type type_" + obj.category.type
  },

  _change: function(event) {
    var obj = this.$el.fullCalendar('clientEvents', event.get('id'))[0];

    if (this._format_object(obj) != false) {
      this.$el.fullCalendar('updateEvent', obj);
      this.$el.fullCalendar('refetchEvents');
    }
  },

  _destroy: function(event) {
    this.$el.fullCalendar('removeEvents', event.id);
  }

});

/**
 * Created by astaniscia on 03/12/16.
 */


HMC.Views.PieChart = Backbone.View.extend({

  elementId: null,
  iChart: null,
  chartId: null,
  dataset: null,
  count_name: null,
  count_type: null,
  title: null,
  color: null,

  events: {
    'click': 'loadData'
  },

  initialize: function(options) {
    this.elementId = options.id;
    this.count_name = options.name || "unamed";
    this.count_type = options.type_id || "0";

    this.color = options.color || '#' + (Math.random().toString(16) + '0000000').slice(2, 8);

    this.model = new HMC.Models.Statistic();
    this.listenTo(this.model, 'reset', this.update);
    this.listenTo(this.model, 'add', this.update);
    this.listenTo(this.model, 'change', this.update);
    this.listenTo(this.model, 'destroy', this.update);
    this.loadData();

    return this;
  },

  loadData: function() {
    this.model.fetch({ url: '/wp-json/hmc/v1/stats/'+this.count_type });
  },

  update: function(){
    this.parseData(this.model.toJSON());
    this.render();
  },

  parseData: function(data_result) {

    var labels = [];
    var data = [];
    var backgroundColor = [];
    var borderColor =[];

    data_result.items.forEach(function(entry) {
      labels.push(entry.name);
      data.push(entry.total);
      backgroundColor.push(this.change_brightness(this.color, ( 100 - (entry.total * 100) / data_result.total)));
      borderColor.push('#eeeeee');
    }, this);

    if (data.length == 0) {
      labels.push('none');
      data.push(0);
      backgroundColor.push("#efefef");
      borderColor.push('#eeeeee');
    }

    this.dataset = {
      labels: labels,
      datasets: [{data: data, backgroundColor: backgroundColor, borderColor: borderColor }]
    };

    //console.log("PIPPO Type: "+this.count_type +" Nome: "+ HMC.COUNT_TYPE.getLabel(this.count_type),this.dataset);
    this.title = this.count_name + " € " + data_result.total;
  },

  change_brightness: function(hex, percent) {
    // strip the leading # if it's there
    hex = hex.replace(/^\s*#|\s*$/g, '');

    // convert 3 char codes --> 6, e.g. `E0F` --> `EE00FF`
    if (hex.length == 3) {
      hex = hex.replace(/(.)/g, '$1$1');
    }

    var r = parseInt(hex.substr(0, 2), 16),
      g = parseInt(hex.substr(2, 2), 16),
      b = parseInt(hex.substr(4, 2), 16);

    return '#' +
      ((0 | (1 << 8) + r + (256 - r) * percent / 100).toString(16)).substr(1) +
      ((0 | (1 << 8) + g + (256 - g) * percent / 100).toString(16)).substr(1) +
      ((0 | (1 << 8) + b + (256 - b) * percent / 100).toString(16)).substr(1);
  },

  render: function() {
    var _me=this;
    var private_options = {
      responsive: true,
      padding: 2,
      legend: {
        display: false
      },
      title: {
        display: true,
        text: this.title
      },
      onClick: function(){
        _me.loadData();
      },
    };

    var ctx = document.getElementById(this.elementId).getContext("2d");
    this.iChart = new Chart(ctx, {
      type: 'doughnut',
      data: this.dataset,
      options: private_options
    });
    return this;
  }
});


/**
 * Created by astaniscia on 03/12/16.
 */

HMC.Views.ReportTableField = Backbone.View.extend({
  // Each person will be shown as a table row
  tagName: 'tr',

  events: {
    "click .hmc-delete-report": "destroy",
    "click .hmc-edit-report": ""
  },


  initialize: function(options) {
    // Ensure our methods keep the `this` reference to the view itself
    _.bindAll(this, 'render');

    // If the model changes we need to re-render
    this.model.bind('change', this.render);
  },

  destroy: function() {
    if (confirm('Are you sure you want to DELETE this Count from database?')) {
      this.model.destroy();
    }
  },

  render: function() {
    // Clear existing row data if needed
    this.$el.empty();

    this.$el.addClass("type_" + this.model.get('category')['type']);

    // Write the table columns
    this.$el.append($('<td>' + moment(this.model.get('value_date')).fromNow() + '</td>'));
    this.$el.append($('<td>' + this.model.get('category')['name'] + '</td>'));
    this.$el.append($('<td>' + this.model.get('description') + '</td>'));
    this.$el.append($('<td> <span class="enMoney">' + this.model.get('value') + '</span> <a class="alignright hmc-delete-report" ><span class="dashicons dashicons-trash"></span></a></td>'));
    return this;
  }
});

/**
 * Created by astaniscia on 03/12/16.
 */

HMC.Views.ReportTable = Backbone.View.extend({
  // The collection will be kept here
  collection: null,

  template: _.template($('#transaction-table-template').html()),

  _me: this,

  events: {
    "click .hmc-add-report": "onNew",
    "click .hmc-refresh-report": "onRefresh",
    "click .hmc-last-mount-report": "onStepBefore",
    "click .hmc-next-mount-report": "onStepAfter",
    "click .hmc-this-mount-report": "onMoveToday",
  },

  initialize: function(options) {
    this._elementID = options.id;
    this.collection = new HMC.Models.Reports();
    this.editor = null;
    this.categories = options.category;
    // Ensure our methods keep the `this` reference to the view itself
    _.bindAll(this, 'render');
    // Bind collection changes to re-rendering
    this.collection.bind('reset', this.render);
    this.collection.bind('add', this.render);
    this.collection.bind('remove', this.render);
    this.collection.bind('change', this.render);
    this.collection.bind('destroy', this.render);
    this.collection.bind('error', this.render);
    this.onMoveToday();
  },


  onStepBefore: function(){
    this.startDate = moment(this.startDate).subtract(1, 'month').startOf('month');
    this.endDate = moment(this.endDate).subtract(1, 'month').endOf('month');
    this.onRefresh();
  },

  onStepAfter: function(){
    this.startDate = moment(this.startDate).add(1, 'month').startOf('month');
    this.endDate = moment(this.endDate).add(1, 'month').endOf('month');
    this.onRefresh();
  },

  onMoveToday: function(){
    this.startDate = moment().startOf('month');
    this.endDate = moment().endOf('month');
    this.onRefresh();
  },

  onRefresh: function(){
    "use strict";
    this.loadData();

  },

  loadData: function(){
    var obj = {
      from: this.startDate.format("YYYY-MM-DD HH:mm:ss"),
      to: this.endDate.format("YYYY-MM-DD HH:mm:ss"),
      type: this.categories
    };
    this.collection.fetch({reset: true, data: $.param(obj)});
  },



  onNew: function() {
    this.editor.setDomainSearch(this.categories);
    this.editor.onNew(moment(), this.collection);
  },

  updateRage: function(start, end){
    this.startDate = start;
    this.endDate = end;
    this.loadData();
  },

  render: function() {

    this.$el.html(this.template());

    this.$el.find('#reportrange').html(this.startDate.format('MMMM YYYY'));

    var element = this.$el.find("#hmc_table_content");
    element.empty();

    var total = 0;

    this.collection.forEach(function(item) {
      var itemView = new HMC.Views.ReportTableField({
        model: item
      });
      element.append(itemView.render().el);
      total += item.get('value');
    });

    this.$el.find("#hmc_table_summ").append(total);
    //this.$el.find("#hmc_table_num").append(this.collection.lenght());

    return this;
  }
});

/**
 * Created by astaniscia on 03/12/16.
 */

HMC.Views.BudgetView = Backbone.View.extend({

  events: {
    "click .hmc-refresh-report": "onRefresh",
    "click .hmc-last-mount-report": "onStepBefore",
    "click .hmc-next-mount-report": "onStepAfter",
    "click .hmc-this-mount-report": "onMoveToday",
  },

  initialize: function(options) {
    this.model= new HMC.Models.TransactionStat();
    this.startDate = moment().startOf('month');
    this.endDate = moment().endOf('month');
    this.moptions = {
      legend:{
        position: 'right'
      },
      scale: {
        ticks: {
          beginAtZero: true
        }
      }
    };
    this.listenTo(this.model, 'sync', this.update);
    this.loadData();
  },

  onStepBefore: function(){
    this.startDate = moment(this.startDate).subtract(1, 'month').startOf('month');
    this.endDate = moment(this.endDate).subtract(1, 'month').endOf('month');
    this.onRefresh();
  },

  onStepAfter: function(){
    this.startDate = moment(this.startDate).add(1, 'month').startOf('month');
    this.endDate = moment(this.endDate).add(1, 'month').endOf('month');
    this.onRefresh();
  },

  onMoveToday: function(){
    this.startDate = moment().startOf('month');
    this.endDate = moment().endOf('month');
    this.onRefresh();
  },

  onRefresh: function(){
    this.loadData();
  },

  loadData: function(){
    var obj = {
      from: this.startDate.format("YYYY-MM-DD HH:mm:ss"),
      to: this.endDate.format("YYYY-MM-DD HH:mm:ss"),
      type: this.categories
    };
    this.model.fetch({reset: true, data: $.param(obj)});
  },

  update: function(){
    this.parseData(this.model.toJSON());
    this.render();
  },

  parseData: function(data_result) {

    var datarow=[];
    for (var i = 0; i < 6; i++) {
      datarow[i] ={
        label: HMC.COUNT_TYPE.getLabel(i),
        avg : 0,
        budget : 0,
        total : 0
      }
    }

    data_result.avgs.forEach(function(entry) {
      datarow[entry.type].avg=entry.avg
    }, this);

    data_result.totals.forEach(function(entry) {
      datarow[entry.type].total=entry.total
    }, this);

    this.dataset = {
      labels: [ datarow[0].label, datarow[1].label, datarow[2].label, datarow[3].label, datarow[5].label ],
      datasets: [
        this._formatData("Dati","54, 162, 235",[datarow[0].total, datarow[1].total, datarow[2].total, datarow[3].total, datarow[5].total ]),
        this._formatData("Media"    ,"75, 192, 192",[datarow[0].avg, datarow[1].avg, datarow[2].avg, datarow[3].avg, datarow[5].avg ]),
        this._formatData("Budget"   ,"255, 99, 132",[datarow[0].budget, datarow[1].budget, datarow[2].budget, datarow[3].budget, datarow[5].budget  ])
      ]
    };
  },

  _formatData: function($label, $color, $data){
    return {
      label: $label,
      backgroundColor: "rgba(" + $color + ", 0.2)",
      borderColor: "rgba(" + $color + ", 1)",
      pointBackgroundColor: "rgba(" + $color + ", 1)",
      pointBorderColor: "#fff",
      pointHoverBackgroundColor: "#fff",
      pointHoverBorderColor: "rgba(" + $color + ",1)",
      data: $data
    }
  },

  render: function() {

    var ctx = this.$el.find('#mouth_budget');

    this.$el.find('#stat_range').html(this.startDate.format('MMMM YYYY'));

    var myRadarChart = new Chart(ctx, {
      type: 'bar',
      data: this.dataset,
      options: this.moptions
    });
  },

});

});