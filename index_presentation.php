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
        background-color: #D0D0D0;
    }

    .dashboard-header {
        text-align: center;
        padding: 25px;
        position: relative;
        color: #FFF !important;
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
        text-align: center;
        margin-top: 15px;
    }

    .dashboard-header img.header-logo {
        width: 135px;
        position: absolute;
        left: 18%;
        top: 15px;
    }

    .bg-container {
        position: absolute;
        top: 0;
        left: 0;
        z-index: -10;
        width: 100%;
        height: 157px;
        overflow: hidden;
    }

    .bg-cover {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 10;
        width: 100%;
        height: 157px;
        background-color: #000000FE;
        opacity: 0.2;
    }
    .bg-container img {
        position: relative;
        top: 0;
        left: 0;
        z-index: -11;
        width: 100%;
    }

    .dashboard-body {
        background-color: #FFF;
        width: 90%;
        margin: auto;
        margin-top: 40px;
        padding: 10px 30px 0 30px;
        border-radius: 5px;
        min-height: 620px;
        overflow: hidden;
    }

    .dashboard-body .table th, 
    .dashboard-body .table td {
        font-size:20px;
        vertical-align: middle;
    }

    .table tr td:nth-child(4),
    .table tr th:nth-child(4) {
        text-align: center;
    }

    .table thead th {
        border: none;
        border-bottom: 1px solid #A0A0A0;
    }
    
    .table tbody, .table thead { position: relative; }
    .table tr { 
        position: absolute; 
        width: calc(100% - 30px);
        display: block;
    }
    .table tr td, .table tr th {
        display: inline-block;
    }

    .table tr td:nth-child(1), .table tr th:nth-child(1) { width: 14.60% }
    .table tr td:nth-child(2), .table tr th:nth-child(2) { width: 39.60% }
    .table tr td:nth-child(3), .table tr th:nth-child(3) { width: 19.60% }
    .table tr td:nth-child(4), .table tr th:nth-child(4) { width: 24.60% }
    
    .table tbody tr { 
        left: 1700px; 
        transition-duration: 1s;
    }
    .table tbody tr:nth-child(1) { margin-top: calc(1 * 55px); }
    .table tbody tr:nth-child(2) { margin-top: calc(2 * 55px); }
    .table tbody tr:nth-child(3) { margin-top: calc(3 * 55px); }
    .table tbody tr:nth-child(4) { margin-top: calc(4 * 55px); }
    .table tbody tr:nth-child(5) { margin-top: calc(5 * 55px); }
    .table tbody tr:nth-child(6) { margin-top: calc(6 * 55px); }
    .table tbody tr:nth-child(7) { margin-top: calc(7 * 55px); }
    .table tbody tr:nth-child(8) { margin-top: calc(8 * 55px); }
    .table tbody tr:nth-child(9) { margin-top: calc(9* 55px); }
    .table tbody tr:nth-child(10) { margin-top: calc(10 * 55px); }

    .table tbody tr:nth-child(1) td { border-top: none !important; }
    
    .main-content {
        background-color: #FFF;
        padding: 10px 20px;
        min-height: 100vh
    }

    tr.show { left: 15px !important; }
    tr.pushIn {
        animation-name: PushIn;
        animation-duration: 2s;
    }
    tr.pushOut {
        left: -1400px;
        animation-name: PushOut;
        animation-duration: 2s;
    }
    

    /* Push Out / Push In animation */
    @-webkit-keyframes PushOut {
        from { left: 0px; }
        to { left: -1700px; }
    }
    @-webkit-keyframes PushIn {
        from { left: 1700px; }
        to { left: 15px; }
    }

    /* Ads Overlay CSS */
    .ads-overlay {
        width: 35%;
        position: fixed;
        bottom: 0px;
        right: 8px;
        z-index: 11;
        display: block;
        transition-duration: 1s;
    }
    .ads-overlay.minimized {
        width: 15%;
    }

    .ads-overlay video {
        width: 100%;
        height: auto;
        margin: auto;
    }

