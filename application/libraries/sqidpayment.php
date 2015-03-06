<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sqidpayment {

    CONST REQUEST_URL = 'https://api.sqidpay.com/post';

    protected $config;
    protected $ci;

    public $token;
    public $response;
    public $errors = array();

    public function __construct() {
        $this->ci =& get_instance();

        $this->ci->load->config('sqidpayment');

        $this->config = array(
            'apiKey'            => $this->ci->config->item('apiKey'),
            'merchantCode'      => $this->ci->config->item('merchantCode'),
            'passPhrase'        => $this->ci->config->item('passPhrase'),
        );
    }

    public function setToken($token) {
        $this->token = $token;
        return $this;
    }

    public function resetResponse() {
        $this->response = '';
    }

    public function processFromToken( $data = array() )  {
        $mandatory = array(
            'amount',
            'currency',
            'token'
        );

        if ( TRUE === $this->validateParameters( $data, $mandatory ) ) {

            $data['methodName']   = 'processTokenPayment';
            $data['merchantCode'] = $this->config['merchantCode'];
            $data['apiKey']       = $this->config['apiKey'];
            $data['hashValue']    = $this->generateHash( $data['amount'] );
            $data['token']        = $this->token;

            if ( !isset($data['token']) ) {
                $data['token'] = $this->token;
            }

            $this->requestURL( self::REQUEST_URL, $data );

            return TRUE;
        }

        return FALSE;
    }

    private function validateParameters( $array, $mandatory=array() ) {
        $this->errors = array();

        if ( !empty($mandatory) ) {
            if ( count( $required = array_diff( $mandatory, array_keys($array)) ) > 0 )
                $this->errors = $required;
        }

        if ( $this->token !== '' ) {
            $this->errors = array_diff($this->errors, array('token'));
            $array['token'] = $this->token;
        }

        if ( is_array( $array ) ) {
            foreach ($array as $key => $value) {
                if ( $value == '' ) {
                    $this->errors[] = $key;
                }
            }
        }

        if ( empty($this->errors) )
            return TRUE;

        return FALSE;
    }

    private function generateHash( $amount ) {
        return md5( $this->config['passPhrase'] . $amount . $this->config['apiKey'] );
    }

    private function requestURL($url, $post, $header=false) {
        $this->resetResponse(); // Reset response variable

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,     "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,        json_encode($post));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,    FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    FALSE);
        curl_setopt($ch, CURLOPT_HEADER,            $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,    TRUE);

        $this->response = json_decode( curl_exec($ch), TRUE );
        
        curl_close($ch);
    }

}

/* End of file sqidpayment.php */
/* Location: ./application/libraries/sqidpayment.php */