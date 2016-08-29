<?php


if ( ! defined( 'ABSPATH' ) ) exit;




if (!class_exists('HMC_Field')) {

    /**
     * Description of class-HMC-field
     *
     * @author astaniscia
     */
    class HMC_Field
    {

        private $id;
        private $posting_date;
        private $value_date;
        private $value;
        private $category;
        private $description;
        private $user_id;

        function __construct(
            $value, HMC_Voice $category,
            $description, $user_id, HMC_Time $posting_date = null, HMC_Time $value_date = null, $id = null)
        {
            $this->id = ($id == null ? HMC_UTILS::UUIDv4() : $id);
            $this->posting_date = ($posting_date != null ? $posting_date : HMC_Time::NOW_DATE());
            $this->value_date = ($value_date != null ? $value_date : HMC_Time::NOW_DATE());
            $this->value = $value;
            $this->category = $category;
            $this->description = $description;
            $this->user_id = $user_id;
        }


        public function getId()
        {
            return $this->id;
        }

        public function getPosting_date()
        {
            return $this->posting_date;
        }

        public function getValue_date()
        {
            return $this->value_date;
        }

        public function getValue()
        {
            return $this->value;
        }

        public function getCategory()
        {
            return $this->category;
        }

        public function getDescription()
        {
            return $this->description;
        }

        public function getUser_id()
        {
            return $this->user_id;
        }

        public function setId($id)
        {
            $this->id = $id;
            return $this;
        }

        public function setPosting_date($posting_date)
        {
            $this->posting_date = $posting_date;
            return $this;
        }

        public function setValue_date($value_date)
        {
            $this->value_date = $value_date;
            return $this;
        }

        public function setValue($value)
        {
            $this->value = $value;
            return $this;
        }

        public function setCategory($category)
        {
            $this->category = $category;
            return $this;
        }

        public function setDescription($description)
        {
            $this->description = $description;
            return $this;
        }

        public function setUser_id($user_id)
        {
            $this->user_id = $user_id;
            return $this;
        }

        public function toArray()
        {
            return array(
                'id' => $this->id,
                'posting_date' =>  $this->posting_date != null ? $this->posting_date->toArray() : null,
                'value_date' => $this->value_date !=null ? $this->value_date->toArray() : null,
                'value' => floatval($this->value),
                'category' => $this->category->toArray(),
                'description' => $this->description,
                'user_id' => $this->user_id
            );
        }


    }

}
