// CATEGORY MANAGER: add categories to item, using an input with list (for autocomplete)

// Count amount of times that categories are added
// Used to determine order of elements in collection
let categoryEventCounter = 0;

// Check input box that represents category
function selectCategory(inputElement) {
    inputElement.checked = true;

    categoryEventCounter++;
    inputElement.parentElement.parentElement.style.order = categoryEventCounter.toString();
}

// Check if input category corresponds to existing checkbox
function checkExistingCategories(input) {
    let categories = document.querySelectorAll(".category-option");

    for (let category of categories) {
        if (category.value === input) {
            selectCategory(category);
            return true;
        }
    }

    return false;
}

// Create new checkbox from html template if not
function createCategoryInput(value, prefix) {
    const clone = document.importNode(document.getElementById(`${prefix}example-category`).content, true);
    const inputElement = clone.querySelector("input");

    inputElement.value = value;
    selectCategory(inputElement);
    clone.querySelector("p").innerText = value;

    document.getElementById(`${prefix}category_selection`).appendChild(clone);
}

// main function
function addCategory(e, prefix) {

    e.preventDefault();

    const input = (prefix === 'hidden_') ? hiddenCategoryInput : categoryInput;
    const value = input.value;

    if (value != "" && !checkExistingCategories(value))
        createCategoryInput(value, prefix);

    input.value = "";
}

// allow adding category by pressing enter
const categoryInput = document.getElementById("category-collection-input");
const hiddenCategoryInput = document.getElementById("hidden_category-collection-input");

if (categoryInput) categoryInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
        e.preventDefault();
        addCategory(e, '');
    }
});
if (hiddenCategoryInput) hiddenCategoryInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
        e.preventDefault();
        addCategory(e, 'hidden_');
    }
});