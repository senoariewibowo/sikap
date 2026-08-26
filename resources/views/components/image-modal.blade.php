<style>
.image-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 50;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease-out;
}
.image-modal-overlay.active {
    display: flex;
    opacity: 1;
    pointer-events: auto;
}
.image-modal-close {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 60;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    color: #374151;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    font-size: 1.25rem;
    line-height: 1;
    padding: 0;
}
.image-modal-close:hover {
    background: #ffffff;
}
.image-modal-card {
    position: relative;
    width: 100%;
    max-width: 576px;
    background: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    display: flex;
    flex-direction: column;
    max-height: 75vh;
    margin: 1rem 0;
    overflow: hidden;
    transform: scale(0.95);
    opacity: 0;
    transition: transform 0.3s ease-out, opacity 0.3s ease-out;
}
.image-modal-overlay.active .image-modal-card {
    transform: scale(1);
    opacity: 1;
}
.image-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.625rem 1rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    flex-shrink: 0;
}
.image-modal-header span {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}
.image-modal-body {
    flex: 1;
    overflow: auto;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
}
.image-modal-img {
    display: block;
    max-height: 55vh;
    max-width: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    cursor: zoom-in;
}
.image-modal-footer {
    padding: 0.75rem;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
    flex-shrink: 0;
}
.image-modal-download {
    display: block;
    width: 100%;
    padding: 0.625rem 0;
    background: #10b981;
    color: #ffffff;
    border: none;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
}
.image-modal-download:hover {
    background: #059669;
}
</style>

<div id="image_modal_overlay" class="image-modal-overlay" onclick="closeImageModal()">
    <button type="button" class="image-modal-close" onclick="event.stopPropagation(); closeImageModal();" aria-label="Tutup">&times;</button>

    <div id="image_modal_card" class="image-modal-card" onclick="event.stopPropagation()">
        <div class="image-modal-header">
            <span>Foto</span>
        </div>
        <div id="image_modal_body" class="image-modal-body">
            <img id="image_modal_img" class="image-modal-img" src="" alt="Preview" onclick="toggleImageZoom()">
        </div>
        <div class="image-modal-footer">
            <a id="image_modal_download" href="#" download="foto.jpg" class="image-modal-download">Download</a>
        </div>
    </div>
</div>

<script>
(function(){
    var overlay = document.getElementById('image_modal_overlay');
    var card = document.getElementById('image_modal_card');
    var img = document.getElementById('image_modal_img');
    var downloadLink = document.getElementById('image_modal_download');
    var zoomed = false;

    function isOpen(){
        return overlay.classList.contains('active');
    }

    window.openImageModal = function(src){
        img.src = src;
        downloadLink.href = src;
        document.body.style.overflow = 'hidden';
        resetZoom();

        overlay.style.display = 'flex';
        // pakai requestAnimationFrame agar transisi terpicu setelah display:flex diterapkan
        requestAnimationFrame(function(){
            overlay.classList.add('active');
        });
    };

    window.closeImageModal = function(){
        overlay.classList.remove('active');
        document.body.style.overflow = '';
        resetZoom();

        setTimeout(function(){
            overlay.style.display = 'none';
        }, 300);
    };

    window.toggleImageZoom = function(){
        zoomed = !zoomed;
        if(zoomed){
            img.style.maxHeight = 'none';
            img.style.maxWidth = 'none';
            img.style.width = '200%';
            img.style.height = 'auto';
            img.style.cursor = 'zoom-out';
        } else {
            resetZoom();
        }
    };

    function resetZoom(){
        zoomed = false;
        img.style.maxHeight = '';
        img.style.maxWidth = '';
        img.style.width = '';
        img.style.height = '';
        img.style.cursor = '';
    }

    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape' && isOpen()){
            closeImageModal();
        }
    });
})();
</script>
