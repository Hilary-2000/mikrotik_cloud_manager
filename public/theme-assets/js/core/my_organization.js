// enable tooltips every where
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});
// get the data from the database
var student_data = data;
// get an object by id 
function cObj(id) {
    return document.getElementById(id);
}

function stopInterval(id) {
    clearInterval(id);
}

// Send data with get
function sendDataGet(method, file, object1, object2) {
    //make the loading window show
    object2.classList.remove("invisible");
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            object1.innerHTML = this.responseText;
            object2.classList.add("invisible");
        } else if (this.status == 500) {
            object2.classList.add("invisible");
            // cObj("loadings").classList.add("invisible");
            object1.innerHTML = "<p class='red_notice'>Cannot establish connection to server.<br>Try reloading your page</p>";
        }
    };
    xml.open(method, file, true);
    xml.send();
}

var rowsColStudents = [];
var rowsNCols_original = [];
var pagecountTransaction = 0; //this are the number of pages for transaction
var pagecounttrans = 1; //the current page the user is
var startpage = 0; // this is where we start counting the page number

// load the user data
window.onload = function () {
    // console.log(student_data);
    // get the arrays
    if (student_data.length > 0) {
        var rows = student_data;
        //create a column now
        for (let index = 0; index < rows.length; index++) {
            const element = rows[index];
            // create the collumn array that will take the row value
            var col = [];
            col.push(element['organization_id'] != null ? element['organization_id'] : "");
            col.push(element['organization_name'] != null ? element['organization_name'] : "");
            col.push(element['organization_address'] != null ? element['organization_address'] : "");
            col.push(element['organization_main_contact'] != null ? element['organization_main_contact'] : "");
            col.push(element['organization_database'] != null ? element['organization_database'] : "");
            col.push(element['organization_email'] != null ? element['organization_email'] : "");
            col.push(element['organization_status'] != null ? element['organization_status'] : "");
            col.push(element['organization_logo'] != null ? element['organization_logo'] : "");
            col.push(element['BusinessShortCode'] != null ? element['BusinessShortCode'] : "");
            col.push(element['date_joined'] != null ? element['date_joined'] : "");
            col.push(element['account_no'] != null ? element['account_no'] : "");
            col.push(element['wallet'] != null ? element['wallet'] : "");
            col.push(element['discount_type'] != null ? element['discount_type'] : "");
            col.push(element['discount_amount'] != null ? element['discount_amount'] : "");
            col.push(element['monthly_payment'] != null ? element['monthly_payment'] : "");
            // col.push(element['last_payment_date'] != null ? element['last_payment_date'] : "");
            // col.push(element['account_renewal_date'] != null ? element['account_renewal_date'] : "");
            rowsColStudents.push(col);
        }
        rowsNCols_original = rowsColStudents;
        cObj("tot_records").innerText = rows.length;
        // console.log(rowsNCols_original);
        //create the display table
        //get the number of pages
        cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsColStudents);

        //show the number of pages for each record
        var counted = rows.length / 50;
        pagecountTransaction = Math.ceil(counted);

        if (rowsColStudents.length > 0) {
            // cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
            // cObj("sort_by_name").addEventListener("click", sortByName);
            // cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
            // cObj("sort_by_expiration").addEventListener("click", sortByExpDate);

            // check and uncheck all fields that have been selected
            // checkedUnchecked();
        }

    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No results found!</p>";
        cObj("tablefooter").classList.add("invisible");
    }
}

