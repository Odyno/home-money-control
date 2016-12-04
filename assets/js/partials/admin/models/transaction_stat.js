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