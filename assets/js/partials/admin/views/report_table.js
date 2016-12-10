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
