<?php

class popupController
{
    
    public function indexAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        $DB = new DB();
        
        $request = $DB->selectAllAnd(array(
            'table' => 'users',
            'where' => array(
                'id' => AuthID
            )
        ));
        
        if ($request[0]['newNews'] == 'Y') {
            echo "Добавлена новая новость.<br><br> <a href = '" . Site . "/news/'>Просьба ознакомится.</a>";
            exit;
        }
        
        if ($request[0]['newSupport'] == 'Y') {
            echo "Получен ответ в тех. поддержке.<br><br> <a href = '" . Site . "/support/'>Просьба ознакомится.</a>";
            exit;
        }
        
        if ($request[0]['newDomains'] == 'Y') {
            echo "Статус в доменах изминен.<br><br> <a href = '" . Site . "/domains/'>Просьба ознакомится.</a>";
            exit;
        }
        
        if ($request[0]['newTrackers'] == 'Y') {
            echo "Статус в потоках изминен.<br><br> <a href = '" . Site . "/trackers/'>Просьба ознакомится.</a>";
            exit;
        }
        
        
        $request['domains'] = $DB->selectAllAnd(array(
            'table' => 'domains',
            'where' => array(
                'uid' => AuthID
            )
        ));
        
        
        if (!isset($request['domains'][0]['id'])) {
            echo "<span style = 'font-size:12px;'>Внимание, у Вас нет припаркованых доменов, Вы не можете создавать потоки.
	<br><br> <a href = '" . Site . "/domains/'>Просьба перейти в мои домены.</a></span>";
            exit;
        }
        
        
        
        
        
        echo "null";
        exit;
    }
    
}