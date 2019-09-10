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

export const timer = (dateStr, secondsElapsed) => {
    dateStr = cleanDate(dateStr);
    return adjustedTimeInMilliseconds(dateStr) / 1000 - secondsElapsed;
}