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
include_once('templates.inc.php');

?>
<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div id="countview">
		<header>
			<span class="align-right"><a class="create dashicons-before dashicons-plus button-primary" >Create new</a></span>
		</header>
		<section id="main">
			<div class="postbox">
				<div class="inside">
				<ul id="count-list"></ul>
					</div>
			</div>
		</section>

		<footer>
			<div id="counts-count"></div>
		</footer>
	</div>


	<script>
		jQuery(document).ready(function ($) {
			var countsTable = new HMC.Views.CountsTable();
		});
	</script>
</div>