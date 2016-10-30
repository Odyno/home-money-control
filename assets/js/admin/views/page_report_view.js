/*
 * Copyright 2012  Alessandro Staniscia  (email : alessandro@staniscia.net)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

jQuery(document).ready(function($) {

  HMC.Views.SummPieChart = Backbone.View.extend({

    elementId: null,
    iChart: null,
    chartId: null,
    dataset: null,
    count_name: null,
    count_type: null,
    title: null,
    color: null,

    initialize: function(options) {
      this.elementId = options.id;
      this.count_name = options.name;
      this.loadData();
      return this
    },

    loadData: function (){
      var _me=this;
      $.ajax({
        context: _me,
        dataType: 'json',
        url: "/wp-json/hmc/v1/stats/",
        success: function(result) {
          _me.parseData(result);
          _me.render();
        },
      });
    },

    parseData: function(data_result) {
      var entrate = 0;
      var uscite = 0;
      var labels = [];
      var data = [];
      var backgroundColor = [];

      data_result.items.forEach(function(entry) {
        if (entry.type != HMC.COUNT_TYPE.ENTRATE.id) {
          labels.push(HMC.COUNT_TYPE.getLabel(entry.type));
          data.push(entry.total);
          backgroundColor.push(HMC.COUNT_TYPE.getColor(entry.type));
          uscite += parseFloat(entry.total);
        } else {
          entrate += parseFloat(entry.total);
        }
      }, this);

      labels.push("Cassa");
      data.push(entrate - uscite);
      backgroundColor.push("#efefef");

      this.dataset = {
        labels: labels,
        datasets: [{data: data, backgroundColor: backgroundColor}]
      };
      this.title = "Summary";
    },

    render: function() {
      var _me= this;
      var private_options = {
        responsive: true,
        padding: 2,
        legend: {
          display: false,
          position: 'right'
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
        type: 'pie',
        data: this.dataset,
        options: private_options
      });
      return this;
    }
  });

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
      this.loadData();
      return this;
    },

    loadData: function() {
      $.ajax({
        context: this, url: "/wp-json/hmc/v1/stats/" + this.count_type, success: function(result) {
          this.parseData(result);
          this.render();
        }
      });
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
        type: 'pie',
        data: this.dataset,
        options: private_options
      });
      return this;
    }
  });

  HMC.Views.ReportView = Backbone.View.extend({

    el: $('#report_dialog'),

    initialize: function() {
      _.bindAll(this, 'render', '_sync_ui', '_sync_model', 'open', 'close', 'save', 'destroy');
      this.value = null;
      this.description = null;
      this.category = null;
    },

    render: function() {
      var buttons = {'Ok': this.save};

      if (!this.model.isNew()) {
        _.extend(buttons, {'Delete': this.destroy});
      }
      _.extend(buttons, {'Cancel': this.close});

      this.$el.dialog({
        modal: true,
        title: (this.model.isNew() ? 'New' : 'Edit') + ' Report',
        buttons: buttons,
        open: this.open
      });

      $("#hmc_count").autocomplete({
        source: "/wp-json/hmc/v1/voices",
        focus: function(event, ui) {
          $("#hmc_count").val(ui.item.name);
          return false;
        },
        select: function(event, ui) {
          $("#hmc_count").val(ui.item.name);
          $("#hmc_count_id").val(ui.item.id);
          $("#hmc_count_description").html(ui.item.description);
          return false;
        }
      })
        .autocomplete("instance")._renderItem = function(ul, item) {
        return $("<li>")
          .append('<span class="count_type type_' + item.type + '"></span> ' + item.name + ' ' + ( item.description !== null ? item.description : ''))
          .appendTo(ul);
      };
      return this;
    },

    _processing: function(isInProgress) {
      if (isInProgress) {
        this.$("#hmc-processing").show();
      } else {
        this.$("#hmc-processing").hide();
      }
    },

    _sync_ui: function() {
      this._processing(true);

      this.$("#hmc_value_date").val(this.model.get('value_date'));

      if (!this.model.isNew()) {
        this.$("#hmc_value").val(this.model.get('value'));
        this.$("#hmc_description").val(this.model.get('description'));
        var category = this.model.get('category');
        this.$('#hmc_count_id').val(category['id']);
        this.$('#hmc_count').val(category['name']);
      } else {
        this.$("#hmc_value").val("");
        this.$("#hmc_description").val("");
        this.$('#hmc_count_id').val("0");
        this.$('#hmc_count').val("");
      }
      this._processing(false);
    },

    _sync_model: function() {

      var category = {};
      category['id'] = this.$("#hmc_count_id").val();

      var report = {};
      report['value_date'] = this.$("#hmc_value_date").val();
      report['posting_date'] = moment().toISOString();
      report['value'] = this.$("#hmc_value").val();
      report['category'] = category;
      report['description'] = this.$("#hmc_description").val();

      this.model.set(report);
    },

    open: function() {
      this._sync_ui();
    },

    close: function() {
      this._processing(false);
      this.$el.dialog('close');
    },

    save: function() {
      this._sync_model();
      this._processing(true);
      if (this.model.isNew()) {
        this.collection.create(this.model, {success: this.close});
      } else {
        this.model.save({}, {success: this.close});
      }

    },

    destroy: function() {
      if (confirm('Are you sure you want to DELETE this Count from database?')) {
        this.model.destroy({success: this.close});
      }
    },

    onEdit: function(model) {
      this.model = model;
      this.render();
    },

    onNew: function(startDate, collection) {
      this.collection = collection;
      this.model = new HMC.Models.Report({'value_date': startDate});
      this._sync_ui();
      this.render();
    }

  });

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
      this.reportView = new HMC.Views.ReportView();
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
      jQuery(this.el).empty();

      this.$el.addClass("type_" + this.model.get('category')['type']);

      //this.$el.addClass("alternate");

      // Write the table columns
      jQuery(this.el).append(jQuery('<td>' + this.model.get('category')['name'] + '</td>'));
      jQuery(this.el).append(jQuery('<td>' + this.model.get('description') + '</td>'));
      jQuery(this.el).append(jQuery('<td> <span class="enMoney">' + this.model.get('value') + '</span></td>'));
      jQuery(this.el).append(jQuery('<td>' + moment(this.model.get('value_date')).fromNow() + '</td>'));
      jQuery(this.el).append(jQuery('<td><a class="hmc-delete-report button-primary" >Cancella</a></td>'));

      return this;
    }
  });

  HMC.Views.ReportTable = Backbone.View.extend({
    // The collection will be kept here
    collection: null,

    _me:this,

    events: {
      "click .hmc-add-report": "onNew",
      "click .hmc-refresh-report": "onRefresh"
    },

    initialize: function(options) {
      this.template = _.template($('#transaction-table-template').html());

      this._elementID = options.id;
      this.collection = new HMC.Models.Reports();
      this.editor = new HMC.Views.ReportView();
      this.categories = options.category

      // Ensure our methods keep the `this` reference to the view itself
      _.bindAll(this, 'render');

      // Bind collection changes to re-rendering
      this.collection.bind('reset', this.render);
      this.collection.bind('add', this.render);
      this.collection.bind('remove', this.render);
      this.collection.bind('change', this.render);
      this.collection.bind('destroy', this.render);
      this.collection.bind('error', this.render);
      this.loadData();
    },



    loadData: function(){
      "use strict";
      var obj = {
        type: this.categories
      };
      this.collection.fetch({reset: true, data: $.param(obj)});
    },

    onRefresh: function(){
      "use strict";
      this.loadData();

    },

    onNew: function() {
      this.editor.onNew(moment(), this.collection);
    },

    render: function() {
      $(this.el).html(this.template());

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

  //var countModels = new HMC.Models.Reports();

  //var reportEditor = new HMC.Views.ReportView();

  var page = new HMC.Views.Calendar().render();

  var type_0 = new HMC.Views.PieChart({
    id: 'cat_type_0',
    name: HMC.COUNT_TYPE.HOBBIES_TEMPO_LIBERO.label,
    type_id: HMC.COUNT_TYPE.HOBBIES_TEMPO_LIBERO.id,
    color: HMC.COUNT_TYPE.HOBBIES_TEMPO_LIBERO.color
  });

  var type_1 = new HMC.Views.PieChart({
    id: 'cat_type_1',
    name: HMC.COUNT_TYPE.IMPREVISTI_EXTRA.label,
    type_id: HMC.COUNT_TYPE.IMPREVISTI_EXTRA.id,
    color: HMC.COUNT_TYPE.IMPREVISTI_EXTRA.color
  });

  var type_2 = new HMC.Views.PieChart({
    id: 'cat_type_2',
    name: HMC.COUNT_TYPE.SERVIZI_OPTIONAL.label,
    type_id: HMC.COUNT_TYPE.SERVIZI_OPTIONAL.id,
    color: HMC.COUNT_TYPE.SERVIZI_OPTIONAL.color
  });

  var type_3 = new HMC.Views.PieChart({
    id: 'cat_type_3',
    name: HMC.COUNT_TYPE.SOPRAVVIVENZA.label,
    type_id: HMC.COUNT_TYPE.SOPRAVVIVENZA.id,
    color: HMC.COUNT_TYPE.SOPRAVVIVENZA.color
  });

  var sum = new HMC.Views.SummPieChart({
    id: 'mouth_stat',
    name: 'statistic'
  });

  var table1 = new HMC.Views.ReportTable({el: $('#hmc_table_uscite_fisse').get(0), category: "5"});

  var table2 = new HMC.Views.ReportTable({el: $('#hmc_table_entrate_fisse').get(0), category: "4"});

});