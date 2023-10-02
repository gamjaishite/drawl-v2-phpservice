const modals = document.querySelectorAll('.modal');
const modalTriggers = document.querySelectorAll('.modal__trigger');
const containerDefault = document.querySelector('main');
const modalClose = document.querySelector('.modal__close');

modals.forEach(modal => {
    const modalTrigger = modal.querySelector('.modal__trigger');
    const modalContentWrapper = modal.querySelector('#modal__content');
    const modalClose = modal.querySelector('.modal__close');

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
})

