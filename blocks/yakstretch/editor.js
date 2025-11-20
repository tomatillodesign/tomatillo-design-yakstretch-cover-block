function applyYakstretchPreviewBackground(blockEl) {
	const inner = blockEl.querySelector('.yakstretch-cover-block');
	if (!inner) {
		return;
	}

	const mode = blockEl.dataset.yakstretchMode || 'images';
	
	if (mode === 'video') {
		applyYakstretchPreviewVideo(blockEl);
		return;
	}

	// Images mode - existing logic
	const rotator = blockEl.querySelector('.yakstretch-image-rotator');
	if (!rotator) {
		return;
	}

	let urls = [];
	try {
		urls = JSON.parse(rotator.dataset.images || '[]');
	} catch (e) {
		return;
	}

	if (urls.length === 0) return;

	let bgDiv = inner.querySelector('.yakstretch-editor-bg');
	if (!bgDiv) {
		bgDiv = document.createElement('div');
		bgDiv.className = 'yakstretch-editor-bg';
		bgDiv.style.position = 'absolute';
		bgDiv.style.inset = '0';
		bgDiv.style.zIndex = '0';
		bgDiv.style.pointerEvents = 'none';
		bgDiv.style.willChange = 'opacity';
		inner.prepend(bgDiv);
	}

	bgDiv.style.backgroundImage = `url("${urls[0]}")`;
	bgDiv.style.backgroundSize = 'cover';
	bgDiv.style.backgroundPosition = 'center';
	bgDiv.style.opacity = '1';
	
	// Start image rotation in editor if multiple images
	if (urls.length > 1) {
		startEditorImageRotation(bgDiv, urls, rotator);
	}
}

function startEditorImageRotation(bgDiv, urls, rotator) {
	// Prevent multiple rotations from starting
	if (bgDiv._isRotating) {
		return;
	}
	
	// Clear any existing timers first
	clearEditorRotationTimers(bgDiv);
	
	const delay = parseInt(rotator.dataset.delay, 10) || 6000;
	const fade = parseInt(rotator.dataset.fade, 10) || 1000;
	const randomize = rotator.dataset.randomize === '1';
	
	const queue = randomize ? [...urls].sort(() => Math.random() - 0.5) : [...urls];
	let index = 0;
	
	// Store timers on the element for cleanup
	bgDiv._rotationTimers = [];
	bgDiv._isRotating = true;
	
	const rotate = () => {
		index = (index + 1) % queue.length;
		const nextUrl = queue[index];
		
		// Add rotation indicator
		bgDiv.classList.add('rotating');
		
		// Create fade transition
		bgDiv.style.transition = `opacity ${fade}ms ease`;
		bgDiv.style.opacity = '0';
		
		const fadeTimer = setTimeout(() => {
			bgDiv.style.backgroundImage = `url("${nextUrl}")`;
			bgDiv.style.opacity = '1';
			bgDiv.classList.remove('rotating');
		}, fade / 2);
		
		const nextTimer = setTimeout(rotate, delay);
		
		// Store timers for cleanup
		bgDiv._rotationTimers.push(fadeTimer, nextTimer);
	};
	
	// Start rotation after initial delay
	const initialTimer = setTimeout(() => {
		if (queue.length > 1) rotate();
	}, delay);
	bgDiv._rotationTimers.push(initialTimer);
}

function clearEditorRotationTimers(bgDiv) {
	if (bgDiv._rotationTimers) {
		bgDiv._rotationTimers.forEach(timer => clearTimeout(timer));
		bgDiv._rotationTimers = [];
	}
	// Reset visual state and rotation flag
	bgDiv.style.transition = '';
	bgDiv.style.opacity = '';
	bgDiv.classList.remove('rotating');
	bgDiv._isRotating = false;
}

