<?php


if (!defined('ABSPATH')) exit;


if (!class_exists('HMC_Category')) {

    /**
     * Description of class-HMC-category
     *
     * @author astaniscia
     */
    class HMC_Category
    {

        const TABLE_VERSION_KEY = "HMC_CATEGORY_DB_VERSION";
        const TABLE_VERSION = "0.0.4";
        const TABLE_NAME = "HMC_CATEGORY";
        const TYPE = 'type';
        const ID = 'id';
        const FATHER_ID = 'father_id';
        const NAME = 'name';
        const DESCRIPTION = 'description';





        static function DROP_DB()
        {
            global $wpdb;
            $tableName = $wpdb->prefix . self::TABLE_NAME;
            delete_option(self::TABLE_VERSION_KEY);
            $wpdb->query("DROP TABLE IF EXISTS $tableName");
        }


        static function BUILD_DB()
        {
            $installed_ver = get_option(self::TABLE_VERSION_KEY);
            global $wpdb;
            if ($installed_ver != self::TABLE_VERSION) {
                $tableName = $wpdb->prefix . self::TABLE_NAME;
                $sql = 'CREATE TABLE ' . $tableName . ' (
                    ' . self::ID . ' varchar(100) NOT NULL,
                    ' . self::FATHER_ID . ' varchar(100) DEFAULT NULL,
                    ' . self::TYPE . ' TINYINT NOT NULL,
                    ' . self::NAME . ' varchar(100) NOT NULL,
                    ' . self::DESCRIPTION . ' tinytext,
                    UNIQUE KEY ' . self::ID . ' ( ' . self::ID . ' )
                  );';

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
                update_option(self::TABLE_VERSION_KEY, self::TABLE_VERSION);
            }
        }

        static function FILL_DB()
        {

            $lines = file(__HMC_PATH__ . "assets/db/HMC_CATEGORY.DF");
            foreach ($lines as $line) {
                $keydata = explode(";", $line);
                self::ADD_DB(HMC_Voice::makeIt($keydata[0], $keydata[2], $keydata[3]));
            }
        }

        private static function ADD_DB(HMC_Voice $voice)
        {
            global $wpdb;

            $table_name = $wpdb->prefix . self::TABLE_NAME;

            if ($voice->getFather() != null) {
                self::ADD_DB($voice->getFather());
            } else {

                $rows_affected = $wpdb->replace($table_name, array(
                    self::ID => $voice->getId(),
                    self::FATHER_ID => null,
                    self::TYPE => $voice->getType(),
                    self::NAME => $voice->getName(),
                    self::DESCRIPTION => $voice->getDescription()
                ),
                    array('%s', '%s', '%s', '%s', '%s')
                );


                if ($rows_affected == false) {
                    $out = new WP_Error("HMC_INSER", "ERROR ON INSER");
                } else {
                    $out = $voice->getId();
                }

            }

            return $out;
        }


        /**
         * Return the list of all category
         * @param type $owner the owner of category, null is root category
         * @return array<voice>
         */
        public function getVoices($id, $order_by = self::NAME, $order = "asc")
        {
            global $wpdb;
            $table_name = $wpdb->prefix . self::TABLE_NAME;
            if (isset($id) && $id != null) {
                $where = "  " . self::ID . "  =  '" . $id . "' ";
            } else {
                $where = " 1 ";
            }

            $select = 'SELECT * ';
            $select .= ' FROM ' . $table_name;
            $select .= ' WHERE ' . $where;
            $select .= ' ORDER BY ' . $order_by . ' ' . $order;

            $allVoice = $wpdb->get_results($select, ARRAY_A);
            $out = array();

            if ($allVoice) {
                foreach ($allVoice as $voice) {
                    $v = HMC_Voice::makeIt($voice['id'], $voice['name'], $voice['description'],$voice['type']);
                    array_push($out, $v);
                }
            }
            return $out;
        }

        /**
         * Add Voice on repository
         * @param type $voice
         */
        public function updateVoice(HMC_Voice $voice)
        {
            if ($voice->getFather() != null) {
                $voice->setFather($this->checkFather($voice));
            }
            return self::ADD_DB($voice);
        }


        /**
         * Remove Voice on repository
         * @param type $voice
         */
        public function removeVoice($id)
        {
            global $wpdb;

            $table_name = $wpdb->prefix . self::TABLE_NAME;
            $where = array(self::ID => $id);
            $result = $wpdb->delete($table_name, $where, $where_format = null);


            if (!$result) {
                throw new Exception("Error on delete " . id);
            }

        }


        private function checkFather(HMC_Voice $voice)
        {

            //controllo che esiste il pade sia ben fatto
            if ($voice->getFather() != null) {
                $father = $voice->getFather();

                //deve essere veramente il padre
                if ($father->getFather() != null) {
                    throw new Exception('No Father of Faterh');
                }
                //il padre deve esistere
                $rf = $this->getVoices($father->getId());
                if (count($rf) == 0) {
                    throw new Exception('No Exist');
                }
                return ($rf[0]);
            }

        }


    }

}
