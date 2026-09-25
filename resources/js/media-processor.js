import '@google/model-viewer';

// MEDIA PROCESSOR
// Provides static functions to process media uploads in the frontend
export default class MediaProcessor {

    // 1. Helper method to dispaatch poster request to correct method
    static async generatePoster(category, file) {
        // Possible poster generators
        const posterGenerators = {
            video: (f) => MediaProcessor.generateVideoPoster(f),
            model: (f) => MediaProcessor.generate3DModelPoster(f)
        }

        // Dispatch to correct posterGenerator based on category
        // If category does not match, return null
        const generator = posterGenerators[category];
        if (!generator) return null;

        // Try to create the poster and return its path
        try {
            return await generator(file);

        } catch (e) {
            console.warn(`Could not create poster image for ${file.name}:`, e);
            return null;
        }
    }

    // 1. Generate poster image of uploaded video file
    // Creates ad hoc video element and copies the frame at seekTo (default 1.0s) to a canvas
    // The canvas then exports the frame as a blob (webp)
    static async generateVideoPoster(videoFile, seekTo = 1.0) {
        return new Promise((resolve, reject) => {
            // 1. Create temporary video and canvas elements
            const video = document.createElement("video");
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");

            // Create an Object URL from the File object
            const videoUrl = URL.createObjectURL(videoFile);
            video.src = videoUrl;
            video.muted = true;
            video.playsInline = true;

            // 2. Once video metadata (dimensions, duration) is loaded, jump to target time
            video.onloadedmetadata = () => {
                // Ensure target seek time isn't past the total duration
                video.currentTime = Math.min(seekTo, video.duration);
            };

            // 3. When the frame at currentTime has finished seeking, capture it
            video.onseeked = () => {
                // Match canvas size to video dimensions
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                // Draw the current video frame onto the canvas
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Export as JPEG Blob (or use canvas.toDataURL("image/jpeg"))
                canvas.toBlob((blob) => {
                    // Clean up memory
                    URL.revokeObjectURL(videoUrl);
                    video.remove();

                    if (blob) {
                        resolve(blob);
                    } else {
                        reject(new Error("Failed to export video poster frame."));
                    }
                }, "image/webp", 0.85); // 85% image quality
            };

            video.onerror = (err) => {
                URL.revokeObjectURL(videoUrl);
                reject(err);
            };
        });
    }

    // 2. Generate poster image for a 3D model
    // Create a poster image by creating a model-viewer somewhere out-of-bounds
    // Then use toBlob method to fetch a poster image
    static async generate3DModelPoster(glbFile, options = {}) {
        return new Promise((resolve, reject) => {
            const viewer = document.createElement('model-viewer');
            const objectUrl = URL.createObjectURL(glbFile);

            // 1. Force immediate auto-loading
            viewer.reveal = 'auto'; // DO NOT use 'interaction'
            viewer.loading = 'eager';

            // 2. Dimensions & placement (visible to WebGL engine, but invisible to user)
            viewer.style.width = options.width || '800px';
            viewer.style.height = options.height || '800px';
            viewer.style.position = 'fixed';
            viewer.style.top = '0';
            viewer.style.left = '0';
            viewer.style.opacity = '0.001'; // WebGL needs opacity > 0 to render frames
            viewer.style.pointerEvents = 'none';
            viewer.style.zIndex = '-9999';

            // 3. Fallback Timeout (Prevents hanging forever if GLB is corrupted)
            const timeoutId = setTimeout(() => {
                cleanup();
                reject(new Error('3D Poster generation timed out after 10 seconds.'));
            }, 10000);

            const cleanup = () => {
                clearTimeout(timeoutId);
                URL.revokeObjectURL(objectUrl);
                viewer.remove();
            };

            // 4. Listen for load event
            viewer.addEventListener('load', async () => {
                try {
                    // Wait 2 animation frames so lighting/shadows compile
                    await new Promise((r) => requestAnimationFrame(() => requestAnimationFrame(r)));

                    // Native canvas snapshot
                    const blob = await viewer.toBlob({
                        mimeType: options.mimeType || 'image/webp',
                        qualityArgument: options.quality || 0.85,
                        idealAspect: true,
                    });

                    cleanup();

                    if (blob) {
                        resolve(blob);
                    } else {
                        reject(new Error('Failed to capture 3D poster blob.'));
                    }
                } catch (err) {
                    cleanup();
                    reject(err);
                }
            });

            viewer.addEventListener('error', (err) => {
                cleanup();
                reject(err);
            });

            // 5. Append to DOM BEFORE setting src
            document.body.appendChild(viewer);
            viewer.src = objectUrl;
        });
    }
}