<?php
class adminMailController
{
    
    public function indexAction($request)
    {
        if (!AuthID or !AuthLogin or ADMIN != 'true') {
            header("Location: " . Site);
            exit;
        }
        $DB = new DB();
        
        $users  = $DB->selectAllAnd(array(
            'table' => 'users',
            'where' => array(
                '1' => '1'
            )
        ));
        $emails = '';
        foreach ($users as $user) {
            $emails = $emails . $user['login'] . ' |' . $user['email'] . '
';
        }
        
        $request['emails'] = $emails;
        
        require_once(Viewer);
        $view = new viewer();
        
        $request['category'] = 'adminMail';
        $request['login']    = AuthLogin;
        $request['Site']     = Site;
        $request['LANG']     = LANG;
        
        echo openCss('adminmail');
        $view->view('authorized', $request);
        
    }
    
    
    public function digestAction()
    {
        if (!AuthID or !AuthLogin or ADMIN != 'true') {
            header("Location: " . Site);
            exit;
        }
        $error = '';
        
        if (isset($_POST['emails']) and $_POST['emails'] != '') {
            $emails = $_POST['emails'];
        } else {
            $error .= ' Отсутствуют emails 
';
        }
        if (isset($_POST['subject']) and $_POST['subject'] != '') {
            $subject = $_POST['subject'];
        } else {
            $error .= ' Отсутствует тема 
';
        }
        if (isset($_POST['message']) and $_POST['message'] != '') {
            $message = $_POST['message'];
        } else {
            $error .= ' Отсутствует сообщение 
';
        }
        
        if ($error != '') {
            echo $error;
            die();
        }
        $DB = new DB();
        require_once(Smtp);
        $mailSMTP = new SendMailSmtpClass('support@dstraff.ru', 'Dstraff312!', 'ssl://smtp.mail.ru', 'Support', 465);
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n"; // кодировка письма
        $emails = explode("\r", $emails);
        if (!strripos($emails[0], '@')) {
            $error .= ' Отсутствуют emails ';
            echo $error;
            die();
        }
        $i = 0;
        $s = 0;
        foreach ($emails as $email) {
            if (strripos($email, '|')) {
                $email = explode("|", $email);
                $email = $email[1];
            }
            $email = str_replace(' ', '', $email);
            $email = str_replace("\r", '', $email);
            $email = str_replace("\n", '', $email);
            
            if ($email == '') {
                continue;
            }
            $i++;
            
            $result = $mailSMTP->send($email, $subject, $message, $headers);
            if ($result == true) {
                $status = 'send';
                $s++;
            } else {
                $status = $result;
            }
            $DB->insert(array(
                'table' => 'mailer_log',
                'set' => array(
                    'email' => $email,
                    'subject' => $subject,
                    'message' => $message,
                    'status' => $status
                )
            ));
            
        }
        
        echo 'ok|Успешно отправлено ' . $s . ' из ' . $i . ' писем';
        
    }
    
    
    
    
    
    
}