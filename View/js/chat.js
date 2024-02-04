window.setInterval(refreshMessages, 2000);
function refreshMessages(){
    let outgoing_email= document.getElementById("o_mail").value;
    let incoming_email = document.getElementById("i_mail").value;
    let sessionId = document.getElementById("sessionId").value;
    let problemId = document.getElementById("problem").value;

    $.ajax({
        url: "/Controller/UpdateChatsAjaxTeacher.php",
        method: "POST",
        data:{
            outgoing_email: outgoing_email,
            incoming_email: incoming_email,
            sessionId: sessionId,
            problemId: problemId,
        },
        success: function(response) {
            let messages_aux= JSON.parse(response);
            let messages= [];
            for(let i=0; i < messages_aux.length; i++){
                messages.push(messages_aux[i]['msg']);
            }

            var text = $.trim($('.messages').text());//to remove the leading and trailing whitespace only

            screenMessages = text.split('\n ');
            let trim_screenMessages =[];
            for(let i=0; i < screenMessages.length; i++){
                trim_screenMessages.push(screenMessages[i].trimStart());
            }
            //console.log("Trim message(Screen): " + trim_screenMessages);//Mensajes en la pantalla
            //console.log("BD messages (DataBase): " +  messages);//Mensajes en la BD.
            //messages.push("PRUEBA");
            let difference = messages.filter(x => !trim_screenMessages.includes(x));//https://stackoverflow.com/questions/1187518/how-to-get-the-difference-between-two-arrays-in-javascript
            //console.log("Difernecias:" + difference);
            messages_n= [];
            repeated_messages={};

            trim_screenMessages_n=[];
            repeated_trim_screenMessages={};

            trim_screenMessages.push(difference);

            for(let i = 0; i < messages.length; i++){

                if(messages_n.includes(messages[i]))
                {
                    if(Object.keys(repeated_messages).includes(messages[i])){

                        repeated_messages[messages[i]] = repeated_messages[messages[i]] + 1;

                    }
                    else{
                        repeated_messages[messages[i]] = 1;

                    }
                }
                else{ messages_n.push(messages[i]); }
            }

            for(let i = 0; i < trim_screenMessages.length; i++){

                if(trim_screenMessages_n.includes(trim_screenMessages[i]))
                {
                    if(Object.keys(repeated_trim_screenMessages).includes(trim_screenMessages[i])){

                        repeated_trim_screenMessages[trim_screenMessages[i]] = repeated_trim_screenMessages[trim_screenMessages[i]] + 1;

                    }
                    else{
                        repeated_trim_screenMessages[trim_screenMessages[i]] = 1;

                    }
                }
                else{ trim_screenMessages_n.push(trim_screenMessages[i]); }
            }

            for (const [key, value] of Object.entries(repeated_messages)) {
                //console.log(key, value);
                if (Object.keys(repeated_trim_screenMessages).includes(key)){
                    if(repeated_messages[key] === repeated_trim_screenMessages[key]){
                        //Todo correcto
                    }
                    else if ( repeated_messages[key] > repeated_trim_screenMessages[key]){
                        $('.messages').append('<div class="other-t"><p>\n '+key+'</p></div>');
                    }
                }
                else{
                    //Repetida en la BD, pero no en la Screen
                    //console.log("Mensaje repetido: "+ repeated_messages[key]);

                    $('.messages').append('<div class="other-t"><p>\n '+key+'</p></div>');
                }
            }
            if(difference.length > 0) {
                $('.messages').append('<div class="other-t"><p>\n '+difference+'</p></div>');
            }
        },
    })
}