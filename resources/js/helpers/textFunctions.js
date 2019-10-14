export const excerpt = (text, limit) => {
    if (text.length <= limit) {
        return text;
    }

    return `${text.slice(0, limit - 3)}...`;
}

export const capitalizeFirstLetter = text => {
    return text.replace(/^\s*[a-z]/, match => {
        return match.toUpperCase();
    });
}