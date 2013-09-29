$(document).ready(function() {
    $("#signin").click(function() {

        var obj =
        {
            login: 1,
            username: $("#username").val(),
            password: $("#password").val()
        };

        var convertJson = JSON.stringify(obj);

        $.post("php/makeAuth.php", { "obj": convertJson },
            function(data){
                if (data.isSuccess === 1)
                {
                    if (data.replyMessage === "authed")
                    {
                        window.location = "index.php";
                    }
                    else
                    {
                        if (data.typeOf === "login")
                        {
                            //Post message that login succeeded and redirect to the main page
                            console.log(data.replyMessage);
                            window.location = "index.php";
                        }
                        else
                        {
                            console.log(data.replyMessage);
                        }
                    }
                }
                else
                {
                    console.log("Login failed: " + data.replyMessage);
                }
            }, "json");
    });
});