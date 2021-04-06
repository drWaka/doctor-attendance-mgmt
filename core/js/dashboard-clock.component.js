window.onload = (() => {
    // System Clock
    setInterval(() => {
        let dateTimeElem = document.querySelector('.datetime-container');
        let dateTime = new Date();

        let timeHr = '', timeMeridian = '';
        if (dateTime.getHours() == 0) {
            timeHr = '12';
            timeMeridian = 'AM';
        } else if (dateTime.getHours() > 12) {
            timeHr = dateTime.getHours() - 12;
            timeMeridian = 'PM';
        } else {
            timeHr = dateTime.getHours();
            timeMeridian = 'AM';
        }

        let day = '', month = '', date = '';
        switch (dateTime.getDay()) {
            case 0 :
                day = 'Sunday';
                break;
            case 1 :
                day = 'Monday';
                break;
            case 2 :
                day = 'Tuesday';
                break;
            case 3 :
                day = 'Wednesday';
                break;
            case 4 :
                day = 'Thursday';
                break;
            case 5 :
                day = 'Friday';
                break;
            case 6 :
                day = 'Saturday';
                break;
        }
        switch (dateTime.getMonth()) {
            case 0 :
                month = 'January';
                break;
            case 1 :
                month = 'February';
                break;
            case 2 :
                month = 'March';
                break;
            case 3 :
                month = 'April';
                break;
            case 4 :
                month = 'May';
                break;
            case 5 :
                month = 'June';
                break;
            case 6 :
                month = 'July';
                break;
            case 7 :
                month = 'August';
                break;
            case 8 :
                month = 'September';
                break;
            case 9 :
                month = 'October';
                break;
            case 10 :
                month = 'November';
                break;
            case 11 :
                month = 'December';
                break;
        }
        dateTimeElem.innerHTML = `
            ${timeHr}:${dateTime.getMinutes()}:${dateTime.getSeconds()} ${timeMeridian} <br>
            ${day} ${month} ${dateTime.getDate()}, ${dateTime.getFullYear()}
        `;
    }, 1000);
})