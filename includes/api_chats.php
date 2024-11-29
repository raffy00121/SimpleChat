<?php

    global $DATA_OBJ, $DB, $info;

    $arr['userid'] = "";

    $refresh = false;
    if ($DATA_OBJ->data_type == "chats_refresh") {
        $refresh = true;
    }

    if (isset($DATA_OBJ->userid)) {

        $arr['userid'] = $DATA_OBJ->userid;
        $sql = "SELECT * FROM `users` WHERE `userid` = :userid LIMIT 1 ";
        $result = $DB->read($sql, $arr);

        if (is_array($result)) {
            $user = $result[0];

            $html_contacts_panel = ""; // initialize

            if (!$refresh) { // don't redisplay if we are refreshing the messages
                $html_contacts_panel = "
                    <h3>You are chatting with: </h3>
                    <p>$user->username</p>
                ";
            }

            // get the receiver
            $chat_receiver = $DB->getChatReceiver($user->userid);

            // find chat if exist
            $chat = "";
            try {
                $chat = $DB->chatFinder($user->userid, $_SESSION['userid']);
            } catch (Exception $e) {
                $info->chat_contact = "No chats found: " . $e->getMessage();
                $info->data_type = "error";
            }

            // find chat messages
            $chat_messages = "";
            if ($chat) {
                $chat_messages = $DB->getChatMessages($chat->chat_id);
            }

            $html_message = ""; // initialize messages section

            if (!$refresh) { // don't redisplay if we are refreshing the messages
                $html_message = "
                    <div id='messages_wrapper'>";
            }

            // the messages itself
            if ($chat_messages) {
                foreach ($chat_messages as $message) {
                    if ($message->receiver == $chat_receiver->userid) {
                        $html_message .= getMessageRight($message);
                    } else {
                        $html_message .= getMessageLeft($chat_receiver, $message);
                    }
                }
            }

            if (!$refresh) { // don't redisplay if we are refreshing the messages
                $html_message .= "
                </div>
                <div id='messages_inputs'>
                    <label for='message_file' id='file_input_label'>File</label>
                    <input type='file' id='message_file' style='display: none' >
                    <input type='text' onkeyup='enter_pressed(event)' id='message_text' placeholder='Enter your message here...' >
                    <input type='button' onclick='sendMessage(event)' id='send_message' value='SEND'>    
                </div>
                ";
            }

            $info->chat_contact = $html_contacts_panel;
            $info->messages = $html_message;
            $info->data_type = "chats"; // send to responseText
            if ($refresh) { // if we are refreshing, only refresh the messages section
                $info->messages = $html_message;
                $info->data_type = "chats_refresh";
            }
        } else {
            $info->chat_contact = "No chats found";
            $info->data_type = "error";
        }
    } else {

        // $_SESSION['userid'] // my userid
        // I will get chats that has my userid
        $all_chats = $DB->findAllMyChat($_SESSION['userid']);

        $html_previous_chats_panel = "";
        if (is_array($all_chats)) {
            $html_previous_chats_panel = "
            <h4>Previous Chats: </h4>
            <div>";

                foreach ($all_chats as $chat) {
                    $otherUser = $chat->sender;
                    if ($chat->sender == $_SESSION['userid']) {
                        $otherUser = $chat->receiver;
                    }
                    $user = $DB->getChatReceiver($otherUser);
                    $html_previous_chats_panel .= "
                        <div id='previous_chat_item' userid='$user->userid' onclick='startChat(event)'>
                            <h5>$user->username</h5>
                            <p>$chat->txt_message</p>
                        </div>
                    ";
                };
                
            $html_previous_chats_panel .= "
            </div>
            ";
        }

        $info->chat_contact = $html_previous_chats_panel;
        $info->data_type = "chats";
    }

    echo json_encode($info);