function applyYakstretchPreviewVideo(blockEl) {
	const inner = blockEl.querySelector('.yakstretch-cover-block');
	if (!inner) {
		return;
	}

	const videoWrapper = blockEl.querySelector('.yakstretch-video-wrapper');
	if (!videoWrapper) {
		return;
	}

	// Hide actual video elements in editor (we'll show preview image instead)
	if (videoWrapper) {
		videoWrapper.style.display = 'none';
	}
	const fallbackEl = blockEl.querySelector('.yakstretch-video-fallback');
	if (fallbackEl) {
		fallbackEl.style.display = 'none';
	}

	// Get poster or fallback image URL
	const posterUrl = videoWrapper.dataset.videoPoster || '';
	const fallbackUrl = fallbackEl?.dataset.fallbackUrl || fallbackEl?.style.backgroundImage?.match(/url\(['"]?([^'"]+)['"]?\)/)?.[1] || '';
	
	// Use poster if available, otherwise fallback, otherwise placeholder
	const imageUrl = posterUrl || fallbackUrl;
	
	// Get width constraint from video wrapper
	const wrapperWidth = videoWrapper.style.width || window.getComputedStyle(videoWrapper).width;
	const wrapperRight = videoWrapper.style.right || window.getComputedStyle(videoWrapper).right;
	
	if (!imageUrl) {
		// Show placeholder
		let bgDiv = inner.querySelector('.yakstretch-editor-bg');
		if (!bgDiv) {
			bgDiv = document.createElement('div');
			bgDiv.className = 'yakstretch-editor-bg';
			bgDiv.style.position = 'absolute';
			bgDiv.style.top = '0';
			bgDiv.style.bottom = '0';
			bgDiv.style.zIndex = '0';
			bgDiv.style.pointerEvents = 'none';
			inner.prepend(bgDiv);
		}
		bgDiv.style.backgroundImage = 'none';
		bgDiv.style.backgroundColor = '#333';
		bgDiv.style.display = 'flex';
		bgDiv.style.alignItems = 'center';
		bgDiv.style.justifyContent = 'center';
		bgDiv.style.color = '#fff';
		bgDiv.style.fontSize = '0.875rem';
		bgDiv.textContent = 'Video background (no preview image)';
		if (wrapperWidth && wrapperWidth !== '100%') {
			bgDiv.style.width = wrapperWidth;
			bgDiv.style.right = wrapperRight || '0';
		}
		return;
	}

	// Show poster/fallback image
	let bgDiv = inner.querySelector('.yakstretch-editor-bg');
	if (!bgDiv) {
		bgDiv = document.createElement('div');
		bgDiv.className = 'yakstretch-editor-bg';
		bgDiv.style.position = 'absolute';
		bgDiv.style.top = '0';
		bgDiv.style.bottom = '0';
		bgDiv.style.zIndex = '0';
		bgDiv.style.pointerEvents = 'none';
		bgDiv.style.willChange = 'opacity';
		inner.prepend(bgDiv);
	}

	bgDiv.style.backgroundImage = `url("${imageUrl}")`;
	bgDiv.style.backgroundSize = 'cover';
	bgDiv.style.backgroundPosition = 'center';
	bgDiv.style.opacity = '1';
	bgDiv.style.display = 'block';
	bgDiv.textContent = '';
	bgDiv.style.left = 'auto';
	
	// Apply width constraint from video wrapper
	if (wrapperWidth && wrapperWidth !== '100%' && wrapperWidth !== 'auto') {
		bgDiv.style.width = wrapperWidth;
		bgDiv.style.right = wrapperRight || '0';
		bgDiv.style.left = 'auto';
	} else {
		bgDiv.style.width = '100%';
		bgDiv.style.right = 'auto';
		bgDiv.style.left = '0';
	}
}


// Initial pass for already-rendered blocks
document.querySelectorAll('.wp-block-yak-yakstretch-cover').forEach((blockEl) => {
	const mode = blockEl.dataset.yakstretchMode || 'images';
	if (mode === 'video') {
		applyYakstretchPreviewVideo(blockEl);
	} else {
		applyYakstretchPreviewBackground(blockEl);
	}
});

// MutationObserver to catch live updates
// --- Debounce utility ---
function debounce(fn, delay = 100) {
	let timeout;
	return (...args) => {
		clearTimeout(timeout);
		timeout = setTimeout(() => fn(...args), delay);
	};
}

// --- Debounced background applier ---
const debouncedApply = debounce((blockEl) => {
	if (blockEl) {
		const mode = blockEl.dataset.yakstretchMode || 'images';
		if (mode === 'video') {
			applyYakstretchPreviewVideo(blockEl);
		} else {
			applyYakstretchPreviewBackground(blockEl);
		}
	}
}, 100);

// --- Settings change handler ---
function handleSettingsChange(blockEl) {
	if (blockEl) {
		const mode = blockEl.dataset.yakstretchMode || 'images';
		// Clear any existing rotation timers and indicators
		const bgDiv = blockEl.querySelector('.yakstretch-editor-bg');
		if (bgDiv) {
			clearEditorRotationTimers(bgDiv);
		}
		// Reapply background with new settings
		if (mode === 'video') {
			applyYakstretchPreviewVideo(blockEl);
		} else {
			applyYakstretchPreviewBackground(blockEl);
		}
	}
}

// --- Debounced settings change handler ---
const debouncedSettingsChange = debounce(handleSettingsChange, 500);

// --- Observer setup ---
const observer = new MutationObserver(mutations => {
	mutations.forEach(mutation => {
		// Handle newly added blocks
		mutation.addedNodes.forEach(node => {
			if (!(node instanceof HTMLElement)) return;

			const block = node.closest?.('.wp-block-yak-yakstretch-cover');
			if (block) {
				debouncedApply(block);
			}

			const embedded = node.querySelector?.('.wp-block-yak-yakstretch-cover');
			if (embedded) {
				debouncedApply(embedded);
			}
		});

		// Handle attribute/child changes inside existing blocks (e.g., gallery updates)
		if (mutation.type === 'attributes' || mutation.type === 'childList' || mutation.type === 'characterData') {
			const block = mutation.target.closest?.('.wp-block-yak-yakstretch-cover');
			if (block) {
				// Check if it's a settings change (data attributes)
				if (mutation.type === 'attributes' && mutation.attributeName && 
					['data-images', 'data-delay', 'data-fade', 'data-randomize', 'data-yakstretch-mode', 
					 'data-video-poster', 'data-video-fallback-url', 'data-mobile-fallback-url'].includes(mutation.attributeName)) {
					debouncedSettingsChange(block);
				} else {
					debouncedApply(block);
				}
			}
		}
	});
});

observer.observe(document.body, {
	subtree: true,
	childList: true,
	attributes: true,
	characterData: true,
	attributeFilter: ['data-images', 'data-yakstretch-mode', 'data-video-poster', 'data-video-fallback-url', 'data-mobile-fallback-url'],
});
