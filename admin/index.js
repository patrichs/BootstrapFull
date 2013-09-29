$(document).ready(function() {

    var obj =
    {
        "infopls": 1
    };

    var newObj = JSON.stringify(obj);

    $.post("php/returnAuthInfo.php", { "obj": newObj },
        function(data){
            if (data.isSuccess === 1)
            {
                $("#userLoggedIn").text(data.replyMessage);
            }
            else
            {
                console.log("Login failed: " + data.replyMessage);
            }
        }, "json");

    function ajaxCallFunction(){
        $("#userstbody").empty();
        $.post("php/returnAllUsers.php",
            function (data)
            {
                var output = "";
                for (var i in data.amountOfRows)
                {
                    output += "<tr>";
                    output += "<td>" + data.amountOfRows[i].userid + "</td>";
                    output += "<td>" + data.amountOfRows[i].username + "</td>";
                    output += "<td>" + data.amountOfRows[i].email + "</td>";
                    output += "<td>" + "<a href='javascript:;' class='btn btn-small btn-primary' id=" + data.amountOfRows[i].userid + ">Edit</a>" + "</td>";
                    output += "</tr>";
                }

                $("#userstbody").append(output);

                $("#amountOfUsers").text(data.amountOfUsers);
            }, "json");
    }

    ajaxCallFunction();
});