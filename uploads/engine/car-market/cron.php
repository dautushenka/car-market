<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

ignore_user_abort(true);

Cache::ClearAllCache();

$base->SetWhere("exp_date", $base->timer->cur_time, "<", 'auto_autos');
$base->SetWhere("exp_date", 0, "!=", 'auto_autos');
$result = $base->Select('auto_autos', array("id", "model_id", "mark_id"), array("allow_site" => 1));

$id = array();
while ($row = $base->FetchArray($result))
{
    $id[] = $row['id'];
    $base->Update('auto_marks', array("auto_num" => "IF(auto_num=0, 0, auto_num-1)"), array('id' => $row['mark_id']), true);
    $base->Update('auto_models', array("auto_num" => "IF(auto_num=0, 0, auto_num-1)"), array('id' => $row['model_id']), true);
}
if ($id)
{
    $base->SetWhere('id', $id, "IN", 'auto_autos');
    $base->Update('auto_autos', array("allow_site" => 0), array());
}

$base->SetWhere("block_date", $base->timer->cur_time, "<", 'auto_autos');
$base->SetWhere("block_date", 0, "!=", 'auto_autos');
$base->Update('auto_autos', array("allow_block" => 0), array("allow_block" => 1));

Cache::SetHTMLCache('cron_time', time());

if ($auto->config['user_send_mail'])
{
    $mail_time = Cache::GetHTMLCache('mail_time');
    
    if ((time() - $mail_time) > 24 * 3600)
    {
        require_once DLE_CLASSES . 'mail.class.php';
        $mail = new dle_mail ($config);
        
        $time_begin = $base->timer->cur_time - 3600 * 24;
        
        $base->SetWhere('exp_date', array($time_begin, $base->timer->cur_time), 'BETWEEN');
        $res = $base->Select('auto_autos', array('author_id'), array('allow_site' => 1));
        
        $text = file_get_contents(ENGINE_DIR . "/car-market/mail_extend.txt");
        
        while ($row = $base->FetchArray($res))
        {
            $user = $db->super_query('SELECT email, user_group, name FROM ' . USERPREFIX . "_users WHERE user_id=" . $row['author_id']);
            
            if (!empty($user) && in_array($user['user_group'], $auto->config['user_int_allow_extend']))
            {
                $mail_text = str_replace("{%username%}", $user['name'], $text);
                $mail_text = str_replace("{%auto_link%}", $template->GetUrl(array("action" => 'auto',  "id" => $row['id'])), $mail_text);
                
                $mail->send ($user['email'], $auto->lang['mail_extend_subj'], $mail_text);
            }
        }
        Cache::SetHTMLCache('mail_time', time());
    }
}

die("ok");
?>