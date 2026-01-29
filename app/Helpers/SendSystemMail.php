<?php
if (!function_exists('send_system_mail')) {
    function send_system_mail($to, $message, $subject, $html = false)
    {
        $headers[] = "From: SysInvAdmin <system@{$_SERVER['SERVER_NAME']}>";
        if ($html) {
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=iso-8859-1';
        } else {
            $message = wordwrap($message, 70, "\r\n");
        }

        mail($to, $subject, $message, implode("\r\n", $headers));
    }
}
?>