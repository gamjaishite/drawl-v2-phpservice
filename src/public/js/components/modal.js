const modalTrigger = document.querySelector('.modal__trigger');
const modalContentWrapper = document.querySelector('#modal__content');
const containerDefault = document.querySelector('.container__default');
const modalClose = document.querySelector('.modal__close');

modalTrigger.addEventListener('click', function () {
    if (modalContentWrapper.style.display === 'flex'){
        modalContentWrapper.classList.style.display = 'none' 
    } else {
        modalContentWrapper.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        containerDefault.style.zIndex = '1000';
    }
})

modalClose.addEventListener('click', function () {
    modalContentWrapper.style.display = 'none';
    document.body.style.overflow = 'unset';
    containerDefault.style.zIndex = '10';
})