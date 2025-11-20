function yakstretchInit(container) {
	const mode = container.dataset.yakstretchMode || 'images';
	
	if (mode === 'video') {
		yakstretchInitVideo(container);
		return;
	}
	
	// Images mode - existing logic
	const rotator = container.querySelector('.yakstretch-image-rotator');
	if (!rotator) return;

	const imageUrls = JSON.parse(rotator.dataset.images || '[]');
	if (imageUrls.length < 1) return;

	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (prefersReducedMotion) {
		const bg = document.createElement('div');
		bg.className = 'yakstretch-bg';
		bg.style.position = 'absolute';
		bg.style.top = 0;
		bg.style.left = 0;
		bg.style.right = 0;
		bg.style.bottom = 0;
		bg.style.backgroundImage = `url('${imageUrls[0]}')`;
		bg.style.backgroundSize = 'cover';
		bg.style.backgroundPosition = 'center';
		bg.style.opacity = '1';
		bg.style.transition = 'none';

		rotator.appendChild(bg);
		return;
	}	

	const delay = parseInt(rotator.dataset.delay, 10) || 6000;
	const fade = parseInt(rotator.dataset.fade, 10) || 1000;
	const randomize = rotator.dataset.randomize === '1';

	const queue = randomize ? [...imageUrls].sort(() => Math.random() - 0.5) : [...imageUrls];
	let index = 0;

	const img1 = document.createElement('div');
	const img2 = document.createElement('div');
	[img1, img2].forEach(el => {
		el.className = 'yakstretch-bg';
		el.style.position = 'absolute';
		el.style.top = 0;
		el.style.left = 0;
		el.style.right = 0;
		el.style.bottom = 0;
		el.style.willChange = 'opacity';
		el.style.backgroundSize = 'cover';
		el.style.backgroundPosition = 'center';
		el.style.transition = `opacity ${fade}ms ease`;
		el.style.zIndex = -1;
		el.style.opacity = 0;
		rotator.appendChild(el);
	});

	let current = img1;
	let next = img2;

	current.style.backgroundImage = `url("${queue[0]}")`;
	current.style.opacity = 1;

	// Image preload cache to prevent memory leaks
	const preloadCache = new Map();
	let rotationTimers = [];
	let isPaused = false;
	
	const rotate = () => {
		if (isPaused) return;
		
		index = (index + 1) % queue.length;
		const nextUrl = queue[index];

		// Check if image is already cached
		if (preloadCache.has(nextUrl)) {
			next.style.backgroundImage = `url("${nextUrl}")`;
			next.style.opacity = 1;
			current.style.opacity = 0;
			[current, next] = [next, current];
			const timer = setTimeout(rotate, delay);
			rotationTimers.push(timer);
			return;
		}

		const preload = new Image();
		preload.src = nextUrl;
		preload.onload = () => {
			// Cache the loaded image
			preloadCache.set(nextUrl, true);
			next.style.backgroundImage = `url("${nextUrl}")`;
			next.style.opacity = 1;
			current.style.opacity = 0;
			[current, next] = [next, current];
			const timer = setTimeout(rotate, delay);
			rotationTimers.push(timer);
		};
		preload.onerror = () => {
			// Handle failed image loads gracefully
			const timer = setTimeout(rotate, delay);
			rotationTimers.push(timer);
		};
	};

	const initialTimer = setTimeout(() => {
		if (queue.length > 1) rotate();
	}, delay);
	rotationTimers.push(initialTimer);

	// Initialize play/pause button
	initPlayPauseButton(container, rotator, rotationTimers, () => isPaused, (value) => { isPaused = value; }, rotate, delay, queue);
}

function initPlayPauseButton(container, rotator, rotationTimers, getIsPaused, setIsPaused, rotate, delay, queue) {
	const playPauseBtn = container.querySelector('.yakstretch-play-pause-btn');
	if (!playPauseBtn) return;
	
	const mode = container.dataset.yakstretchMode || 'images';
	
	// Video mode play/pause
	if (mode === 'video') {
		const videoWrapper = container.querySelector('.yakstretch-video-wrapper');
		if (!videoWrapper) return;
		
		const videoElement = videoWrapper.querySelector('video');
		const videoEmbed = videoWrapper.querySelector('.yakstretch-video-embed iframe');
		
		playPauseBtn.addEventListener('click', () => {
			if (videoElement) {
				if (videoElement.paused) {
					videoElement.play().catch(() => {
						// Autoplay blocked, show fallback
						showVideoFallback(container);
					});
					playPauseBtn.setAttribute('data-yakstretch-pause', 'true');
					playPauseBtn.setAttribute('aria-label', 'Pause video');
					playPauseBtn.setAttribute('title', 'Pause video');
					playPauseBtn.querySelector('.yakstretch-btn-text').textContent = 'Pause';
				} else {
					videoElement.pause();
					playPauseBtn.setAttribute('data-yakstretch-pause', 'false');
					playPauseBtn.setAttribute('aria-label', 'Play video');
					playPauseBtn.setAttribute('title', 'Play video');
					playPauseBtn.querySelector('.yakstretch-btn-text').textContent = 'Play';
				}
			} else if (videoEmbed) {
				// For external videos, we can't control playback directly
				// Button is less useful here, but we'll keep it for consistency
				console.log('External video play/pause not directly controllable');
			}
		});
		return;
	}

	// Images mode play/pause (existing logic)
	playPauseBtn.addEventListener('click', () => {
		const currentPaused = getIsPaused();
		setIsPaused(!currentPaused);
		
		if (!currentPaused) {
			// Pausing rotation - clear all timers
			rotationTimers.forEach(timer => clearTimeout(timer));
			rotationTimers.length = 0;
			playPauseBtn.setAttribute('data-yakstretch-pause', 'false');
			playPauseBtn.setAttribute('aria-label', 'Resume image rotation');
			playPauseBtn.setAttribute('title', 'Resume image rotation');
			playPauseBtn.querySelector('.yakstretch-btn-text').textContent = 'Resume';
		} else {
			// Resuming rotation
			playPauseBtn.setAttribute('data-yakstretch-pause', 'true');
			playPauseBtn.setAttribute('aria-label', 'Pause image rotation');
			playPauseBtn.setAttribute('title', 'Pause image rotation');
			playPauseBtn.querySelector('.yakstretch-btn-text').textContent = 'Pause';
			
			// Restart rotation
			const timer = setTimeout(() => {
				if (!getIsPaused() && queue.length > 1) rotate();
			}, delay);
			rotationTimers.push(timer);
		}
	});
}

