// MEDIA MANAGER: logic to load preview from file input & change form depending on file type selected

// HTML elements used
const fileInput = document.getElementById("media-file-input");
const fileTypeSelector = document.getElementById("file_type_dropdown");
const mediaPreview = document.getElementById("media-preview");
const fileLabel = document.getElementById("file-selected-label");

// Reload preview when a new file is uploaded
if (fileInput) fileInput.addEventListener("change", (e) => {
    loadPreview();
});

// Change form when file type is selected (and clear input)
if (fileTypeSelector) fileTypeSelector.addEventListener("change", (e) => {
    clearFileInput();
    switch (fileTypeSelector.value) {
        case "image":
            fileInput.accept = "image/png, image/jpeg";
            fileInput.multiple = false;
            break;
        case "video":
            fileInput.accept = "video/*";
            fileInput.multiple = false;
            break;
    }
});

// Main function: generate preview depending on file selected
function loadPreview() {

    // 1. Start from blank canvas
    clearFilePreview();

    // 2. If no files selected, keep blank canvas
    if (fileInput.files.length === 0) {
        return;
    }

    // 3. File selected; depending on type, load preview
    mediaPreview.classList.remove("nothing-selected");
    mediaPreview.classList.add("something-selected")

    switch (fileTypeSelector.value) {
        case "image":
            let img = new Image();
            mediaPreview.appendChild(img);

            var fileReader = new FileReader();
            fileReader.onload = function () {
                img.src = fileReader.result;
            }
            fileReader.readAsDataURL(fileInput.files[0]);
            break;

    }

    // 4. Adjust file label
    fileLabel.classList.remove("nothing-selected");
    fileLabel.classList.add("something-selected");
    fileLabel.innerHTML = `Previewing ${fileInput.files[0].name}`;
}

// Clear preview (to get blank canvas)
function clearFilePreview(fullUpdate = true) {
    if (mediaPreview) {
        if (fullUpdate) {
            mediaPreview.classList.remove("something-selected");
            mediaPreview.classList.add("nothing-selected");
        }
        while (mediaPreview.firstChild) {
            mediaPreview.removeChild(mediaPreview.lastChild);
        }
    }
    if (fileLabel) {
        if (fullUpdate) {
            fileLabel.classList.remove("something-selected");
            fileLabel.classList.add("nothing-selected");
        }
        fileLabel.innerHTML = "File preview:<br>no file selected";
    }
}

// Clear file input & reload preview (should become blank)
function clearFileInput() {
    fileInput.value = "";
    loadPreview();
}
