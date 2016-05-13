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

if ( ! class_exists( 'HMC_Voice' ) ) {


	/**
	 * Class HMC_Voice
	 */
	class HMC_Voice {

		/**
		 * @var string
		 */
		private $id;
		
		/**
		 * @var int
		 */
		private $type;
		
		/**
		 * @var null
		 */
		private $father;
		
		/**
		 * @var string
		 */
		private $name;
		
		/**
		 * @var null
		 */
		private $description;

		/**
		 * HMC_Voice constructor.
		 */
		function __construct() {
			$this->id          = HMC_UTILS::UUIDv4();
			$this->father      = null;
			$this->name        = 'NONAME-' . $this->id;
			$this->description = null;
			$this->type        = HMC_Voice_Type::HOBBIES_TEMPO_LIBERO;
		}


		/**
		 * Make a new Voice from scratch.
		 *
		 * @param string $id null The identification.
		 * @param string $name The name of this voice.
		 * @param string $description The descriptions of voice.
		 * @param int $type The type.
		 *
		 * @return HMC_Voice
		 */
		static function make_it( $id = null, $name = null, $description = null, $type = HMC_Voice_Type::HOBBIES_TEMPO_LIBERO ) {
			$dummy = new HMC_Voice();
			if ( null !== $id ) {
				$dummy->setId( $id );
			}
			$dummy->setName( $name );
			$dummy->setDescription( $description );
			$dummy->setType( $type );

			return $dummy;
		}

		/**
		 * @param $json
		 *
		 * @return mixed
		 */
		public function fromJSON( $json ) {
			return json_dencode( $json );
		}

		/**
		 * @param $type
		 */
		function setType( $type ) {

			$this->type = HMC_Voice_Type::check( $type );
		}

		/**
		 * @return int
		 */
		function getType() {
			return $this->type;
		}


		/**
		 * @param mixed $description
		 */
		public function setDescription( $description ) {
			$this->description = $description;
		}

		/**
		 * @return mixed
		 */
		public function getDescription() {
			return $this->description;
		}

		/**
		 * @param HMC_Count $father
		 */
		public function setFather( HMC_Voice $father ) {
			$this->father = $father;
		}

		/**
		 * @return HMC_Voice
		 */
		public function getFather() {
			return $this->father;
		}

		/**
		 * @return mixed
		 */
		private function setId( $id ) {
			$this->id = $id;
		}

		/**
		 * @return mixed
		 */
		public function getId() {
			return $this->id;
		}

		/**
		 * @param mixed $name
		 */
		public function setName( $name ) {
			$this->name = $name;
		}

		/**
		 * @return mixed
		 */
		public function getName() {
			return $this->name;
		}

		/**
		 * @return mixed|string|void
		 */
		public function toJSON() {
			return json_encode( $this );
		}

		/**
		 * @return array
		 */
		public function toArray() {
			return array(
				'id'          => $this->getId(),
				'name'        => $this->getName(),
				'description' => $this->getDescription(),
				'type'        => $this->getType()
			);

		}

	}
}