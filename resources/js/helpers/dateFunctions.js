// if time zone used in database (data type dateTimeTz), stored date will look like '2019-09-10 16:00:00+00'
const cleanDate = dateStr => dateStr.split('+')[0];

const adjustedTimeInMilliseconds = dateStr => {
    const timeDiffInMilliSecs = new Date(dateStr).getTimezoneOffset() * 60 * 1000;
    const newTimeInMilliSecs = Date.parse(dateStr) - timeDiffInMilliSecs;
    return newTimeInMilliSecs;
}

export const dueBy = dateStr => {
    dateStr = cleanDate(dateStr);
    const newTimeInMilliSecs = adjustedTimeInMilliseconds(dateStr);
    const newDate = new Date(newTimeInMilliSecs);
    return getYearMonthDay(newDate) + ' ' + newDate.toTimeString().slice(0, 5);
}

export const getYearMonthDay = date => {
    return `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`;
}

const timer = (dateStr, secondsElapsed) => {
    dateStr = cleanDate(dateStr);
    return adjustedTimeInMilliseconds(dateStr) / 1000 - secondsElapsed;
}

const timeRemaining = (dueBy, secondsElapsed, startSeconds) => {
    const secondsRemaining = Math.floor(timer(dueBy, secondsElapsed) - startSeconds);
    const minute = 60;
    const hour = minute * 60;
    const day = hour * 24;
    const week = day * 7;
    const month = day * 31;
    const year = day * 365;
    let value;
    let unit;

    if (secondsRemaining > year || secondsRemaining < -year) { value = Math.ceil(secondsRemaining / year); unit = 'year'; }
    else if (secondsRemaining > month || secondsRemaining < -month) { value = Math.ceil(secondsRemaining / month); unit = 'month'; }
    else if (secondsRemaining > week || secondsRemaining < -week) { value = Math.ceil(secondsRemaining / week); unit = 'week'; }
    else if (secondsRemaining > day || secondsRemaining < -day) { value = Math.ceil(secondsRemaining / day); unit = 'day'; }
    else if (secondsRemaining > hour || secondsRemaining < -hour) { value = Math.ceil(secondsRemaining / hour); unit = 'hour'; }
    else { value = Math.ceil(secondsRemaining / minute); unit = 'minute'; }

    return {value, unit, secondsRemaining};
}

export const timeRemainingString = (dueBy, secondsElapsed, startSeconds) => {
    const {value, unit, secondsRemaining} = timeRemaining(dueBy, secondsElapsed, startSeconds);

    let outputStr;

    if (value > 0) outputStr = `due in under ${value} ${unit}${value === 1 ? '' : 's'}`;
    else if (value <= 0 && secondsRemaining > -60) outputStr = 'due now';
    else outputStr = `due over ${-value} ${unit}${-value === 1 ? '' : 's'} ago`;

    return outputStr;
}