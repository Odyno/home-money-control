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

if ( ! class_exists( 'HMC_Database' ) ) {

	/**
	 * Class HMC_Database
	 */
	class HMC_Database {

		private $category_entity;
		private $transaction_entity;

		/**
		 * HMC_Database constructor.
		 */
		public function __construct() {
			$this->load_dependencies();
			// $this->add_actions();
			// $this->add_filters();
		}

		/**
		 *  Load the dependency
		 */
		public function load_dependencies() {
			require_once( __HMC_PATH__ . 'includes/database/category/class-hmc-voice-type.php' );
			require_once( __HMC_PATH__ . 'includes/database/category/class-hmc-voice.php' );
			require_once( __HMC_PATH__ . 'includes/database/category/class-hmc-category.php' );
			require_once( __HMC_PATH__ . 'includes/database/transactions/class-hmc-field.php' );
			require_once( __HMC_PATH__ . 'includes/database/transactions/class-hmc-transactions.php' );
		}

		public function get_category_entity() {
			if ( null === $this->category_entity )
				$this->category_entity = new HMC_Category();
			return $this->category_entity ;
		}

		public function get_transaction_entity() {
			if ( null === $this->transaction_entity )
				$this->transaction_entity = new HMC_Transactions();
			return $this->transaction_entity ;
		}
		

		/**
		 * Build the database.
		 */
		public function create() {
			HMC_Category::BUILD_DB();
			HMC_Transactions::BUILD_DB();
		}

		/**
		 * Fill the database.
		 */
		public function fill() {
			HMC_Category::FILL_DB();
		}

		/**
		 * Drop the database.
		 */
		public static function DROP() {
			HMC_Category::DROP_DB();
			HMC_Transactions::DROP_DB();
		}
	}
}