function displayRecord(start, finish, arrays) {
    var total = arrays.length;
    //the finish value
    var fins = 0;

    //this is the table header to the start of the tbody
    var tableData = "<table class='table'><thead><tr><th><span  title='Sort by date registered' id='sort_by_reg_date' style='cursor:pointer;'># <i class='ft-chevron-down'></i></span></th><th><span id ='sort_by_name'   title='Sort by Client Name' style='cursor:pointer;'>Full Names <i class='ft-chevron-down'></i></span></th><th><span id ='sort_by_acc_number'   title='Sort by Account Number' style='cursor:pointer;'>Account Number <i class='ft-chevron-down'></i></span></th><th>Location</th><th><span  id ='sort_by_expiration'   title='Sort by Last Payment Date' style='cursor:pointer;'>Monthly Payment <i class='ft-chevron-down'></i></span></th><th>Action</th></tr></thead><tbody>";
    if (finish < total) {
        fins = finish;
        //create a table of the 50 records
        var counter = start + 1;
        for (let index = start; index < finish; index++) {
            var status = "<span class='badge badge-success'> </span>";
            var account_status = "<a href='/Organization/Deactivate/" + arrays[index][0] + "' class='btn btn-sm btn-danger text-dark text-bolder'  data-toggle='tooltip' title='Disable this Organization'><i class='ft-x'></i></a>";
            if (arrays[index][6] == 0) {
                // if the user is active
                status = "<span class='badge badge-danger'> </span>";
                account_status = "<a href='/Organization/Activate/" + arrays[index][0] + "' class='btn btn-sm btn-success text-dark text-bolder'  data-toggle='tooltip' title='Activate this Organization'><i class='ft-check'></i></a>";
            }
            if (arrays[index][12] != null && arrays[index][12] != "") {
                var mainData = arrays[index][12];
                if (arrays[index][12].substr(0, 1) == "\"") {
                    mainData = mainData.substr(1, mainData.length - 2);
                    mainData = mainData.replace(/\\/g, "");
                }
                if (hasJsonStructure(mainData)) {
                    var data = JSON.parse(mainData);
                    // get the client name
                    var fullname = "Null";
                    var id = 0;
                    for (let ind = 0; ind < rowsNCols_original.length; ind++) {
                        const element = rowsNCols_original[ind];
                        if (element[11] == data.client_acc) {
                            fullname = element[1];
                            id = element[0];
                        }
                    }
                }
            }
            tableData += "<tr><th scope='row'><input type='checkbox' class='actions_id' id='actions_id_"+arrays[index][11]+"'><input type='hidden' id='actions_value_"+arrays[index][11]+"' value='"+arrays[index][11]+"'> " + counter + "</th><td><a href='/Organization/View/" + arrays[index][0] + "' class='text-secondary'>" + ucwords(arrays[index][1]) + " " + status + "</a><br><small class='text-gray d-none d-xl-block'>" + (discount) + "</small></td><td>" + arrays[index][10].toUpperCase() + " </td><td>" + ucwords(arrays[index][2]) + "<br></td><td>" + arrays[index][14] + "</td><td><a href='/Organization/View/" + arrays[index][0] + "' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this User'><i class='ft-eye'></i></a> "+account_status+"</td></tr>";
            counter++;
        }
    } else {
        //create a table of the 50 records
        var counter = start + 1;
        for (let index = start; index < total; index++) {
            var status = "<span class='badge badge-success'> </span>";
            var account_status = "<a href='/Organization/Deactivate/" + arrays[index][0] + "' class='btn btn-sm btn-danger text-dark text-bolder'  data-toggle='tooltip' title='Disable this Organization'><i class='ft-x'></i></a>";
            if (arrays[index][6] == 0) {
                // if the user is active
                status = "<span class='badge badge-danger'> </span>";
                account_status = "<a href='/Organization/Activate/" + arrays[index][0] + "' class='btn btn-sm btn-success text-dark text-bolder'  data-toggle='tooltip' title='Activate this Organization'><i class='ft-check'></i></a>";
            }
            var discount = "";
            if (arrays[index][12] != null && arrays[index][12] != "") {
                var mainData = arrays[index][12];
                if (arrays[index][12].substr(0, 1) == "\"") {
                    mainData = mainData.substr(1, mainData.length - 2);
                    mainData = mainData.replace(/\\/g, "");
                }
                
                if (mainData == "number") {
                    discount = "Kes "+arrays[index][13];
                }else{
                    discount = arrays[index][13]+"%";
                }
            }
            tableData += "<tr><th scope='row'><input type='checkbox' class='actions_id' id='actions_id_"+arrays[index][11]+"'><input type='hidden' id='actions_value_"+arrays[index][11]+"' value='"+arrays[index][11]+"'> " + counter + "</th><td><a href='/Organization/View/" + arrays[index][0] + "' class='text-secondary'>" + ucwords(arrays[index][1]) + " " + status + "</a><br><small class='text-gray d-none d-xl-block'>" + (discount) + "</small></td><td>" + arrays[index][10].toUpperCase() + " </td><td>" + ucwords(arrays[index][2]) + "<br></td><td>" + arrays[index][14] + "</td><td><a href='/Organization/View/" + arrays[index][0] + "' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this User'><i class='ft-eye'></i></a> "+account_status+"</td></tr>";
            counter++;
        }
        fins = total;
    }

    tableData += "</tbody></table>";
    //set the start and the end value
    cObj("startNo").innerText = start + 1;
    cObj("finishNo").innerText = fins;
    //set the page number
    cObj("pagenumNav").innerText = pagecounttrans;
    // set tool tip
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    return tableData;
}
function ucwords(string) {
    var cases = string.toLowerCase().split(" ");
    // split the string to get the number of words present
    var final_word = "";
    for (let index = 0; index < cases.length; index++) {
        const element = cases[index];
        final_word += element.substr(0, 1).toUpperCase() + element.substr(1) + " ";
    }
    return final_word.trim();
}
function ucword(string) {
    if (string != null) {
        var cases = string+"".toLowerCase();
        // split the string to get the number of words present
        var final_word = cases.substr(0, 1).toUpperCase() + cases.substr(1);
        return final_word.trim();
    }
    return "";
}
function hasJsonStructure(str) {
    if (typeof str !== 'string') return false;
    try {
        const result = JSON.parse(str);
        const type = Object.prototype.toString.call(result);
        return type === '[object Object]'
            || type === '[object Array]';
    } catch (err) {
        return false;
    }
}
// fornat the date we are given
function setDate(string) {
    string = string.toString();
    var year = string.substr(0, 4);
    var month = string.substr(4, 2) - 1;
    var day = string.substr(6, 2);
    var hour = string.substr(8, 2);
    var min = string.substr(10, 2);
    var sec = string.substr(12, 2);
    const d = new Date(year, month, day, hour, min, sec);
    var hours = d.getHours() > 9 ? d.getHours() : "0" + d.getHours();
    var minutes = d.getMinutes() > 9 ? d.getMinutes() : "0" + d.getMinutes();
    var seconds = d.getSeconds() > 9 ? d.getSeconds() : "0" + d.getSeconds();
    return getDays(d.getDay()) + " " + d.getDate() + " " + getMonths(d.getMonth()) + " " + d.getFullYear() + " @ " + hours + ":" + minutes + ":" + seconds;
}
function getMonths(month) {
    if (month == 0) {
        return "Jan";
    } else if (month == 1) {
        return "Feb";
    } else if (month == 2) {
        return "Mar";
    } else if (month == 3) {
        return "Apr";
    } else if (month == 4) {
        return "May";
    } else if (month == 5) {
        return "Jun";
    } else if (month == 6) {
        return "Jul";
    } else if (month == 7) {
        return "Aug";
    } else if (month == 8) {
        return "Sep";
    } else if (month == 9) {
        return "Oct";
    } else if (month == 10) {
        return "Nov";
    } else if (month == 11) {
        return "Dec";
    }
}
function getDays(days) {
    if (days == 0) {
        return "Sun";
    } else if (days == 1) {
        return "Mon";
    } else if (days == 2) {
        return "Tue";
    } else if (days == 3) {
        return "Wed";
    } else if (days == 4) {
        return "Thur";
    } else if (days == 5) {
        return "Fri";
    } else if (days == 6) {
        return "Sat";
    }
}