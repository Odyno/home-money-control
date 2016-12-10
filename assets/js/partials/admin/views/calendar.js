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
