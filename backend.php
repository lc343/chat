<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "12345678";
$dbname = "chat";
$store_num = 10;
$display_num = 10;

// ���󱨸�
//error_reporting(E_ALL);
error_reporting(E_ALL ^ E_DEPRECATED);

 // ͷ����Ϣ
header("Content-type: text/xml");
header("Cache-Control: no-cache");

//����mysql
$dbconn = mysql_connect($dbhost,$dbuser,$dbpass);
mysql_select_db($dbname,$dbconn);

//Ϊ���ײ�����������,����Ϊ�����е�ÿ����������һ������,ÿ���������������еĲ���ֵ��Ϊ���Լ���ֵ
//foreach�����������е�POST����,����Ϊÿ����������һ������,���Ҹ�����ֵ
foreach($_POST as $key => $value){
	$$key = mysql_real_escape_string($value, $dbconn);
}

//�����κδ�����ʾ,�ж�action�Ƿ����� postmsg
if(@$action == "postmsg"){
	//��������
	mysql_query("INSERT INTO messages (`user`,`msg`,`time`) 
	             VALUES ('$name','$message',".time().")",$dbconn);
	//ɾ������(��Ϊ����Ĭ��ֵ�洢10������)
	mysql_query("DELETE FROM messages WHERE id <= ".
				(mysql_insert_id($dbconn)-$store_num),$dbconn);
}

//��ѯ����
$messages = mysql_query("SELECT user,msg
						 FROM messages
						 WHERE time>$time
						 ORDER BY id ASC
						 LIMIT $display_num",$dbconn);
//�Ƿ����¼�¼
if(mysql_num_rows($messages) == 0) $status_code = 2;
else $status_code = 1;

//����xml���ݽṹ
echo "<?xml version=\"1.0\"?>\n";
echo "<response>\n";
echo "\t<status>$status_code</status>\n";
echo "\t<time>".time()."</time>\n";
if($status_code == 1){ //�����м�¼
	while($message = mysql_fetch_array($messages)){
		$message['msg'] = htmlspecialchars(stripslashes($message['msg']));
		echo "\t<message>\n";
		echo "\t\t<author>$message[user]</author>\n";
		echo "\t\t<text>$message[msg]</text>\n";
		echo "\t</message>\n";
	}
}
echo "</response>";
?>