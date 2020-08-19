<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('BASEPATH') OR exit('No direct script access allowed');


class UploadFile
{
    const PICTURES = "PICTURES"; //图片
    const VIDEOS   = "VIDEOS";  //视频
    const LOGS     = "LOGS";    //日志
    const PLUG     = "PLUG";    //插件
    
    public static function upload($fieldname,$type,$file_name = null){
        $CI = &get_instance();
        if($file_name == null){
            $file_name = $fieldname.date("YmdHis",time());
        }
        $upload_config = $CI->config->item('upload');
        $CI->load->model("Img_model");
        $md5_file = md5_file($_FILES[$fieldname]['tmp_name']);
        $Img = $CI->Img_model->get_one_data(['img_md5'=>$md5_file]);
        if($Img){
             return $Img;
        }
        else
        {
            $sys_value_option = $CI->admin_process->get_sys_value_option();
            $upload_config['max_size'] = $sys_value_option['file_max_size'];
            $upload_config['upload_path'] = $sys_value_option['file_upload_path'];
            $upload_config['file_name'] =$file_name;
            $CI->load->library('upload');
            $upload = new Upload($upload_config);
            if(!$upload->do_upload($fieldname)){
               throw new Exception("上传失败");
            }
            $ext = $CI->upload->get_extension($_FILES[$fieldname]['name']);
            $path = $upload_config['upload_path'].$file_name.$ext;
            $data = [
              "group"=>$type,
                "path" =>new CURLFile(realpath($path))
            ];
            $url = $CI->config->item("FileServerApiUrl");
            $response =  self::request($url,$data,true);
            
            $ouput_data = json_decode($response);
            if($ouput_data->code == 0){
                $insert_data = [
                    "name"          =>$ouput_data->msg->name,
                    "path"          =>"group2",
                    "img_md5"       =>$md5_file,
                    "create_time"   =>time(),
                    "url"           =>$ouput_data->msg->url
                ];
             
                $CI->Img_model->insertData($insert_data);
                $insert_data['id'] = $CI->db->insert_id();
                unlink($path);
                return $insert_data;   
            }
            else
            {
                unlink($path);
                throw new Exception("上传失败");
            }
        }
    }
    protected static function request($url,$postFields,$isPost = false,$header = null){
       $ch = curl_init();
       if (is_null($header)) {
            $header = array(
                'Pragma' => 'no-cache',
                'Accept' => 'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,q=0.5',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.82 Safari/537.36',
            );
        }

        $headers = array();
        foreach ($header as $k => $v) {
            $headers[] = $k . ': ' . $v;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($isPost) {
            //$postFields = http_build_query($postFields);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($isPost) {
            //$postFields = http_build_query($postFields);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }
        $timeOut = 300;
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception(curl_error($ch), '500');
        }
        return $response;
    }
}