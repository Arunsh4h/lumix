<?php
if ($_SERVER["REQUEST_METHOD"] <> "POST") 
	die("You can only reach this page by posting from the html form");

if(!session_id()) session_start();
if (isset($_REQUEST["ask_captcha"]) && isset($_SESSION["askme_code_captcha_register"]) && $_REQUEST["ask_captcha"] == $_SESSION["askme_code_captcha_register"] && !empty($_REQUEST["ask_captcha"]) && !empty($_SESSION["askme_code_captcha_register"])) {
	echo "ask_captcha_1";
}else if (isset($_REQUEST["ask_captcha"]) && isset($_SESSION["askme_code_captcha_login"]) && $_REQUEST["ask_captcha"] == $_SESSION["askme_code_captcha_login"] && !empty($_REQUEST["ask_captcha"]) && !empty($_SESSION["askme_code_captcha_login"])) {
	echo "ask_captcha_1";
}else if (isset($_REQUEST["ask_captcha"]) && isset($_SESSION["askme_code_captcha_password"]) && $_REQUEST["ask_captcha"] == $_SESSION["askme_code_captcha_password"] && !empty($_REQUEST["ask_captcha"]) && !empty($_SESSION["askme_code_captcha_password"])) {
	echo "ask_captcha_1";
}else if (isset($_REQUEST["ask_captcha"]) && isset($_SESSION["askme_code_captcha_post"]) && $_REQUEST["ask_captcha"] == $_SESSION["askme_code_captcha_post"] && !empty($_REQUEST["ask_captcha"]) && !empty($_SESSION["askme_code_captcha_post"])) {
	echo "ask_captcha_1";
}else if (isset($_REQUEST["ask_captcha"]) && isset($_SESSION["askme_code_captcha_category"]) && $_REQUEST["ask_captcha"] == $_SESSION["askme_code_captcha_category"] && !empty($_REQUEST["ask_captcha"]) && !empty($_SESSION["askme_code_captcha_category"])) {
	echo "ask_captcha_1";
}else if (isset($_REQUEST["ask_captcha"]) && isset($_SESSION["askme_code_captcha_question"]) && $_REQUEST["ask_captcha"] == $_SESSION["askme_code_captcha_question"] && !empty($_REQUEST["ask_captcha"]) && !empty($_SESSION["askme_code_captcha_question"])) {
	echo "ask_captcha_1";
}else if (isset($_REQUEST["ask_captcha"]) && isset($_SESSION["askme_code_captcha_message"]) && $_REQUEST["ask_captcha"] == $_SESSION["askme_code_captcha_message"] && !empty($_REQUEST["ask_captcha"]) && !empty($_SESSION["askme_code_captcha_message"])) {
	echo "ask_captcha_1";
}else if (isset($_REQUEST["ask_captcha"]) && isset($_SESSION["askme_code_captcha_comment"]) && $_REQUEST["ask_captcha"] == $_SESSION["askme_code_captcha_comment"] && !empty($_REQUEST["ask_captcha"]) && !empty($_SESSION["askme_code_captcha_comment"])) {
	echo "ask_captcha_1";
}else if (isset($_REQUEST["ask_captcha"]) && isset($_SESSION["askme_code_captcha_answer"]) && $_REQUEST["ask_captcha"] == $_SESSION["askme_code_captcha_answer"] && !empty($_REQUEST["ask_captcha"]) && !empty($_SESSION["askme_code_captcha_answer"])) {
	echo "ask_captcha_1";
}else if (isset($_REQUEST["ask_captcha"]) && isset($_SESSION["askme_code_captcha_custom"]) && $_REQUEST["ask_captcha"] == $_SESSION["askme_code_captcha_custom"] && !empty($_REQUEST["ask_captcha"]) && !empty($_SESSION["askme_code_captcha_custom"])) {
	echo "ask_captcha_1";
}else {
	echo "ask_captcha_0";
}
?>