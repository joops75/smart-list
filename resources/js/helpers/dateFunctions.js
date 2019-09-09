export const dueBy = (dateStr) => {
    // if time zone used in database (data type dateTimeTz), stored date will look like '2019-09-10 16:00:00+00'
    dateStr = dateStr.split('+')[0];
    const timeDiffInMilliSecs = new Date(dateStr).getTimezoneOffset() * 60 * 1000;
    const newTimeInMilliSecs = Date.parse(dateStr) - timeDiffInMilliSecs;
    const newDate = new Date(newTimeInMilliSecs);
    return getYearMonthDay(newDate) + ' ' + newDate.toTimeString().slice(0, 5);
}

export const getYearMonthDay = date => {
    return `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`;
}