</style>
</head>
<body>
    <div class="ads-overlay">
        <video width="320" height="240" controls>
            <source src="" type="video/mp4">
            Video Unsupported in your browser.
        </video>
    </div>

    <div class="bg-container">
        <div class="bg-cover"></div>
        <img src="./core/img/background/doctor-dashboard-background-2.jpg" class="bg-image" alt="">
    </div>

    <div class="container-fluid ">
      <div class="row">
          <div class="col-12 dashboard-header">
            <img src="./core/img/ollh-logo-white-trimmed.gif" class='header-logo' alt="">
            <h3>Our Lady of Lourdes Hospital</h3>
            <h1>Available Doctors for Clinic Consultation Today</h1>
            <h3 class="datetime-container">08:00:00 AM &minus; Monday, January 1, 2021</h3>
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
                        <th>Clinic Hours</th>
                    </tr>
                </thead>
                <tbody class='attendance-data'>
                    <?php
                        $attendance = EmployeeAttendance::filter(array(
                            "isOnBoard" => 1,
                            "date" => date('Y-m-d')
                        ));
                
                        $startNode = 0; 
                        $limitNode = 10;
                        for ($i = $startNode ; $i < $limitNode ; $i++) {
                            if (isset($attendance[$i])) {
                                $schedule = EmployeeClinicSchedule::filter(array(
                                    "employeeId" => $attendance[$i]['PK_employee'],
                                    "day" => date('D')
                                ));
                                $timeIn = date('h:i A', strtotime($schedule[0]['time_start']));
                                $timeOut = date('h:i A', strtotime($schedule[0]['time_end']));

                                $clinicHours = '-';
                                if ($timeIn != '12:00 AM' && $timeOut != '12:00 AM') {
                                    $clinicHours = "{$timeIn} - {$timeOut}";
                                }

                                echo "
                                    <tr class='show' data-employee-no='{$attendance[$i]['PK_employee']}'>
                                        <td>" . $attendance[$i]['clinic'] . "</td>
                                        <td>" . strtoupper($attendance[$i]['name']) . "</td>
                                        <td>" . strtoupper($attendance[$i]['department']) . "</td>
                                        <td>{$clinicHours}</td>
                                    </tr>
                                ";
                            }
                        }
                    ?>
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
        totaRecords: 1,
        itemLimit: 1,
        pageDuration: 5000
    };

    let videoAds = {
        currentNode: 1,
        totalNodes: 1,
        nodes: []
    }

    let link = './core/ajax/attendance-dashboard-content.php';
    let callbackFunction = ((result) => {
        // Attendance Content Properties
        let attendanceElem = document.querySelector('tbody.attendance-data');
        let existingTr = attendanceElem.querySelectorAll('tr');

        result.content.record.forEach((data)=>{
            // Determine if the data is not yet included at the Table
            let isIncluded = false;
            for (let i = 0 ; i < existingTr.length ; i++) {
                isIncluded = isIncluded || existingTr[i].getAttribute('data-employee-no') == data[4];
            }
            console.log(isIncluded);
            if (isIncluded !== true) {
                // Remove Old Attendance Row
                if (existingTr.length == 10) {
                    existingTr[0].classList.remove('show');
                    existingTr[0].classList.add('pushOut');
                }
                setTimeout(() => {
                    if (existingTr.length == 10) attendanceElem.removeChild(existingTr[0]);
                    
                    let tr = document.createElement('tr');
                    tr.setAttribute('data-employee-no', data[4]);
                    for (let i = 0 ; i <= 3 ; i++) {
                        let td = document.createElement('td');
                        td.textContent = data[i];
                        tr.appendChild(td);
                    }
                    attendanceElem.appendChild(tr);
                    tr.classList.add('pushIn');
                    setTimeout(() => { tr.classList.add('show'); }, 2000);
                }, 1500);
            }
        });

        // Pagination Properties
        pageProps.totaRecords = result.content.totaRecords;
        if (++pageProps['currentPage'] > pageProps['totaRecords']) {
            pageProps['currentPage'] = 1;
        }
        setTimeout(
            (()=> { 
                sendXHR(link, 'POST', {
                    currentPage: pageProps['currentPage'],
                    itemLimit: pageProps['itemLimit']
                }, callbackFunction) 
            }), 
            pageProps['pageDuration']
        );

        // Clean the dashboard if the attendance records is less than 10
        if (pageProps.totaRecords < 10) {
            sendXHR(link, 'POST', {
                currentPage: 1,
                itemLimit: 10
            }, (result) => {
                // Attendance Content Properties
                let attendanceElem = document.querySelector('tbody.attendance-data');
                let existingTr = attendanceElem.querySelectorAll('tr');

                for (let i = 0 ; i < existingTr.length ; i++) {
                    let log = result.content.record.filter((node) => {
                        return node[4] == existingTr[i].getAttribute('data-employee-no');
                    });
                    console.log(log.length);
                    if (log.length == 0) {
                        existingTr[i].classList.remove('show');
                        existingTr[i].classList.add('pushOut');
                        setTimeout(() => { attendanceElem.removeChild(existingTr[i]); }, 1500);
                    }
                }
            });
        }
    });

    $(document).ready(function() {
        setTimeout(() => {
            sendXHR(link, 'POST', {
                currentPage: pageProps['currentPage'],
                itemLimit: pageProps['itemLimit']
            }, callbackFunction);
        }, pageProps['pageDuration']);
    });

    
    function loadVideo(fileName) {
        let adsVideoOverlay = document.querySelector('.ads-overlay');
        let adsVideo = adsVideoOverlay.querySelector('video');
        let adsVideoSrc = adsVideoOverlay.querySelector('video source');
        
        adsVideoSrc.setAttribute('src', './core/videos/_/' + fileName);
        adsVideo.load();
        
        setTimeout(() => {
            adsVideoOverlay.style.display = 'block';
            adsVideo.play()
            console.log(adsVideo.duration);
            
            videoAds.currentNode = (videoAds.currentNode + 1) > videoAds.totalNodes
                ? 1
                : (videoAds.currentNode + 1);
            console.log(videoAds);
            setTimeout(() => { loadVideo(videoAds.nodes[(videoAds.currentNode - 1)][0]); }, adsVideo.duration * 1000);
        }, 2000);
    }

    // Ads Overlay JS
    $(document).ready(function() {
        sendXHR(
            './core/ajax/video-files-content.php', 
            'GET', 
            {}, 
            ((result) => {
                console.log({result});
                videoAds.totalNodes = result.content.length;
                videoAds.currentNode = 1;
                videoAds.nodes = result.content;

                loadVideo(videoAds.nodes[(videoAds.currentNode - 1)][0]);
            })
        );
        

        let body = document.querySelector('body');
        
        let adsVideoOverlay = document.querySelector('.ads-overlay');
        body.addEventListener('mouseover', () => { adsVideoOverlay.classList.add('minimized'); });
        body.addEventListener('mouseout', () => { adsVideoOverlay.classList.remove('minimized'); });

        // let delayTime = 5 * 1000 * 60;
        // delayTime = 5000;
        
        // setTimeout(() => {
            
        // }, delayTime);
    });
</script>
</html>