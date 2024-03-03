document.addEventListener('DOMContentLoaded', function() {
    const votingButtons = document.getElementById('wtah-voting-buttons');

    // Disable transition initially
    votingButtons.classList.add('no-transition');

    const intro = document.querySelector('.intro');
    const isOpen = localStorage.getItem('wtahState') === 'open';
    if (isOpen) {
        votingButtons.classList.add('open');
    }

    intro.addEventListener('click', function() {
        votingButtons.classList.toggle('open');

        // Save state to localStorage
        if (votingButtons.classList.contains('open')) {
            localStorage.setItem('wtahState', 'open');
        } else {
            localStorage.setItem('wtahState', 'closed');
        }
    });

    // Re-enable transition after a short delay
    setTimeout(function() {
        votingButtons.classList.remove('no-transition');
    }, 50); 
});