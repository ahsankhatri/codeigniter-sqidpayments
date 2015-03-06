<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* CodeIgniter Test Controller
*/
class Test extends CI_Controller {
    
    function __construct() {
        parent::__construct();
    }

    public function tokenPayment() {
        $this->output->set_content_type('application/json');

        $this->load->library('Sqidpayment');

        $token = 'YOUR TOKEN GOES HERE';

        // You can set token in two different ways, through parameter.
        $this->sqidpayment->processFromToken( array(
            'currency'  => 'AUD',
            'amount'    => 5.00,
            'token'     => $token,
        ));

        // And through method
        $this->sqidpayment->setToken( $token );

        if ( empty($this->sqidpayment->errors) ) {

            // If succeed
            if ( isset( $this->sqidpayment->response['sqidResponseCode'] ) && $this->sqidpayment->response['sqidResponseCode'] == 0 ) {
                // $this->output->set_output(json_encode( $this->sqidpayment->response ));

                $this->output->set_output(json_encode(array(
                    'status' => true
                )));

                /**
                 * Payment successfull do all db queries here
                 */

            } else if ( isset($this->sqidpayment->response['sqidResponseMessage']) && $this->sqidpayment->response['sqidResponseCode'] < 0 ) {
                $this->output->set_output(json_encode(array(
                    'status' => false,
                    'message' => $this->sqidpayment->response['sqidResponseMessage']
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'status' => false,
                    'message' => 'Unknown error occured, please contact us!'
                )));
            }
            
        } else {
            // Required value errors
            $this->output->set_output(json_encode(array(
                'status' => false,
                'message' => implode("\n", $this->sqidpayment->errors)
            )));
        }
    }
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */