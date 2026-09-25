import "@google/model-viewer";

// The MediaPreviewFactory is a class that creates and customizes HTML elements for the MediaManager
// It provides static methods to handle all main template cloning logic
// And renaming of form elements to make them functional for the backend
export default class MediaPreviewFactory {

    // 1. Helper function to find relevant template and clone it
    static createFromTemplate(container, templateSelector) {
        // Try the selector
        const template = container.querySelector(templateSelector);
        if (!template) throw new Error(`Template missing: ${templateSelector}`);

        // Check how many children the template has (should be 1)
        const elementCount = template.content.children.length;

        if (elementCount === 0)
            throw new Error(`Template '${templateSelector}' must contain at least one top-level element.`);

        if (elementCount > 1)
            console.error(
                `Template '${templateSelector}' has ${elementCount} top-level elements. ` +
                `Templates should have exactly one root element. Defaulting to the first element.`
            );

        // Clone the template and return the first child as container
        const clone = document.importNode(template.content, true);
        return clone.firstElementChild;
    }

    // 2. Helper function to configure individual input elements
    // => handles renaming (to create arrays of inputs in the laravel request)
    // => relinking to their labels when renamed
    // => and customization based on type
    static actualizeInput(input, { name, value, parentForId }) {
        if (!input) return;
        if (name) input.name = name;
        if (value !== undefined) input.value = value;
        if (parentForId) {
            input.id = parentForId;
            input.parentElement?.setAttribute("for", parentForId);
        }

        return input;
    }

    // 3. Helper function to configure checkboxes
    // => handles renaming (to create arrays of inputs in the laravel request)
    // => and sync with hidden input (to give negative response as well)
    static actualizeCheckbox(checkbox, { name, value }) {
        if (!checkbox) return;
        if (name) checkbox.name = name;
        if (value) checkbox.checked = true;

        const hiddenInput = checkbox.parentElement?.querySelector("input[type='hidden']");
        if (hiddenInput) hiddenInput.name = name;

        return checkbox;
    }

    // 4. Helper function to generate file description
    static createFileDescription(container, { name, path, size, type }) {
        const file_name = container.querySelector("p.name span");
        file_name.innerHTML = name;
        const file_location = container.querySelector("p.location span");
        file_location.innerHTML = `<a href="${path}" title="${path}">${path}</a>`;
        const file_size = container.querySelector("p.size span");
        file_size.innerHTML = `${size}kB`;
        const file_type = container.querySelector("p.type span");
        file_type.innerHTML = type;
    }

    // 5. Helper function to do final evaluation of container
    static attachContainer(context, container) {
        // Create an observer to track size updates to the preview figure
        const previewFigure = container.querySelector(".media-preview");
        const observer = new ResizeObserver((entries) => {
            for (const entry of entries) {
                // entry.contentRect or entry.borderBoxSize gives accurate dimensions
                const { width, height } = entry.contentRect;

                // Skip initial 0x0 render if element isn't visible yet
                if (width === 0 && height === 0) return;

                // Perform your size-dependent logic here...
                // If preview figure is a lot heigher than it is wide, switch to tall UI
                if (height > 1.15 * width) container.classList.add("tall");
            }
        });
        // Start watching the figure element
        observer.observe(previewFigure);

        // Attach the container
        context.content.appendChild(container);
    }

    // 6. ACTUAL PREVIEW FACTORY METHODS
    // A. Image Preview
    static createImagePreview(context, { number, image, name, alt, path, size, type }) {
        const container = this.createFromTemplate(context.container, "template.image-preview");
        const prefix = `${context.id}[files][${number}]`;

        // 1. Configure inputs
        const alt_input = this.actualizeInput(container.querySelector('.image-alt-input'), {
            name: `${prefix}[alt]`,
            value: alt,
            parentForId: `${prefix}[alt]`
        });
        const path_input = this.actualizeInput(container.querySelector(".image-path-input"), {
            name: `${prefix}[path]`,
            value: path
        });
        const name_input = this.actualizeInput(container.querySelector(".image-name-input"), {
            name: `${prefix}[name]`,
            value: name
        });

        // 2. Webp checkbox
        const webp_checkbox = this.actualizeCheckbox(container.querySelector(".image-webp-input"), {
            name: `${prefix}[convert_to_webp]`
        });
        const isAllowedMime = ['image/png', 'image/jpeg', 'image/jpg'].includes(type);
        webp_checkbox.parentElement?.classList.toggle("removed", !isAllowedMime);

        // 3. Image details
        this.createFileDescription(container.querySelector(".media-properties"), {
            name: name,
            path: "/storage/" + path,
            size: size,
            type: type
        });

        // 4. Image preview
        const previewFigure = container.querySelector(".media-preview");
        const img = new Image();
        img.onload = () => this.attachContainer(context, container);
        img.onerror = () => this.attachContainer(context, container);
        previewFigure.appendChild(img);
        img.src = "/storage/" + path;

        // 5. Done
        return container;
    }

