<?php

/*

this is just for testing purpose!
I'll update RESTAPI.php asap
BK 2022-02-11

*/



namespace RRZE\WP\EXT;

defined('ABSPATH') || exit;

class RESTAPI
{
    private function getResponse($sType, $sParam = NULL){
        $aRet = [
            'valid' => FALSE, 
            'content' => ''
        ];

        $aGetArgs = [];

        if (($this->provider == 'bite') && (!empty($this->options['rrze-jobs_apiKey']))) {
            $aGetArgs = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'BAPI-Token' => $this->options['rrze-jobs_apiKey']
                    ]
                ];
        }

        $api_url = $this->helper->getURL($this->provider, $sType) . $sParam;

        $content = wp_remote_get($api_url, $aGetArgs);
        $content = $content["body"];

        $content = json_decode($content, true);

        if ($this->provider == 'bite'){
            if (!empty($content['code'])){
                $aRet = [
                    'valid' => FALSE, 
                    'content' => '<p>' . __('Error', 'rrze_jobs') . ' ' . $content['code'] . ' : ' . $content['type'] . ' - ' . $content['message'] . '</p>'
                ];
            }elseif (self::isValid($content)){
                $aRet = [
                    'valid' => TRUE, 
                    'content' => $content
                ];
            }else{
                $aRet = [
                    'valid' => FALSE,
                    'content' => '<p>' . __('This job offer is not available', 'rrze-jobs') . '</p>'
                ];
            }
        }else{
            if (!$content) {
                $aRet = [
                    'valid' => FALSE, 
                    'content' => '<p>' . ($sType == 'single' ? __('This job offer is not available', 'rrze-jobs') : __('Cannot connect to API at the moment.', 'rrze-jobs'))  . '</p>'
                ];    
            }else{
                $aRet = [
                    'valid' => TRUE, 
                    'content' => $content
                ];
            }
        }

        return $aRet;
    }


}
