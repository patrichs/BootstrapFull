$(document).ready(function() {

    /* This bit returns the amount of users in the database */
    function checkForAuth()
    {

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
                    $.msgGrowl({type: "error", title: "Error", text: data.replyMessage})
                }
            }, "json");
    }

    checkForAuth();

    /* This updates the users table with all users found in the database */

    function updateUsersTable()
    {
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
                    //lame hack but whatever works
                    output += "<td>" + "<a href='#' class='btn btn-small btn-primary btn-edit-user' id=" +
                        data.amountOfRows[i].userid + "," + data.amountOfRows[i].username + "," +
                        data.amountOfRows[i].email + "><i class='icon-pencil icon-white'></i> Edit</a>   "
                        + "<a href='#' class='btn btn-small btn-warning btn-delete-user' id=" +
                        data.amountOfRows[i].userid + "><i class='icon-remove icon-white'></i> Delete</a>" + "</td>";
                    output += "</tr>";
                }

                $("#userstbody").append(output);

                $("#amountOfUsers").text(data.amountOfUsers);
            }, "json");
    }

    /* This updates the groups table with all groups found in the database */

    function updateGroupsTable()
    {
        $("#groupstbody").empty();
        $.post("php/returnAllGroups.php",
            function (data)
            {
                var output = "";
                for (var i in data.amountOfRows)
                {
                    output += "<tr>";
                    output += "<td>" + data.amountOfRows[i].groupid + "</td>";
                    output += "<td>" + data.amountOfRows[i].groupname + "</td>";
                    //lame hack but whatever works
                    output += "<td>" + "<a href='#' class='btn btn-small btn-primary btn-edit-group' id=" +
                        data.amountOfRows[i].groupid + "," + data.amountOfRows[i].groupname + "><i class='icon-pencil icon-white'></i> Edit</a>   "
                        + "<a href='#' class='btn btn-small btn-info btn-add-member-group' id=addusers," +
                        data.amountOfRows[i].groupid + "><i class='icon-plus-sign icon-white'></i> Add users</a>   "
                        + "<a href='#' class='btn btn-small btn-warning btn-delete-group' id=" +
                        data.amountOfRows[i].groupid + "><i class='icon-remove icon-white'></i> Delete</a>" + "</td>";
                    output += "</tr>";
                }

                $("#groupstbody").append(output);

                $("#amountOfGroups").text(data.amountOfGroups);
            }, "json");
    }

    /* Delete users */
    $(".btn-delete-user").live("click", function()
    {
        if (confirm("Are you sure you want to delete this user?"))
        {
            var obj =
            {
                objUserId: this.id
            };

            var convertJson = JSON.stringify(obj);

            $.post("php/deleteUserAccount.php", { "obj": convertJson },
                function(data){
                    if (data.isSuccess === 1)
                    {
                        $.msgGrowl({type: "success", title: "Success", text: data.replyMessage});
                        updateUsersTable();
                        writeToLog(2, "Delete event", "An administrator has deleted a user.")
                    }
                    else
                    {
                        $.msgGrowl({type: "error", title: "Error", text: data.replyMessage});
                    }
                }, "json");
        }
        else
        {
            $.msgGrowl({type: "warning", title: "Action Cancelled", text: "The user was NOT deleted."});
        }
    });

    /* Delete group */
    $(".btn-delete-group").live("click", function()
    {
        if (confirm("Are you sure you want to delete this group?"))
        {
            var obj =
            {
                objGroupId: this.id
            };

            var convertJson = JSON.stringify(obj);

            $.post("php/deleteGroup.php", { "obj": convertJson },
                function(data){
                    if (data.isSuccess === 1)
                    {
                        $.msgGrowl({type: "success", title: "Success", text: data.replyMessage});
                        updateGroupsTable();
                        writeToLog(5, "Delete event", "An administrator has deleted a group.")
                    }
                    else
                    {
                        $.msgGrowl({type: "error", title: "Error", text: data.replyMessage});
                    }
                }, "json");
        }
        else
        {
            $.msgGrowl({type: "warning", title: "Action Cancelled", text: "The group was NOT deleted."});
        }
    });

    /* Modifying a user */
    $(".btn-edit-user").live("click", function() {
        var splitIdString = this.id.split(",");

        $.msgbox("<p>Change the appropriate field and then click 'Save' to save all the changes.<br>Leave the password field blank to not change the password.</p>", {
            type    : "prompt",
            inputs  : [
                {type: "text", label: "Username: ", value: splitIdString[1], required: true},
                {type: "password", label: "Password: ", value: "", required: false},
                {type: "text", label: "E-mail: ", value: splitIdString[2], required: true},
                {type: "hidden", value: splitIdString[0] , required: true}
            ],
            buttons : [
                {type: "submit", value: "Save"},
                {type: "cancel", value: "Cancel"}
            ]
        }, function(Username, Password, Email, Userid) {
            if (Username.length > 0) {

                var obj =
                {
                    objUsername: Username,
                    objPassword: Password,
                    objEmail: Email,
                    objUserId: Userid
                };

                var convertJson = JSON.stringify(obj);

                $.post("php/changeUserData.php", { "obj": convertJson },
                    function(data){
                        if (data.isSuccess === 1)
                        {
                            $.msgGrowl({type: "success", title: "Success", text: data.replyMessage});
                            updateUsersTable();
                            writeToLog(1, "Modify event", "An administrator has modified a user.")
                        }
                        else
                        {
                            $.msgGrowl({type: "error", title: "Error", text: data.replyMessage});
                        }
                    }, "json");

            } else {
                $.msgGrowl({type: "warning", title: "Action Cancelled", text: "User data was NOT changed."});
            }
        });
    });

    /* Modifying a group */
    $(".btn-edit-group").live("click", function() {
        var splitIdString = this.id.split(",");

        $.msgbox("<p>Change the appropriate field and then click 'Save' to save all the changes.</p>", {
            type    : "prompt",
            inputs  : [
                {type: "text", label: "Group name: ", value: splitIdString[1], required: true},
                {type: "hidden", value: splitIdString[0] , required: true}
            ],
            buttons : [
                {type: "submit", value: "Save"},
                {type: "cancel", value: "Cancel"}
            ]
        }, function(Groupname, Groupid) {
            if (Groupname.length > 0) {

                var obj =
                {
                    objGroupname: Groupname,
                    objGroupId: Groupid
                };

                var convertJson = JSON.stringify(obj);

                $.post("php/changeGroupData.php", { "obj": convertJson },
                    function(data){
                        if (data.isSuccess === 1)
                        {
                            $.msgGrowl({type: "success", title: "Success", text: data.replyMessage});
                            updateGroupsTable();
                            writeToLog(4, "Modify event", "An administrator has modified a group.")
                        }
                        else
                        {
                            $.msgGrowl({type: "error", title: "Error", text: data.replyMessage});
                        }
                    }, "json");

            } else {
                $.msgGrowl({type: "warning", title: "Action Cancelled", text: "Group data was NOT changed."});
            }
        });
    });

    /* Adding a user */
    $(".btn-add-user").live("click", function() {

        $.msgbox("<p>Fill in all fields and click 'Add' to add a user.</p>", {
            type    : "prompt",
            inputs  : [
                {type: "text", label: "Username: ", value: "", required: true},
                {type: "password", label: "Password: ", value: "", required: true},
                {type: "text", label: "E-mail: ", value: "", required: true}
            ],
            buttons : [
                {type: "submit", value: "Add"},
                {type: "cancel", value: "Cancel"}
            ]
        }, function(Username, Password, Email) {
            if (Username.length > 0) {

                var obj =
                {
                    "register": 1,
                    "username": Username,
                    "password": Password,
                    "email": Email
                };

                var convertJson = JSON.stringify(obj);

                $.post("php/makeRegister.php", { "obj": convertJson },
                    function(data){
                        if (data.isSuccess === 1)
                        {
                            $.msgGrowl({type: "success", title: "Success", text: data.replyMessage});
                            updateUsersTable();
                            updateUsersGraph();
                            writeToLog(0, "Add event", "An administrator has added a user.")
                        }
                        else
                        {
                            $.msgGrowl({type: "error", title: "Error", text: data.replyMessage});
                        }
                    }, "json");

            } else {
                $.msgGrowl({type: "warning", title: "Action Cancelled", text: "No data was changed or added."});
            }
        });
    });

    /* Adding a group */
    $(".btn-add-group").live("click", function() {

        $.msgbox("<p>Fill in all fields and click 'Add' to add a group.</p>", {
            type    : "prompt",
            inputs  : [
                {type: "text", label: "Group name: ", value: "", required: true}
            ],
            buttons : [
                {type: "submit", value: "Add"},
                {type: "cancel", value: "Cancel"}
            ]
        }, function(groupname) {
            if (groupname.length > 0) {

                var obj =
                {
                    "addgroup": 1,
                    "groupname": groupname
                };

                var convertJson = JSON.stringify(obj);

                $.post("php/makeGroup.php", { "obj": convertJson },
                    function(data){
                        if (data.isSuccess === 1)
                        {
                            $.msgGrowl({type: "success", title: "Success", text: data.replyMessage});
                            updateGroupsTable();
                            writeToLog(3, "Add event", "An administrator has added a group.")
                        }
                        else
                        {
                            $.msgGrowl({type: "error", title: "Error", text: data.replyMessage});
                        }
                    }, "json");

            } else {
                $.msgGrowl({type: "warning", title: "Action Cancelled", text: "No data was changed or added."});
            }
        });
    });

    /* Adding a user to a group */
    $(".btn-add-member-group").live("click", function() {

        var splitIdString = this.id.split(",");

        $.msgbox("<p>Fill in all fields and click 'Add' to add a user to a group.</p>", {
            type    : "prompt",
            inputs  : [
                {type: "text", label: "Username: ", name: "addUserAC", id: "addUserACClass", required: true},
                {type: "hidden", value: splitIdString[1], required: true}
            ],
            buttons : [
                {type: "submit", value: "Add"},
                {type: "cancel", value: "Cancel"}
            ]
        }, function(username, groupid) {
            if (username.length > 0) {

                var obj =
                {
                    "addusertogroup": 1,
                    "username": username,
                    "groupid": groupid
                };

                var convertJson = JSON.stringify(obj);

                $.post("php/addUserToGroup.php", { "obj": convertJson },
                    function(data){
                        if (data.isSuccess === 1)
                        {
                            $.msgGrowl({type: "success", title: "Success", text: data.replyMessage});
                            updateUsersTable();
                            writeToLog(6, "Add event", "An administrator has added a user to a group.")
                        }
                        else
                        {
                            $.msgGrowl({type: "error", title: "Error", text: data.replyMessage});
                        }
                    }, "json");

            } else {
                $.msgGrowl({type: "warning", title: "Action Cancelled", text: "No data was changed or added."});
            }
        });

        /* Auto complete field to add a user to a group */

        $(".jquery-msgbox-inputs :input").autocomplete({
            source: "php/addUserToGroupAutoComplete.php",
            minLength: 1,
            select: function( event, ui ) {
                console.log( ui.item ?
                    ui.item.value :
                    "Nothing selected, input was " + this.value );
            }
        });
    });

    /* graph plotting members registered this month / members logged in this month */
    function updateUsersGraph()
    {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1;
        var yyyy = today.getFullYear();
        if(dd<10)
        {
            dd='0'+dd
        }
        if(mm<10)
        {
            mm='0'+mm
        }
        today = yyyy+'-'+mm+'-'+dd;

        $.post("php/graphUsersToday.php",
            function(data){
                var dataFlot = [];

                dataFlot.push([0, data.RegisteredToday]);
                dataFlot.push([1, data.LoggedInToday]);

                var dataset = [{ label: today, data: dataFlot, color: "#5482FF"}];
                var ticks = [[0, "New accounts"], [1, "Logins today"]];

                var options = {
                    series: {
                        bars: {
                            show: true
                        }
                    },
                    bars: {
                        align: "center",
                        barWidth: 0.2
                    },
                    xaxis: {
                        ticks: ticks
                    }
                };

                $.plot($("#todayUsersRegisteredLoggedInGraph"), dataset, options);

                console.log(dataFlot); //Debug
            }, "json");
    }

    function updateLogTable()
    {
        $("#logItems").empty();
        $.post("php/returnLogsDashboard.php", { objLimit: 1 },
            function (data)
            {
                var output = "";
                for (var i in data.amountOfRows)
                {
                    output += "<li><div class='news-item-date'>";
                    output += "<span class='news-item-day'>Event</span>";
                    output += "<span class='news-item-month'>" + data.amountOfRows[i].eventdate + "</span></div>";
                    output += "<div class='news-item-detail'>";
                    output += "<p class='news-item-title'>" + data.amountOfRows[i].eventtitle + "</p>";
                    output += "<p class='news-item-preview'>" + data.amountOfRows[i].eventdesc + "</p>";
                    output += "</div></li>";
                }

                /*
                 For reference how the HTML should look in the end:
                 <li>
                 <div class="news-item-date">
                 <span class="news-item-day">Event</span>
                 <span class="news-item-month">2013-10-12 16:34:51</span>
                 </div>

                 <div class="news-item-detail">
                 <a href="javascript:" class="news-item-title">Log Title</a>
                 <p class="news-item-preview">Logtext.</p>
                 </div>
                 </li> */

                $("#logItems").append(output);

                $("#amountOfLogs").text(data.amountOfLogs);
            }, "json");
    }

    function writeToLog(eventid, eventtitle, eventdesc)
    {
        var obj =
        {
            "objEventId": eventid,
            "objEventTitle": eventtitle,
            "objEventDesc": eventdesc
        };

        var convertJson = JSON.stringify(obj);

        $.post("php/writeToLog.php", { "obj": convertJson },
            function(data){
                if (data.isSuccess === 1)
                {
                    $.msgGrowl({type: "success", title: "Success", text: data.replyMessage});
                    updateLogTable();
                }
                else
                {
                    $.msgGrowl({type: "error", title: "Error", text: data.replyMessage});
                }
            }, "json");
    }

    /* Once page is loaded start update the users table */
    updateUsersTable();
    updateGroupsTable();
    updateUsersGraph();
    updateLogTable()

});