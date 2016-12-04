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
