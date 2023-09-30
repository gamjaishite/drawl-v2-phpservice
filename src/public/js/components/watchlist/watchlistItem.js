const watchlistItems = document.querySelectorAll('.watchlist-item')

let timer = null;
function drag(element) {
    clearTimeout(timer);
    element.setAttribute('draggble', 'true');
    timer = setTimeout(function() {  drag(element)}, 250);
}
function stopDrag(element) {
    element.setAttribute('draggable', 'false');
    clearTimeout(timer);
}

watchlistItems.forEach(watchlistItem => {
    const dragHandler = watchlistItem.querySelector('.drag-handler');
    dragHandler.addEventListener('mouseup', function () { stopDrag(watchlistItem) });
    dragHandler.addEventListener('dragstart', function () {
        console.log('baka')
    });
})