<?php
    require_once 'includes/_autoload.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once 'includes/head-tags.php'; ?>
<link rel="stylesheet" href="core/css/date-picker-modal-1.0.0.css">
<style>
    body {
        background-color: #F0F0F0;
    }
    .dashboard-header,
    .dashboard-body {
        background-color: #FFFFFFEF;
    }


    .dashboard-header {
        text-align: center;
        padding: 25px;
        position: relative
    }
    h1 {
        font-size: 36px;
        line-height: 1;
    }
    h3 {
        text-transform: uppercase;
        font-size: 22px;
    }

    .dashboard-header .datetime-container {
        position: absolute;
        bottom: 25px;
        right: 15px;
        text-align: right;
        font-size: 18px;
    }

    .dashboard-header img.header-logo {
        width: 80px;
        position: absolute;
        left: 18%;
        top: 15px;
    }

    .dashboard-body {
        width: 90%;
        margin: auto;
        margin-top: 40px;
        padding: 10px 30px 0 30px;
        border-radius: 5px;
    }

    .dashboard-body .table th, 
    .dashboard-body .table td {
        font-size:20px;
        vertical-align: middle;
    }

    .table tr td:nth-child(3),
    .table tr td:nth-child(4),
    .table tr th:nth-child(3),
    .table tr th:nth-child(4) {
        text-align: center;
    }

    .table thead th {
        border: none;
        border-bottom: 1px solid #A0A0A0;
    }

    .main-content {
        background-color: #FFF;
        padding: 10px 20px;
        min-height: 100vh
    }

    .bg-container {
        position: fixed;
        top: 0;
        left: 0;
        z-index: -10;
        width: 100%;
        height: 100vh;
    }

    .bg-cover {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 10;
        width: 100%;
        height: 100vh;
        background-color: #FFF;
        opacity: 0.2;
    }
    .bg-container img {
        position: fixed;
        top: 0;
        left: 0;
        z-index: -11;
        width: 100%;
    }

    

    /* .loading-cover {
        background-color: #0009;
        position: fixed;
        width: 100%;
        height: 100vh;
        z-index: 10;
        display: none;
        text-align: center;
        color: #FFF;
        padding-top: 30vh;
    }

    .loading-cover .loading-icon {
        -webkit-animation: rotation 1s infinite linear;
        font-size: 140px;
    }
    .loading-cover .loading-text {
        font-size: 40px;
    }
    .show {
        display: block !important;
    }

    @-webkit-keyframes rotation {
            0% {
                -webkit-transform: rotate(0deg);
            }

            40% {
                -webkit-transform: rotate(90deg);
            }

            60% {
                -webkit-transform: rotate(270deg);
            }

            100% {
                -webkit-transform: rotate(359deg);
            }
    } */
</style>
</head>
<body>
    <div class="bg-container">
        <div class="bg-cover"></div>
        <img src="./core/img/background/doctor-dashboard-background-2.jpg" class="bg-image" alt="">
    </div>

    <div class="container-fluid ">
      <div class="row">
          <div class="col-12 dashboard-header">
            <img src="./core/img/ollh-logo.gif" class='header-logo' alt="">
            <h1>Available Doctors for Clinic Consulation</h1>
            <h3>Our Lady of Lourdes Hospital</h3>
            <h4 class="datetime-container">08:00:00 AM <br> Monday, January 1, 2021</h4>
          </div>
      </div>
    </div>

    <div class="container-fluid dashboard-body">
        <div class="row">
            <div class="col-12">
            <table class="table">
                <thead>
                    <tr>
                        <th>Clinic No.</th>
                        <th>Doctor Name</th>
                        <th>Specialization</th>
                        <th>Schedule</th>
                    </tr>
                </thead>
                <tbody class='attendance-data'>
                    <tr>
                        <td>101</td>
                        <td>Dr. Juan Dela Cruz</td>
                        <td>Internal Medicine</td>
                        <td>12:00 NN - 3:00 PM</td>
                    </tr>
                    <tr>
                        <td>101</td>
                        <td>Dr. Juan Dela Cruz</td>
                        <td>Internal Medicine</td>
                        <td>12:00 NN - 3:00 PM</td>
                    </tr>
                    <tr>
                        <td>101</td>
                        <td>Dr. Juan Dela Cruz</td>
                        <td>Internal Medicine</td>
                        <td>12:00 NN - 3:00 PM</td>
                    </tr>
                    <tr>
                        <td>101</td>
                        <td>Dr. Juan Dela Cruz</td>
                        <td>Internal Medicine</td>
                        <td>12:00 NN - 3:00 PM</td>
                    </tr>
                    <tr>
                        <td>101</td>
                        <td>Dr. Juan Dela Cruz</td>
                        <td>Internal Medicine</td>
                        <td>12:00 NN - 3:00 PM</td>
                    </tr>
                    <tr>
                        <td>101</td>
                        <td>Dr. Juan Dela Cruz</td>
                        <td>Internal Medicine</td>
                        <td>12:00 NN - 3:00 PM</td>
                    </tr>
                    <tr>
                        <td>101</td>
                        <td>Dr. Juan Dela Cruz</td>
                        <td>Internal Medicine</td>
                        <td>12:00 NN - 3:00 PM</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</body>
<?php require_once 'includes/js-init.php'; ?>
<script src="./core/js/dashboard-clock.component.js"></script>
<script>
    // ToDo: Move to a separate JS File named dashboard-content.component.js
    let pageProps = {
        currentPage : 1,
        totalPages: 1,
        itemLimit: 10,
        pageDuration: 10000
    };

    let link = './core/ajax/attendance-dashboard-content.php';
    let callbackFunction = ((result) => {
        console.log(pageProps);
        // Attendance Content Properties
        let attendanceElem = document.querySelector('tbody.attendance-data');
        attendanceElem.innerHTML = '';
        result.content.record.forEach((data)=>{
            let tr = document.createElement('tr');
            
            data.forEach((col) => {
                let td = document.createElement('td');
                td.textContent = col;
                tr.appendChild(td);
            });
            attendanceElem.appendChild(tr);
        });

        // Pagination Properties
        pageProps.totalPages = result.content.totalPages;
        if (++pageProps['currentPage'] > pageProps['totalPages']) {
            pageProps['currentPage'] = 1;
        }
        setTimeout(
            (()=> { sendXHR(link, 'POST', {
                currentPage: pageProps['currentPage'],
                itemLimit: pageProps['itemLimit']
            }, callbackFunction) }), 
            pageProps['pageDuration']
        );
    });

    $(document).ready(function() {
        sendXHR(link, 'POST', {
            currentPage: pageProps['currentPage'],
            itemLimit: pageProps['itemLimit']
        }, callbackFunction);
    });
</script>
</html>