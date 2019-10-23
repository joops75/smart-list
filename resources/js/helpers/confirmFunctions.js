import swal from 'sweetalert';

export const confirmModal = text => {
    return swal({
        text,
        buttons: [true, 'Delete'],
        dangerMode: true
    }).then(val => val === true);
}