    // B. Video Preview
    static createVideoPreview(context, { number, name, path, size, category, type, title, description, include_audio, poster }) {
        const container = this.createFromTemplate(context.container, "template.video-preview");
        const prefix = `${context.id}[files][${number}]`;

        // 1. Input elements
        // 1a. Title & description
        const title_input = this.actualizeInput(container.querySelector('.video-title-input'), {
            name: `${prefix}[title]`,
            value: title,
            parentForId: `${prefix}[title]`
        });
        const description_input = this.actualizeInput(container.querySelector('.video-description-input'), {
            name: `${prefix}[description]`,
            value: description,
            parentForId: `${prefix}[description]`
        });

        // 1b. Hidden inputs: path, file name and poster
        const path_input = this.actualizeInput(container.querySelector(".video-path-input"), {
            name: `${prefix}[path]`,
            value: path
        });
        const name_input = this.actualizeInput(container.querySelector(".video-name-input"), {
            name: `${prefix}[name]`,
            value: name
        });
        const poster_input = this.actualizeInput(container.querySelector(".video-poster-input"), {
            name: `${prefix}[poster]`,
            value: poster
        });

        // 2. Audio checkbox (hidden and unchecked if gifv)
        const audio_checkbox = this.actualizeCheckbox(container.querySelector(".video-audio-input"), {
            name: `${prefix}[include_audio]`,
            value: include_audio
        });
        const showCheckbox = (category === "video");
        audio_checkbox.parentElement?.classList.toggle("removed", !showCheckbox);

        // 3. Media properties (file type, file size, location)
        this.createFileDescription(container.querySelector(".media-properties"), {
            name: name,
            path: "/storage/" + path,
            size: size,
            type: type
        });

        // 4. Preview
        const previewFigure = container.querySelector(".media-preview");
        const video = document.createElement("video");
        video.controls = (category === "video");
        video.autoplay = (category === "gifv");
        video.loop = (category === "gifv");
        video.muted = (category === "video");
        video.src = "/storage/" + path;
        previewFigure.appendChild(video);

        // 5. Done
        this.attachContainer(context, container);
        return container;
    }

    // C. 3D Model Preview
    static createModelPreview(context, { number, name, path, alt, size, type, poster }) {
        const container = this.createFromTemplate(context.container, "template.model-preview");
        const prefix = `${context.id}[files][${number}]`;

        // 1. Input elements
        // 1a. Regular inputs
        const alt_input = this.actualizeInput(container.querySelector('.model-alt-input'), {
            name: `${prefix}[alt]`,
            value: alt,
            parentForId: `${prefix}[alt]`
        });

        // 1b. Hidden inputs
        const path_input = this.actualizeInput(container.querySelector(".model-path-input"), {
            name: `${prefix}[path]`,
            value: path
        });
        const name_input = this.actualizeInput(container.querySelector(".model-name-input"), {
            name: `${prefix}[name]`,
            value: name
        });
        const poster_input = this.actualizeInput(container.querySelector(".model-poster-input"), {
            name: `${prefix}[poster]`,
            value: poster
        });

        // 2. Media properties
        this.createFileDescription(container.querySelector(".media-properties"), {
            name: name,
            path: "/storage/" + path,
            size: size,
            type: type
        });

        // 3. Preview (using model-viewer web component)
        const previewFigure = container.querySelector(".media-preview");
        const viewer = document.createElement("model-viewer");
        previewFigure.appendChild(viewer);

        viewer.src = "/storage/" + path;
        viewer.alt = alt;
        viewer.autoRotate = true;
        viewer.cameraControls = true;
        viewer.ar = true;

        // 5. Done
        this.attachContainer(context, container);
        return container;
    }
}
