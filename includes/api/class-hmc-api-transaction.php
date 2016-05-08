<?php


/**
 * Created by PhpStorm.
 * User: astaniscia
 * Date: 09/10/15
 * Time: 16:46
 */
class Hmc_API_Transaction
{

    /**
     * Server object
     *
     * @var WP_JSON_ResponseHandler
     */
    protected $transactionHandler;
    protected $categoryHandler;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->transactionHandler = new HMC_Transactions();
        $this->categoryHandler = new HMC_Category();

    }


    public function register_routes($routes)
    {
        $routes['/hmc/fields'] = array(
            array(array($this, 'get_fields'), WP_JSON_Server::READABLE),
            array(array($this, 'create_field'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON),
        );
        $routes['/hmc/fields/(?P<id>[\w|-]+)'] = array(
            array( array( $this, 'get_field'), WP_JSON_Server::READABLE),
            array( array( $this, 'edit_field'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
            array( array( $this, 'delete_field'), WP_JSON_Server::DELETABLE ),
        );

        // Add more custom routes here

        return $routes;
    }


    public function get_fields()
    {
        HMC_UTILS::checkUser();

        $allTransactions = $this->transactionHandler->get();

        $out = array();

        foreach ($allTransactions as $voice) {
            array_push($out, $voice->toArray());
        }
        $response = new WP_JSON_Response($out);
        return $response;
    }



    public function edit_field($id, $data, $_headers = array())
    {
        HMC_UTILS::checkUser();

        try {
            if ($id != HMC_UTILS::check_array_value('id', $data, true)){
                throw new Exception('The id is not congruent');
            }
            return $this->update_trans($data,false);
        } catch (Exception $e) {
            return new WP_Error("c-2","Error on edit: " . $e->getMessage(), $e);
        }
    }

    public function create_field($data)
    {
        unset($data['id']);
        try {
            return $this->update_trans($data,true);
        } catch (Exception $e) {
            return new WP_Error("c-1","Error on creation: " . $e->getMessage(), $e);
        }
    }



    /**
     * Create a new post for any registered post type.
     */
    private function update_trans($data, $is_creation=false){

        HMC_UTILS::checkUser();

        if (!$is_creation) $id = HMC_UTILS::check_array_value('id', $data, true);

        try {
            $value = HMC_UTILS::check_array_value('value', $data, true);

            $description = HMC_UTILS::check_array_value('description', $data, false);

            $user_id = get_current_user_id();


            $this->categoryHandler->getVoices(HMC_UTILS::check_array_value('category', $data, true));

            $category = $this->categoryHandler->getVoices(HMC_UTILS::check_array_value('id', $data['category'], true));

            HMC_UTILS::check_array_value('0', $category, true);

            if (HMC_UTILS::check_array_value('posting_date', $data) != null)
                $posting_date = HMC_Time::MakeFromECMAScriptISO8601($data['posting_date']);
            else
                $posting_date = null;

            if (HMC_UTILS::check_array_value('value_date', $data) != null)
                $value_date = HMC_Time::MakeFromECMAScriptISO8601($data['value_date']);
            else
                $value_date = null;


            $field = new HMC_Field( $value, $category[0], $description, $user_id, $posting_date, $value_date,$id);

            $result = $this->transactionHandler->add($field);

            if (is_wp_error($result))
                return $result;

            $response = new WP_JSON_Response($field->toArray());
            $response->set_status(201);
            $response->header('Location', json_url('/hmc/fields/' . $result));
            return $response;

        } catch (Exception $e) {
            return new WP_Error("Add error",  'Error found: ' .  $e->getMessage());
        }
    }


    /**
     * Retrieve a post.
     *
     * @uses get_post()
     * @param int $id Post ID
     * @return array Post entity
     */
    public function get_field($id)
    {
        HMC_UTILS::checkUser();

        $where=array();
        $where[].=HMC_Transactions::COL_ID . " = '$id'";

        $data = $this->transactionHandler->get($where);

        if (empty($id) || empty($data) ) {
            return new WP_Error('HMC_Invalid_Id', __('Invalid voice ID.'), array('status' => 404));
        }


        $response = new WP_JSON_Response($data[0]->toArray());
        return $response;
    }



    public function delete_field($id){
        HMC_UTILS::checkUser();

        try {
            $result = $this->transactionHandler->delBy($id);
        } catch (Exception $e) {
            return new WP_Error("c-4","Error on delete: " . $e->getMessage(), $e);
        }
        $response = new WP_JSON_Response("");
        $response->set_status(202);
        return $response;
    }




}