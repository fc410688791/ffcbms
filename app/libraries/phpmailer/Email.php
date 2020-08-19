<?php 

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require "vendor/autoload.php";

class Email 
{

    protected static $email;

    /**
     * [__construct description]
     *
     * @Author leeprince:2019-08-07T11:26:40+0800
     */
    public function __construct()
    {
        if (! self::$email) {
            self::$email = new PHPMailer(true);

            try {
                self::$email->SMTPDebug = 0;
                self::$email->isSMTP();

                //这里使用我们第二步设置的stmp服务地址
                self::$email->Host = "smtp.qq.com";
                // self::$email->Host = "smtp.163.com";
                self::$email->SMTPAuth = true;

                self::$email->Username = "leeprince@foxmail.com";
                // self::$email->Username = "leeprincehz@163.com";
                //客户端授权密码
                self::$email->Password = "ikminbtmmkhzbahe"; 

                // 解决报超时
                self::$email->SMTPSecure = "ssl";
                self::$email->Port = 465;
                
                self::$email->CharSet = "utf-8";

                //设置邮箱的来源，邮箱与$mail->Username一致，名称随意
                self::$email->setFrom("leeprince@foxmail.com", 'prince');

                self::$email->isHTML(true);

                //设置收件的邮箱地址
                // self::$email->addAddress("");
                //设置回复地址，一般与来源保持一直
                // self::$email->addReplyTo("");
                // self::$email->Subject = "";
                // self::$email->Body = "";
                
            } catch (Exception $e) {
                throw new Exception("发送邮件初始化失败".$e->getMessage(), 1);
            }
        }
    }

    /**
     * [sendEmail 发送邮件]
     *
     * @Author leeprince:2019-08-07T11:26:45+0800
     * @return [type]                             [description]
     */
    public function sendEmail($to, $subject, $body, $cc = '', $from = '')
    {
        if ( ! $to || ! $subject || ! $body) {
            throw new Exception("发送邮件请求参数错误", 1);
        }
        if (! empty($from)) {
            self::$email->setFrom($from, $from);
        }
        try {
            if (is_string($to)) {
                self::$email->addAddress($to);
            } elseif(is_array($to)) {
                foreach ($to as $key => $value) {
                    self::$email->addAddress($value);
                }
            }
            if (is_string($cc)) {
                self::$email->addCC($to);
            } elseif(is_array($cc)) {
                foreach ($cc as $key => $value) {
                    self::$email->addCC($value);
                }
            }
            self::$email->Subject = $subject;
            self::$email->Body    = $body;
            self::$email->send();
        } catch (Exception $e) {
            throw new Exception("发送邮件失败".$e->getMessage(), 1);
        }
        return true;
    }
}











