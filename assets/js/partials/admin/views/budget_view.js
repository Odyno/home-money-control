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
    console.log(this.dataset);
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
