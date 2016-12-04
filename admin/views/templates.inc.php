<?php
/**
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
 * Created by PhpStorm.
 * User: astaniscia
 * Date: 04/12/16
 * Time: 21:34
 */
?>

<!-- Templates -->
<script type="text/template" id="item-template">

	<span class="view">
			<span class="count_type type_<%- type %>"></span>
			<h4><%- name %></h4>
			<p><%- description %></p>
			<a class="change dashicons dashicons-edit"></a>
			<a class="destroy dashicons dashicons-dismiss"></a>
		</span>


	<table class="edit form-table  ">
		<tbody>
		<tr>
			<th>
				Name
			</th>
			<td>
				<input class="large-text" type="text" name="name" value="<%- name %>"/>
				<p class="description">Select the Mnemonic name</p>
			</td>
		</tr>
		<tr>
			<th>
				Nature of Count
			</th>
			<td>
				<select id="selection_type" name="type">
					<option value="0"
					<%= type == '0' ? 'selected' : '' %> >Sopravvivenza</option>
					<option value="1"
					<%= type == '1' ? 'selected' : '' %>>Servizi optional</option>
					<option value="2"
					<%= type == '2' ? 'selected' : '' %>>Hobbies e tempo libero</option>
					<option value="3"
					<%= type == '3' ? 'selected' : '' %>>Imprevisti extra</option>
					<option value="4"
					<%= type == '4' ? 'selected' : '' %>>Entrate fisse</option>
					<option value="5"
					<%= type == '5' ? 'selected' : '' %>>Uscite fisse</option>
				</select>
				<p class="description">Select the type of counts:
				<ol>
					<li>Sopravvivenza: Rientrano in questa categoria le voci di spesa abituali e indispensabili per
						l'alimentazione, salute, trasporti
					</li>
					<li>Servizi optional: Questa categoria raccoglie le attività legate al tempo libero</li>
					<li>Hobbies e tempo libero: Questa categoria raccoglie le attività e gli oggetti che non servono
						al mero intrattenimento ma ti arricchisce mentalmente/fisicamente
					</li>
					<li>Imprevisti extra: Tutto ciò che non rientra nelle precedenti categorie, Viaggi, Regali,
						Riparazioni, Arredamenti e casalinghi, Elettronica
					</li>
					<li>Entrate fisse</li>
					<li>Uscite fisse</li>
				</ol>
				</p>
			</td>
		</tr>

		<tr>
			<th>
				Description
			</th>
			<td>
				<textarea cols="80" rows="10" class="large-text" name="description"><%- description %></textarea>
				<p class="description">Short description of this operation</p>
			</td>
		</tr>
		<tr>
			<th>
				<a class="close apply dashicons dashicons-no"></a>
			</th>
			<td>
				<span class="close button-primary" value="Close">Close</span>
			</td>
		</tr>
		</tbody>
	</table>
</script>


<script type="text/template" id="transaction-table-template">

	<span class="alignright" style="margin-bottom: 10px;">
				<span id="reportrange"></span>
			</span>

	<span class="alignleft" style="margin-bottom: 10px;">
		<a class="hmc-last-mount-report "><span class="dashicons dashicons-arrow-left-alt2"
		                                        style="margin-top: 3px;"> </span></a>
		<a class="hmc-this-mount-report "><span class="dashicons dashicons-admin-home"></span></a>
		<a class="hmc-next-mount-report "><span class="dashicons dashicons-arrow-right-alt2"
		                                        style="margin-top: 3px;"> </span></a>
	</span>

	<!-- The empty table we'll use as the example -->
	<table class="widefat">
		<thead>
		<tr>
			<th class="row-title">Data prevista</th>
			<th class="row-title">Natura</th>
			<th class="row-title">Descrizione</th>
			<th class="row-title">Ammontare</th>
		</tr>
		</thead>
		<!-- We'll attach the PeopleView to this element -->
		<tbody id="hmc_table_content">
		</tbody>
		<tfoot>
		<tr>
			<th></th>
			<th></th>
			<th>Totale</th>
			<th><span id="hmc_table_summ" class="enMoney"></span><a class="hmc-add-report alignright"><span
						class="dashicons dashicons-welcome-add-page"> </span></a></th>
		</tr>
		</tfoot>
	</table>
</script>