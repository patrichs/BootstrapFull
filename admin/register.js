$(document).ready(function() {
    $("#signup").click(function() {

        var getUsername = $("#username").val();
        var getPassword = $("#password").val();
        var getEmail = $("#email").val();

        var obj =
        {
            "register": 1,
            "username": getUsername,
            "password": getPassword,
            "email": getEmail
        };

        var newObj = JSON.stringify(obj);

        $.post("php/makeRegister.php", { "obj": newObj },
            function(data){
                if (data.isSuccess === 1)
                {
                    if (data.replyMessage === "authed")
                    {
                        window.location = "index.html";
                    }
                    else
                    {
                        if (data.typeOf === "register")
                        {
                            //Post message that login succeeded and redirect to the main page
                            console.log(data.replyMessage);
                        }
                        else
                        {
                            console.log(data.replyMessage);
                        }
                    }
                }
                else
                {
                    console.log("Register failed: " + data.replyMessage);
                }
            }, "json");
    });
});