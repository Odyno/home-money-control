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

