<?php


/**
 * Created by PhpStorm.
 * User: astaniscia
 * Date: 09/10/15
 * Time: 16:46
 */
class Hmc_Api_Category
{

    /**
     * Server object
     *
     * @var WP_JSON_ResponseHandler
     */
    protected $categoryHandler;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->categoryHandler = new HMC_Category();

    }


    public function register_routes($routes)
    {

        $routes['/hmc/voices'] = array(
            array(array($this, 'get_voices'), WP_JSON_Server::READABLE),
            array(array($this, 'create_voice'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON),
        );
        $routes['/hmc/voices/(?P<id>[\w|-]+)'] = array(
            array(array($this, 'get_voice'), WP_JSON_Server::READABLE),
            array(array($this, 'edit_voice'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON),
            array(array($this, 'delete_voice'), WP_JSON_Server::DELETABLE),
        );
        $routes['/hmc/voice/types'] = array(
            array(array($this, 'get_voice_types'), WP_JSON_Server::READABLE),
        );
        $routes['/hmc/voice/types/(?P<id>[\w|-]+)'] = array(
            array(array($this, 'get_voice_type'), WP_JSON_Server::READABLE),
        );

        // Add more custom routes here

        return $routes;
    }

    public function get_voice_types(){
        return new WP_JSON_Response(HMC_Voice_Type::getAll());
    }

    public function get_voice_type($id){
        return new WP_JSON_Response(HMC_Voice_Type::get($id));
    }


    /**
     * @return WP_JSON_Response
     */
    public function get_voices()
    {

        $allVoice = $this->categoryHandler->getVoices();

        $out = array();
        foreach ($allVoice as $voice) {
            array_push($out, $voice->toArray());
        }
        $response = new WP_JSON_Response($out);
        return $response;
    }


    public function delete_voice($id){
        try {
            $result = $this->categoryHandler->removeVoice($id);
        } catch (Exception $e) {
            return new WP_Error("c-4","Error on delete: " . $e->getMessage(), $e);
        }
        $response = new WP_JSON_Response("");
        $response->set_status(202);
        return $response;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function create_voice($data){
        unset($data['id']);
        try {
            return $this->update_voice($data,true);
        } catch (Exception $e) {
            return new WP_Error("c-1","Error on creation: " . $e->getMessage(), $e);
        }
    }

    /**
     * @param $data
     * @return mixed|WP_Error|WP_JSON_Response
     */
    public function edit_voice($id, $data, $_headers = array())
    {
        try {

            if ($id != HMC_UTILS::check_array_value('id', $data, true)){
                throw new Exception('The id is not congruent');
            }
            return $this->update_voice($data,false);
        } catch (Exception $e) {
            return new WP_Error("c-2","Error on edit: " . $e->getMessage(), $e);
        }
    }



    private function update_voice( $data, $is_creation=false){

            if (!$is_creation) $id = HMC_UTILS::check_array_value('id', $data, true);
            $name = HMC_UTILS::check_array_value('name', $data, true);
            $description = HMC_UTILS::check_array_value('description', $data, false);

            $type = HMC_UTILS::check_array_value('type', $data, false);

            $voice = HMC_Voice::makeIt($id, $name, $description, $type);

            $result = $this->categoryHandler->updateVoice($voice);

            if (is_wp_error($result))
                return $result;

            $response = new WP_JSON_Response($voice->toArray());
            $response->set_status(201);
            $response->header('Location', json_url('/hmc/voices/' . $result));
            return $response;

    }



    /**
     * Get a single voice
     * @param $id
     * @return WP_Error|WP_JSON_Response
     */
    public function get_voice($id)
    {
        $data = $this->categoryHandler->getVoices($id);
        if (empty($id) or !is_array($data) or count($data) != 1) {
            return new WP_Error('c-3', __('Invalid voice ID.'), array('status' => 404));
        }
        return new WP_JSON_Response($data[0]->toArray());

    }





}