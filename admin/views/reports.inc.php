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
 * Date: 22/08/16
 * Time: 08:49
 */
?>
<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div id='hmc_calendar'></div>

	<div id='report_dialog' class='dialog ui-helper-hidden'>

		<table>
			<tr>
				<td>
					<input id="hmc_value" class="large-text" type="number" value="10.00"  step="0.5" data-number-to-fixed="2" data-number-stepfactor="100"  />
					<p class="description">Ammount of Value</p>

				</td>
			</tr>
			<tr>
				<td>
					<textarea id="hmc_description" cols="80" rows="5" class=" all-options" ></textarea>
					<p class="description">Note</p>
				</td>
			</tr>
			<tr>
				<td>
					<input type="text"      id="hmc_count" class="large-text" >
					<input type="hidden"    id="hmc_count_id">
					<p  class="description" id="hmc_count_description">Type of actions</p>
				</td>
			</tr>
			<tr>
				<td>
					<!-- <input class="large-text" type="text" name="dates" value="<%- dates %>"/> -->
					<input type="hidden" id="hmc_value_date">
				</td>
			</tr>
		</table>

	</div>

</div>