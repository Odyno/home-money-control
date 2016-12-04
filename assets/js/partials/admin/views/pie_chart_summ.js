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


    this.model = new HMC.Models.AllStat();
    this.listenTo(this.model, 'reset', this.update);
    this.listenTo(this.model, 'add', this.update);
    this.listenTo(this.model, 'change', this.update);
    this.listenTo(this.model, 'destroy', this.update);
    this.loadData();

    return this
  },

  loadData: function (){
    this.model.fetch();
  },

  update: function(){
    this.parseData(this.model.toJSON());
    this.render();
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