function yakstretchInitVideo(container) {
	const videoWrapper = container.querySelector('.yakstretch-video-wrapper');
	if (!videoWrapper) return;
	
	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const disableOnMobile = videoWrapper.dataset.disableMobileVideo === '1';
	const isMobile = window.innerWidth <= 767;
	
	// Check if we should show fallback instead of video
	if (prefersReducedMotion || (disableOnMobile && isMobile)) {
		showVideoFallback(container);
		return;
	}
	
	const fallbackEl = container.querySelector('.yakstretch-video-fallback');
	const videoElement = videoWrapper.querySelector('video');
	const videoEmbed = videoWrapper.querySelector('.yakstretch-video-embed iframe');
	
	// Handle self-hosted video
	if (videoElement) {
		// Set up video element styling
		videoElement.style.width = '100%';
		videoElement.style.height = '100%';
		videoElement.style.objectFit = 'cover';
		videoElement.style.position = 'absolute';
		videoElement.style.top = '0';
		videoElement.style.left = '0';
		videoElement.style.zIndex = '-2';
		
		// Show fallback initially
		if (fallbackEl) {
			fallbackEl.style.opacity = '1';
			fallbackEl.style.transition = 'opacity 0.5s ease';
		}
		
		// Hide fallback when video starts playing
		videoElement.addEventListener('playing', () => {
			if (fallbackEl) {
				fallbackEl.style.opacity = '0';
				setTimeout(() => {
					fallbackEl.style.display = 'none';
				}, 500);
			}
		});
		
		// Show fallback if video fails to load
		videoElement.addEventListener('error', () => {
			showVideoFallback(container);
		});
		
		// Check if video is blocked (still paused after load attempt)
		videoElement.addEventListener('loadeddata', () => {
			setTimeout(() => {
				if (videoElement.paused && !videoElement.ended) {
					// Video is likely blocked by browser
					showVideoFallback(container);
				}
			}, 1000);
		});
		
		// Try to play video
		if (videoWrapper.dataset.videoAutoplay === '1') {
			videoElement.play().catch(() => {
				// Autoplay blocked, show fallback
				showVideoFallback(container);
			});
		}
	}
	
	// Handle external video (YouTube/Vimeo)
	if (videoEmbed) {
		// Set up iframe styling
		videoEmbed.style.width = '100%';
		videoEmbed.style.height = '100%';
		videoEmbed.style.position = 'absolute';
		videoEmbed.style.top = '0';
		videoEmbed.style.left = '0';
		videoEmbed.style.border = 'none';
		videoEmbed.style.pointerEvents = 'none';
		
		// Show fallback initially
		if (fallbackEl) {
			fallbackEl.style.opacity = '1';
			fallbackEl.style.transition = 'opacity 0.5s ease';
		}
		
		// Hide fallback when iframe loads (approximate detection)
		videoEmbed.addEventListener('load', () => {
			setTimeout(() => {
				if (fallbackEl) {
					fallbackEl.style.opacity = '0';
					setTimeout(() => {
						fallbackEl.style.display = 'none';
					}, 500);
				}
			}, 2000); // Give iframe time to start playing
		});
		
		// Show fallback if iframe fails to load
		videoEmbed.addEventListener('error', () => {
			showVideoFallback(container);
		});
	}
	
	// Initialize play/pause button for video
	initPlayPauseButton(container, null, [], () => false, () => {}, null, 0, []);
}

function showVideoFallback(container) {
	const videoWrapper = container.querySelector('.yakstretch-video-wrapper');
	const fallbackEl = container.querySelector('.yakstretch-video-fallback');
	const videoElement = videoWrapper?.querySelector('video');
	const videoEmbed = videoWrapper?.querySelector('.yakstretch-video-embed iframe');
	
	// Hide video
	if (videoWrapper) {
		videoWrapper.style.display = 'none';
	}
	if (videoElement) {
		videoElement.pause();
	}
	
	// Show fallback
	if (fallbackEl) {
		fallbackEl.style.display = 'block';
		fallbackEl.style.opacity = '1';
		
		// Use mobile fallback if on mobile and available
		const isMobile = window.innerWidth <= 767;
		const mobileFallbackUrl = fallbackEl.dataset.mobileFallbackUrl;
		if (isMobile && mobileFallbackUrl) {
			fallbackEl.style.backgroundImage = `url('${mobileFallbackUrl}')`;
		}
	}
}


// YakStretch waits for AVIF to allow/init, or runs standalone
if (window.tomatilloAvifYakDelay) {
	window.addEventListener('tomatilloAvifReady', () => {
		document.querySelectorAll('[data-yakstretch="1"]').forEach(yakstretchInit);
	}, { once: true });
} else {
	document.addEventListener('DOMContentLoaded', () => {
		document.querySelectorAll('[data-yakstretch="1"]').forEach(yakstretchInit);
	});
}

