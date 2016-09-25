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
 * Created by astaniscia on 27/08/16.
 */
jQuery(document).ready(function ($) {
    
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


    HMC.Models.Report = Backbone.Model.extend();

    HMC.Models.Reports = Backbone.Collection.extend({
        model: HMC.Models.Report,
        url: '/wp-json/hmc/v1/fields'
    });




    
    

});