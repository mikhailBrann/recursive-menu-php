function checker(event, parentSelector) {
    let parent = event.currentTarget.closest(parentSelector);
    parent.classList.toggle('active');
}


window.addEventListener('DOMContentLoaded', () => {
    let checkersArray = Array.from(document.querySelectorAll('.check-menu-wrapp'));
    checkersArray.map(item => item.addEventListener('click', (event) => checker(event, 'li')));
});


