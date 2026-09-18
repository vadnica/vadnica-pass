document.addEventListener('DOMContentLoaded', function() {
    // Modals handling
    document.querySelectorAll('.modal-button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const target = this.getAttribute('data-target');
            const modal = document.getElementById(target);
            if (modal) {
                modal.style.display = 'block';
                if (target === 'imageModal') {
                    resetZoom();
                }
            }
        });
    });

    document.querySelectorAll('.close-icon').forEach(function(icon) {
        icon.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.style.display = 'none';
                if (modal.id === 'imageModal') {
                    resetZoom();
                }
            }
        });
    });

    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
            if (event.target.id === 'imageModal') {
                resetZoom();
            }
        }
    });

    // Pinch Zoom & Pan Logic za sliko (Vanilla JS)
    let scale = 1;
    let lastScale = 1;
    let startDist = 0;
    let posX = 0, posY = 0;
    let lastPosX = 0, lastPosY = 0;
    let startX = 0, startY = 0;
    let isPinching = false;
    let isPanning = false;

    const img = document.querySelector('.full-preview-img');

    if (img) {
        img.addEventListener('touchstart', function(e) {
            const touches = e.touches;
            if (touches.length === 2) {
                isPinching = true;
                startDist = Math.hypot(
                    touches[0].pageX - touches[1].pageX,
                    touches[0].pageY - touches[1].pageY
                );
                lastScale = scale;
            } else if (touches.length === 1 && scale > 1) {
                isPanning = true;
                startX = touches[0].pageX - lastPosX;
                startY = touches[0].pageY - lastPosY;
            }
        }, { passive: false });

        img.addEventListener('touchmove', function(e) {
            const touches = e.touches;
            if (isPinching && touches.length === 2) {
                e.preventDefault();
                const dist = Math.hypot(
                    touches[0].pageX - touches[1].pageX,
                    touches[0].pageY - touches[1].pageY
                );
                scale = Math.min(Math.max(1, lastScale * (dist / startDist)), 4);
                updateTransform();
            } else if (isPanning && touches.length === 1 && scale > 1) {
                e.preventDefault();
                posX = touches[0].pageX - startX;
                posY = touches[0].pageY - startY;
                updateTransform();
            }
        }, { passive: false });

        img.addEventListener('touchend', function(e) {
            const touches = e.touches;
            if (isPinching && touches.length < 2) {
                isPinching = false;
                lastScale = scale;
            }
            if (isPanning && touches.length === 0) {
                isPanning = false;
                lastPosX = posX;
                lastPosY = posY;
            }
        });
    }

    function updateTransform() {
        if (img) {
            img.style.transform = "translate(" + posX + "px, " + posY + "px) scale(" + scale + ")";
        }
    }

    function resetZoom() {
        scale = 1;
        lastScale = 1;
        posX = 0;
        posY = 0;
        lastPosX = 0;
        lastPosY = 0;
        if (img) {
            img.style.transform = 'none';
        }
    }
});
