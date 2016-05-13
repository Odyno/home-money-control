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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'HMC_Voice_Type' ) ) {

	/**
	 * Class VoiceType
	 */
	class VoiceType {
		/**
		 * @var int
		 */
		public $id = 0;
		/**
		 * @var string
		 */
		public $label = "";
	}

	;


	/**
	 * Class HMC_Voice_Type
	 */
	class HMC_Voice_Type {

		/**
		 *
		 */
		const SOPRAVVIVENZA = 0;
		/**
		 *
		 */
		const SERVIZI_OPTIONAL = 1;
		/**
		 *
		 */
		const HOBBIES_TEMPO_LIBERO = 2;
		/**
		 *
		 */
		const IMPREVISTI_EXTRA = 3;
		/**
		 *
		 */
		const ENTRATE = 4;
		/**
		 *
		 */
		const USCITE_FISSE = 5;

		/**
		 * @var array
		 */
		static $types = array(
			'SOPRAVVIVENZA',
			'SERVIZI OPTIONAL',
			'HOBBIES E TEMPO LIBERO',
			'IMPREVISTI EXTRA',
			'ENTRATE',
			'USCITE FISSE'
		);


		/**
		 * @param $in
		 *
		 * @return int|string
		 * @throws Exception
		 */
		static public function check( $in ) {
			if ( is_numeric( $in ) ) {
				HMC_Voice_Type::toString( $in );

				return $in;
			} else {
				return HMC_Voice_Type::fromString( $in );
			}

		}

		/**
		 * @param $in
		 *
		 * @return int|string
		 * @throws Exception
		 */
		static public function fromString( $in ) {
			foreach ( HMC_Voice_Type::$types as $key => $type ) {
				if ( $type == $in || $key == $in ) {
					return $key;
				}
			}
			throw new Exception( "Not Valid Type" );
		}

		/**
		 * @param $id
		 *
		 * @return mixed
		 * @throws Exception
		 */
		static public function toString( $id ) {
			if ( ! isset( HMC_Voice_Type::$types[ $id ] ) ) {
				throw new Exception( "not Found" );
			}

			return HMC_Voice_Type::$types[ $id ];
		}


		/**
		 * @param $id
		 *
		 * @return VoiceType
		 * @throws Exception
		 */
		public static function get( $id ) {
			if ( ! isset( HMC_Voice_Type::$types[ $id ] ) ) {
				throw new Exception( "not Found" );
			}

			$a        = new VoiceType();
			$a->id    = $id;
			$a->label = HMC_Voice_Type::$types[ $id ];

			return $a;
		}

		/**
		 * @return array
		 * @throws Exception
		 */
		public static function getAll() {
			$out = array();

			foreach ( HMC_Voice_Type::$types as $key => $type ) {
				array_push( $out, HMC_Voice_Type::get( $key ) );
			}

			return $out;
		}


	}

}