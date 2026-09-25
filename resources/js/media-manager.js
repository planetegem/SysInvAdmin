import MediaPreviewFactory from "./media-preview-factory.js";
import MediaProcessor from "./media-processor.js";
import '@google/model-viewer';

// MEDIA MANAGER: logic to load preview from file input & change form depending on file type selected
export default class MediaManager {

    constructor(id) {
        // Find main container
        this.id = id;
        this.container = document.getElementById(id);

        // State properties
        this.previews = [];
        this.currentInex = 0;
        this.settings = null;

        // Important HTML elements
        this.fileInput = document.getElementById(id + "[file]");
        this.typeInput = document.getElementById(id + "[type]");
        this.content = document.getElementById(id + "[content]");
        this.leftButton = this.content.querySelector("button.left");
        this.rightButton = this.content.querySelector("button.right");

        // Event listeners
        this.fileInput.addEventListener("change", (e) => this.loadFile());
        this.typeInput.addEventListener("change", (e) => this.setType());
        this.leftButton.addEventListener("click", (e) => this.cyclePreviews(-1));
        this.rightButton.addEventListener("click", (e) => this.cyclePreviews(1));

        this.setType();

    }

    // Normalize type into a category
    getCategory(type) {
        const categoryMap = {
            image: "image",
            carousel: "image",
            "image-list": "image",
            video: "video",
            gifv: "video",
            "3d-model": "model"
        };
        return categoryMap[type] || null;
    }

    // Map file input configs depending on type
    getInputCongif(type) {
        const configMap = {
            none: { disabled: true, accept: "", multiple: false },
            image: { disabled: false, accept: "image/*", multiple: false },
            carousel: { disabled: false, accept: "image/*", multiple: true },
            "image-list": { disabled: false, accept: "image/*", multiple: true },
            video: { disabled: false, accept: "video/*", multiple: false },
            gifv: { disabled: false, accept: "video/*", multiple: false },
            "3d-model": { disabled: false, accept: ".glb,model/gltf-binary", multiple: false }
        }
        return configMap[type] || null;
    }

    // File type selection
    setType() {
        this.clearFileInput();
        this.content.classList.add("removed");
        this.container.setAttribute("mediatype", this.typeInput.value);

        // Fetch config for the file selector
        const config = this.getInputCongif(this.typeInput.value);

        // Fall back in case type isn't recognized
        if (!config) {
            console.log(`File type ${this.typeInput.value} doesn't have a mapped config yet.`);
            return;
        }
        this.fileInput.disabled = config.disabled;
        this.fileInput.accept = config.accept;
        this.fileInput.multiple = config.multiple;

        // If updating an item and switching between types, retain state
        if (this.settings) this.executePrefill(this.settings);
    }

    // Dispatch to to correct preview factory
    createPreview(category, payload) {
        const factoryMethods = {
            image: () => MediaPreviewFactory.createImagePreview(this, {
                ...payload,
                alt: payload.alt ?? ""
            }),
            video: () => MediaPreviewFactory.createVideoPreview(this, {
                ...payload,
                category: payload.categoryType ?? this.typeInput.value,
                title: payload.title ?? "",
                description: payload.description ?? "",
                include_audio: payload.include_audio ?? false
            }),
            model: () => MediaPreviewFactory.createModelPreview(this, {
                ...payload,
                alt: payload.alt ?? ""
            })
        };
        return factoryMethods[category]?.() || null;
    }

    // Helper to prepare and reset the preview container
    preparePreviewContainer(number) {
        // Set fileselected attribute and reset all previews
        this.container.setAttribute("fileselected", true);
        this.clearPreviews();
        this.content.classList.remove("removed");

        // Toggle cycle navigation if more than 1 file
        const showNav = number > 1;
        this.leftButton.classList.toggle("removed", !showNav);
        this.rightButton.classList.toggle("removed", !showNav);
    }

