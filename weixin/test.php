<?php

    //>>>>>>>>>>>>处理curl api方法开始<<<<<<<<<<<<<<<<
       function createApiCall($url, $method, $headers, $data = array())
    {
        if ($method == 'PUT')
        {
            $headers[] = 'X-HTTP-Method-Override: PUT';
        }
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);//这是你想用PHP取回的URL地址。你也可以在用curl_init()函数初始化时设置这个选项。
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_HEADER, false);
        switch($method)
        {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($handle, CURLOPT_POST, true);
                curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data));// 传递一个作为HTTP “POST”操作的所有数据的字符串。
                break;
            case 'PUT':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');//当进行HTTP请求时，传递一个字符被GET或HEAD使用。为进行DELETE或其它操作是有益的，更Pass a string to be used instead of GET or HEAD when doing an HTTP request. This is useful for doing  or another, more obscure, HTTP request.
                curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'DELETE':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        $response = curl_exec($handle);
        return $response;
    }
    //>>>>>>>>>>>>处理curl api方法结束<<<<<<<<<<<<<<<<
