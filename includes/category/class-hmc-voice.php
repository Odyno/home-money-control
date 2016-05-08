<?php

if ( ! defined( 'ABSPATH' ) ) exit;



if (!class_exists('HMC_Voice')) {




    /**
     * Created by PhpStorm.
     * User: staniscia
     * Date: 26/05/14
     * Time: 18.37
     */
    class HMC_Voice
    {

        private $id;
        private $type;
        private $father;
        private $name;
        private $description;

        function __construct()
        {
            $this->id = HMC_UTILS::UUIDv4();
            $this->father = null;
            $this->name = "NONAME-" . $this->id;
            $this->description = null;
            $this->type= HMC_Voice_Type::HOBBIES_TEMPO_LIBERO;
        }


        /**
         * Make a new Voice from scratch
         * @param null $id
         * @param HMC_Voice $father
         * @param $name
         * @param $description
         */
        static function makeIt($id = null, $name, $description, $type)
        {
            $dummy = new HMC_Voice();
            if ($id != null) {
                $dummy->setId($id);
            }
            $dummy->setName($name);
            $dummy->setDescription($description);
            $dummy->setType($type);
            return $dummy;
        }

        static public function fromJSON($json)
        {
            return json_dencode($json);
        }

        public function setType($type)
        {

            $this->type = HMC_Voice_Type::check($type);
        }
        public function getType()
        {
            return $this->type;
        }





        /**
         * @param mixed $description
         */
        public function setDescription($description)
        {
            $this->description = $description;
        }

        /**
         * @return mixed
         */
        public function getDescription()
        {
            return $this->description;
        }

        /**
         * @param HMC_Count $father
         */
        public function setFather(HMC_Voice $father)
        {
            $this->father = $father;
        }

        /**
         * @return HMC_Voice
         */
        public function getFather()
        {
            return $this->father;
        }

        /**
         * @return mixed
         */
        private function setId($id)
        {
            $this->id = $id;
        }

        /**
         * @return mixed
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @param mixed $name
         */
        public function setName($name)
        {
            $this->name = $name;
        }

        /**
         * @return mixed
         */
        public function getName()
        {
            return $this->name;
        }

        public function toJSON()
        {
            return json_encode($this);
        }

        public function toArray()
        {
            return array(
                'id' => $this->getId(),
                'name' => $this->getName(),
                'description' => $this->getDescription(),
                'type' => $this->getType()
            );

        }




    }
}