    // FLOW 1: UPLOAD FILES AND IMMEDIATELY DISPLAY THEM AS PREVIEWS
    // Process file when uploaded via input
    // Includes upload to temp storage, and calls to the factory
    async processFile(file, index, category) {
        // Prepare form to soft save the file
        const formData = new FormData();
        formData.append("file", file);
        formData.append("type", category);

        // Attempt to add a poster
        const createdPoster = await MediaProcessor.generatePoster(category, file);
        if (createdPoster) formData.append("poster", createdPoster, "poster.webp");

        // Attempt to submit the form
        try {
            const response = await fetch("/upload/tmp", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Accept": "application/json"
                },
                body: formData
            });

            // If response is nOK, serve the HTML error in the browser console
            if (!response.ok) {
                console.error("Server Error Page HTML:", await response.text());
                return null;
            }

            // Response is ok, prepare media object
            const { path, poster } = await response.json();
            console.log("Uploaded file: ", path);
            if (poster) console.log("uploaded poster: ", poster);

            // Basic props shared by all media types
            const basePayload = {
                number: index,
                name: file.name,
                path: path,
                size: Math.round(file.size / 1024),
                type: file.type
            };

            // Build with only the basePayload (missing props will just evaluate to their defaults)
            return this.createPreview(category, basePayload);

        } catch (e) {
            console.error('Error uploading file:', e);
        }
    }

    // Create previews and media options inputs when loading a file via file input
    async loadFile() {
        // If no files selected, return early
        const files = Array.from(this.fileInput.files);
        if (!files.length) return;

        // Else: prepare the preview container for action
        this.preparePreviewContainer(files.length);

        // Normalize type input
        const category = this.getCategory(this.typeInput.value);
        if (!category) return;

        // Process all files and clear null values when done
        const promises = files.map((file, index) => {
            return this.processFile(file, index, category);
        });
        const promisedPreviews = (await Promise.all(promises)).filter(Boolean);

        // Done
        this.previews.push(...promisedPreviews);
        if (this.previews.length) {
            this.cyclePreviews(0);
        }
    }

    // FLOW 2: DISPLAY PREVIEW OF FILES PREVIOUSLY UPLOADED
    // Prefill settings object from DB item (injected in PHP component)
    prefill(settings) {
        // Keep settings object in memory for use in other methods
        this.settings = settings;

        // First set the type and trigger change event
        // Prepares container elements and options
        this.typeInput.value = settings.type;
        this.setType();
    }

    // Create previews and media options inputs when prefilling files
    // Triggered in setType (to avoid losing state)
    async executePrefill(settings) {
        // Only do prefill if settings.type differs from typeInput
        if (this.typeInput.value != settings.type) return;

        // Check if there is a valid array in the prefill
        const files = settings.files || [];
        if (!files.length) return;

        // Prepare the preview container for action
        this.preparePreviewContainer(files.length);

        // Normalize type input
        const category = this.getCategory(this.typeInput.value);
        if (!category) return;

        // Create the previews
        this.previews = files.map((file, index) => {
            // Put all possible props for all possible file types into the payload
            const payload = {
                number: index,
                name: file.name,
                path: file.path,
                size: Math.round(file.size / 1024),
                type: file.mime,
                alt: file.alt,
                category: settings.type,
                poster: file.poster_path,
                title: file.title,
                description: file.description,
                include_audio: file.has_audio
            };
            return this.createPreview(category, payload);
        });
        this.cyclePreviews(0);
    }

    // PREVIEW CYCLING LOGIC
    // Store preview and image options in this array to keep preview attached to its options container
    // Important for when using multiple images (carousel or image-list)

    // Clear the previews
    clearPreviews() {
        for (let preview of this.previews) {
            preview.remove();
        }
        this.previews = [];
        this.currentIndex = 0;
    }

    // Clear file input
    clearFileInput() {
        this.fileInput.value = "";
        this.container.setAttribute("fileselected", false);

        this.clearPreviews();
    }

    // Cycle through images (if multiple images)
    cyclePreviews(change) {
        // Move to currentIndex
        this.currentIndex += change;
        const limit = this.previews.length - 1;
        if (this.currentIndex < 0) this.currentIndex = limit;
        if (this.currentIndex > limit) this.currentIndex = 0;

        // Hide all previews
        for (let preview of this.previews) {
            preview.classList.add("removed");
        }

        // Show currently selected preview
        const selectedPreview = this.previews[this.currentIndex];
        selectedPreview.classList.remove("removed");
    }
}