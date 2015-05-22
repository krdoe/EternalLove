<?php

define("TOKEN", "eternal");

/**
 * Function Name: el_check_signature
 * Description: To generate signature from "token", "timestamp" and "nonce",
 *              and verify with the one provided by the request.
 */
function el_check_signature()
{
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];
    $token = TOKEN;
    
    $verify_sig = array($token, $timestamp, $nonce);
    sort($verify_sig, SORT_STRING);
    $verify_sig = implode($verify_sig);
    $verify_sig = sha1($verify_sig);

    if($verify_sig == $signature)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * Function Name: el_handle_text
 * Description: To handle the text message.
 * @param post_obj the HTTP POST object
 */
function el_handle_text($post_obj)
{
    $user_msg = trim($post_obj->Content);
    if($user_msg == "time")
    {
        $ret_content = date("Y-m-d H:i:s",time());
    }
    else
    {
        $ret_content = $user_msg;
    }
    return $ret_content;
}

/**
 * Function Name: el_reply_msg
 * Description: To generate the XML message and reply.
 * @param post_obj the HTTP POST object
 */
function el_reply_msg($post_obj, $content, $type)
{
    $from_user = $post_obj->FromUserName;
    $to_user = $post_obj->ToUserName;
    $time = time();

    $xml_msg_tmp = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0</FuncFlag>
            </xml>";
    $xml_msg = sprintf($xml_msg_tmp, $from_user, $to_user, $time, $type, $content);
    
    /** Reply */
    echo "$xml_msg";
}

/**
 * Function Name: el_response_msg
 * Description: To process the user request.
 */
function el_response_msg()
{
    $post_str = $GLOBALS["HTTP_RAW_POST_DATA"];
    if(empty($post_str))
    {
        /** This should not happened. */
        $reply_content = "System Error! Please try again ...";
    }
    else
    {
        $post_obj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $msg_type = trim($post_obj->MsgType);
        switch($msg_type)
        {
            case "text":
                $reply_type = "text";
                $reply_content = handle_text($post_obj);
                break;
/*            case "event":
                $reply_type = "text";
                $reply_content = handle_event($post_obj);
                break; */
            default:
                $reply_type = "text";
                $reply_content = "Coming soon ...";
                break;
        }
        el_reply_msg($post_obj, $reply_content, $reply_type);
        exit (0);
    }
}


/** System starts from here. */

if(isset($_GET['echostr']))
{
    /** To validate request from Wechat. This is only called once at the first
     *  communication between Wechat server and our own server.
     */
    $echo_str = $_GET["echostr"];
    if(el_check_signature() === true)
    {
        echo $echo_str;
        exit (0);
    }
    // TODO: else, log the illegal connection.
    exit (1);
}

/** Process user message. */
el_response_msg();


?>
