// MEDIA MANAGER: logic to load preview from file input & change form depending on file type selected
export default class MediaManager {

    constructor(id, labels) {
        // Find main container
        this.id = id;
        this.container = document.getElementById(id);

        // Important component elements
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

        this.labels = labels;
    }

    // File type selection
    setType() {
        this.clearFileInput();
        this.container.setAttribute("mediatype", this.typeInput.value);
        this.content.classList.add("removed");

        switch (this.typeInput.value) {

            case "image":
                this.fileInput.disabled = false;
                this.fileInput.accept = "image/*";
                this.fileInput.multiple = false;

                break;

            case "carousel":
            case "image-list":
                this.fileInput.disabled = false;
                this.fileInput.accept = "image/*";
                this.fileInput.multiple = true;

                break;
            case "none":
            default:
                this.fileInput.disabled = true;
                this.clearPreviews();

                break;
        }

        // If updating an item and switching between types, retain state
        if (this.settings) this.executePrefill(this.settings);

    }

    // Create previews and media options inputs when loading a file via file input
    async loadFile() {
        // If no files selected, return early
        if (this.fileInput.files.length === 0) return;

        // File selected; depending on type, load preview
        switch (this.typeInput.value) {
            case "image":
            case "carousel":
            case "image-list":
                this.clearPreviews();
                this.content.classList.remove("removed");

                let previewsCreated = 0;

                this.leftButton.classList.toggle("removed", this.fileInput.files.length < 2);
                this.rightButton.classList.toggle("removed", this.fileInput.files.length < 2);

                for (let i = 0; i < this.fileInput.files.length; i++) {
                    const file = this.fileInput.files[i];

                    // Temp save file to server
                    const formData = new FormData();
                    formData.append('image', file);

                    try {
                        const response = await fetch("/upload/tmp", {
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        if (response.ok) {
                            const data = await response.json();

                            console.log('Uploaded temporarily:', data.path);
                            const path = data.path;
                            const img = new Image();

                            img.onload = async () => {
                                // Create the preview
                                const preview = await this.createImagePreview(i, img, file.name, "", path);
                                this.previewedImages.push(preview);

                                // On the last file load: select correct preview
                                previewsCreated++;
                                if (previewsCreated === this.fileInput.files.length) this.cyclePreviews(0);
                            }
                            img.src = "/storage/" + path;

                        } else {
                            const errorHtml = await response.text();
                            console.error('Server Error Page HTML:', errorHtml);
                        }

                    } catch (e) {
                        console.error('Error uploading file:', e);
                    }
                }
                break;

        }
    }

    // Prefill logic: used when updating existing item
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
        // Only do prefill if settings.type eaquals typeInput
        if (this.typeInput.value != settings.type) return;

        // Prefill the html elements with DB data
        switch (settings.type) {
            case "image":
            case "carousel":
            case "image-list":
                this.clearPreviews();
                this.content.classList.remove("removed");

                this.leftButton.classList.toggle("removed", settings.files.length < 2);
                this.rightButton.classList.toggle("removed", settings.files.length < 2);

                let previewsCreated = 0;

                for (let i = 0; i < settings.files.length; i++) {
                    const file = settings.files[i];
                    const img = new Image();
                    img.onload = async (e) => {
                        const preview = await this.createImagePreview(i, img, file.name, file.alt, file.path);
                        this.previewedImages.push(preview);

                        previewsCreated++;
                        if (previewsCreated === settings.files.length) this.cyclePreviews(0);
                    };
                    
                    img.onerror = async (e) => {
                        const preview = await this.createImagePreview(i, img, "not found", "", "not found");
                        this.previewedImages.push(preview);

                        previewsCreated++;
                        if (previewsCreated === settings.files.length) this.cyclePreviews(0);
                    };

                    const src = '/storage/' + file.path;
                    img.src = '/storage/' + file.path;
                }

                break;
        }
    }


    async createImagePreview(number, image, name, alt, path) {

        // Get the template
        const template = this.container.querySelector("template.image-preview");
        const clone = document.importNode(template.content, true);
        const container = clone.querySelector(".media-preview-container");
        this.content.appendChild(container);

        // Configure the template
        // 1. image alt input
        const image_alt = document.getElementById(`${this.id}[alt]`);
        const id = image_alt.id + `[${number}]`;
        image_alt.id = id;
        image_alt.name = id;
        image_alt.parentElement.setAttribute("for", id);
        image_alt.value = alt;

        // 2. Webp checkbox
        const webp_checkbox = container.querySelector(".checkbox");
        const webp_checkbox_input = webp_checkbox.querySelector("input[type='checkbox']");
        webp_checkbox_input.name = webp_checkbox_input.name + `[${number}]`;
        const webp_hidden_input = webp_checkbox.querySelector("input[type='hidden']");
        webp_hidden_input.name = webp_hidden_input.name + `[${number}]`;

        // 3. Hidden input for path & name
        const image_path = container.querySelector(".image_path");
        image_path.name = image_path.name + `[${number}]`;
        image_path.value = path;
        const image_name = container.querySelector(".image_name");
        image_name.name = image_name.name + `[${number}]`;
        image_name.value = name;

        // 4. Image details
        const image_description = container.querySelector(".image-description");
        const image_details = image ? await this.getImageDetails(image) : null;
        if (image_details && image_details.type) {
            const message = document.createElement("p");
            const isUrl = image_details.type === "URL";

            message.innerHTML =
                `<u>${this.labels.image_header}</u>:<br>
                ${this.labels.location}: <a href="${image_details.location}" title="${image_details.location}">${image_details.location}</a><br>
                ${this.labels.file_size}: ${image_details.size}kB<br>
                ${this.labels.file_type}: ${image_details.mime}`;

            const isAllowedMime = ['image/png', 'image/jpeg', 'image/jpg'].includes(image_details.mime);
            webp_checkbox.classList.toggle("removed", !isAllowedMime);

            image_description.appendChild(message);
        }

        // 5. Image preview
        const previewFigure = container.querySelector(".preview-figure");
        previewFigure.appendChild(image);
        const previewLabel = container.querySelector(".preview-label");
        previewLabel.innerHTML = `Previewing ${name}`;

        return container;
    }

    // Store preview and image options in this array to keep preview attached to its options container
    // Important for when using multiple images (carousel or image-list)
    previewedImages = [];

    // Clear the previews
    clearPreviews() {
        for (let preview of this.previewedImages) {
            preview.remove();
        }
        this.previewedImages = [];
        this.currentIndex = 0;
    }

    // Clear file input
    clearFileInput() {
        this.fileInput.value = "";
        this.clearPreviews();
    }

    // Cycle through images (if multiple images)
    currentIndex = 0;
    cyclePreviews(change) {
        // Move to currentIndex
        this.currentIndex += change;
        const limit = this.previewedImages.length - 1;
        if (this.currentIndex < 0) this.currentIndex = limit;
        if (this.currentIndex > limit) this.currentIndex = 0;

        // Hide all previews
        for (let preview of this.previewedImages) {
            preview.classList.add("removed");
        }

        // Show currently selected preview
        const selectedPreview = this.previewedImages[this.currentIndex];
        selectedPreview.classList.remove("removed");

    }

    // Retrieve details object from an image object
    async getImageDetails(img) {
        const src = img.src;

        // Default structure to return
        const details = {
            type: null,
            location: null,
            mime: null,
            size: null
        };

        // If no src, return early
        if (!src) return details;

        try {
            const urlObject = new URL(src);
            details.location = src;

            // Fetch file size and Mime type via HEAD request
            try {
                const response = await fetch(src, { method: 'HEAD' });

                // MIME
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.startsWith('image/')) {
                    // Split on semicolon to strip parameters like "image/jpeg; charset=UTF-8"
                    details.mime = contentType.split(';')[0].trim();
                }

                // SIZE
                const bytes = response.headers.get('content-length');
                if (bytes) {
                    details.size = Math.floor(parseInt(bytes, 10) / 1024);

                    // TYPE
                    details.type = "URL";
                }


            } catch (e) {
                // If the server blocks CORS, a HEAD request fails.
                console.warn(`Could not fetch size for ${src} due to CORS restrictions.`);
            }

        } catch (e) {
            console.error("Invalid image src string provided.", e);
        }
        return details;
    }

}