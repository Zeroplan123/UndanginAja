/**
 * Modern Photo Gallery with LightGallery.js
 * Features: Zoom, Swipe, Fullscreen, Thumbnails, Share
 */

import lightGallery from 'lightgallery';
import lgThumbnail from 'lg-thumbnail';
import lgZoom from 'lg-zoom';
import lgFullscreen from 'lg-fullscreen';
import lgAutoplay from 'lg-autoplay';
import lgShare from 'lg-share';
import lgRotate from 'lg-rotate';

// Initialize LightGallery when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const galleryElement = document.getElementById('lightgallery');
    
    if (galleryElement) {
        // Initialize LightGallery with all features
        const gallery = lightGallery(galleryElement, {
            // Core settings
            speed: 500,
            licenseKey: 'your_license_key',
            
            // Plugins
            plugins: [lgThumbnail, lgZoom, lgFullscreen, lgAutoplay, lgShare, lgRotate],
            
            // Thumbnail settings
            thumbnail: true,
            thumbWidth: 100,
            thumbHeight: 80,
            thumbMargin: 5,
            animateThumb: true,
            currentPagerPosition: 'middle',
            
            // Zoom settings
            zoom: true,
            scale: 1,
            enableZoomAfter: 300,
            actualSize: true,
            showZoomInOutIcons: true,
            
            // Fullscreen
            fullScreen: true,
            
            // Autoplay
            autoplay: false,
            pause: 3000,
            progressBar: true,
            
            // Share
            share: true,
            facebook: true,
            facebookDropdownText: 'Facebook',
            twitter: true,
            twitterDropdownText: 'Twitter',
            pinterest: true,
            pinterestDropdownText: 'Pinterest',
            
            // Rotate
            rotate: true,
            flipHorizontal: true,
            flipVertical: true,
            
            // Mobile settings
            mobileSettings: {
                controls: true,
                showCloseIcon: true,
                download: true,
                rotate: true
            },
            
            // Swipe settings
            swipeThreshold: 50,
            enableSwipe: true,
            enableDrag: true,
            
            // Animation settings
            mode: 'lg-slide',
            cssEasing: 'cubic-bezier(0.25, 0, 0.25, 1)',
            
            // Controls
            controls: true,
            download: true,
            counter: true,
            closable: true,
            showMaximizeIcon: true,
            appendSubHtmlTo: '.lg-sub-html',
            subHtmlSelectorRelative: true,
            
            // Preload settings
            preload: 2,
            showAfterLoad: true,
            
            // Custom selectors
            selector: '.gallery-item-link',
            
            // Event callbacks
            onBeforeOpen: function() {
                console.log('Gallery opening...');
            },
            
            onAfterOpen: function() {
                console.log('Gallery opened');
                // Add custom styling or functionality here
            },
            
            onBeforeClose: function() {
                console.log('Gallery closing...');
            },
            
            onAfterClose: function() {
                console.log('Gallery closed');
            },
            
            onSlideClick: function() {
                // Toggle zoom on image click
                const zoomPlugin = gallery.plugins.zoom;
                if (zoomPlugin) {
                    zoomPlugin.zoomIn();
                }
            }
        });
        
        // Add custom keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (gallery.lgOpened) {
                switch(e.key) {
                    case 'z':
                    case 'Z':
                        e.preventDefault();
                        const zoomPlugin = gallery.plugins.zoom;
                        if (zoomPlugin) {
                            zoomPlugin.zoomIn();
                        }
                        break;
                    case 'r':
                    case 'R':
                        e.preventDefault();
                        const rotatePlugin = gallery.plugins.rotate;
                        if (rotatePlugin) {
                            rotatePlugin.rotateLeft();
                        }
                        break;
                    case 'f':
                    case 'F':
                        e.preventDefault();
                        const fullscreenPlugin = gallery.plugins.fullscreen;
                        if (fullscreenPlugin) {
                            fullscreenPlugin.toggle();
                        }
                        break;
                }
            }
        });
        
        // Add touch gestures for mobile
        let touchStartX = 0;
        let touchStartY = 0;
        
        galleryElement.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });
        
        galleryElement.addEventListener('touchmove', function(e) {
            if (!touchStartX || !touchStartY) return;
            
            const touchEndX = e.touches[0].clientX;
            const touchEndY = e.touches[0].clientY;
            
            const diffX = touchStartX - touchEndX;
            const diffY = touchStartY - touchEndY;
            
            // Prevent default scrolling when swiping horizontally
            if (Math.abs(diffX) > Math.abs(diffY)) {
                e.preventDefault();
            }
        }, { passive: false });
    }
});

// Utility function to open gallery at specific index
window.openGalleryAt = function(index) {
    const galleryElement = document.getElementById('lightgallery');
    if (galleryElement && galleryElement.lgData) {
        galleryElement.lgData.openGallery(index);
    }
};

// Utility function to add new images dynamically
window.addToGallery = function(imageData) {
    const galleryElement = document.getElementById('lightgallery');
    if (galleryElement && galleryElement.lgData) {
        galleryElement.lgData.destroy(true);
        // Re-initialize with new data
        // This would require updating the gallery HTML first
    }
};
