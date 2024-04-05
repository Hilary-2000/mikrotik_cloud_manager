// enable tooltips every where
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

// get the data from the database
var student_data = packages;

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
            col.push(element['package_id'] != null ? element['package_id'] : "");
            col.push(element['package_name'] != null ? element['package_name'] : "");
            col.push(element['amount_paid'] != null ? element['amount_paid'] : "");
            col.push(element['free_trial_period'] != null ? element['free_trial_period'] : "");
            col.push(element['package_period'] != null ? element['package_period'] : "");
            col.push(element['date_created'] != null ? element['date_created'] : "");
            col.push(element['date_updated'] != null ? element['date_updated'] : "");
            col.push(element['status'] != null ? element['status'] : 0);
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
    var tableData = "<table class='table'><thead><tr><th>#</th><th>Package Names</th><th>Amount</th><th>Free Trial Period</th><th>Package Period</th><th>Action</th></tr></thead><tbody>";
    if (finish < total) {
        fins = finish;
        //create a table of the 50 records
        var counter = start + 1;
        for (let index = start; index < finish; index++) {
            var status = arrays[index][7] == 1 ? "<span class='badge badge-success'> </span>" : "<span class='badge badge-danger'> </span>";
            tableData += "<tr><th scope='row'>" + counter + "</th><td> <a href='/Packages/View/" + arrays[index][0] + "' class='text-secondary'>" + ucwords(arrays[index][1]) + " " + status + "</a></td><td>" + arrays[index][2] + " " + "</td><td>" + ucwords(arrays[index][3]) + "</td><td>" + arrays[index][4] + "</td><td><a href='/Packages/View/" + arrays[index][0] + "' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this Package'><i class='ft-eye'></i></a></td></tr>";
            counter++;
        }
    } else {
        //create a table of the 50 records
        var counter = start + 1;
        for (let index = start; index < total; index++) {
            var status = arrays[index][7] == 1 ? "<span class='badge badge-success'> </span>" : "<span class='badge badge-danger'> </span>";
            tableData += "<tr><th scope='row'>" + counter + "</th><td> <a href='/Packages/View/" + arrays[index][0] + "' class='text-secondary'>" + ucwords(arrays[index][1]) + " " + status + "</a></td><td>" + arrays[index][2] + " " + "</td><td>" + ucwords(arrays[index][3]) + "</td><td>" + arrays[index][4] + "</td><td><a href='/Packages/View/" + arrays[index][0] + "' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this Package'><i class='ft-eye'></i></a></td></tr>";
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
        var cases = string.toLowerCase();
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