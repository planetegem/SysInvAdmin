// USED IN FORMS TO CREATE AND UPDATE RELATIONSHIPS
// Just adds some minor js interactions to the UI

export default class RelationshipForm {

    constructor() {

        try {
            // Prepare form parent; thorw error if non-existant
            this.form = document.querySelector('.db-form.relationship');
            if (!this.form) throw new Error("form element not found");

            // Do the same for all input elements being used
            this.typeSelector = this._getElement('#relationship_type');
            this.subjectLabel = this._getElement('#relationship_subject_label');
            this.objectLabel = this._getElement('#relationship_object_label');
            this.subjectDescriptor = this._getElement('#relationship_subject_descriptor');
            this.objectDescriptor = this._getElement('#relationship_object_descriptor');

            // Set event listeners
            this.typeSelector.addEventListener("change", () => this.applyType());
            this.applyType();

            this.subjectLabel.addEventListener("input", () => this.setObjectLabel());
            this.subjectDescriptor.addEventListener("input", () => this.setObjectDescriptor());

        } catch (e) {
            console.error(`Error constructing RelationshipForm: ${e.message}...`);
        }
    }

    // Helper: find element, but throw error if null
    _getElement(selector) {
        const el = this.form.querySelector(selector);
        if (!el) throw new Error(`${selector} not found`);
        return el;
    }

    // If the relationship type is set to horizontal, disable the object label & descriptor 
    // (as this is the same as the subject label & descriptor) 
    type = null;
    applyType() {
        this.type = this.typeSelector.value;
        this.objectLabel.disabled = (this.type === "horizontal");
        this.objectDescriptor.disabled = (this.type === "horizontal");
    }

    // Copy subject label to object label if horizontal relationship
    setObjectLabel() {
        if (this.type === "horizontal")
            this.objectLabel.value = this.subjectLabel.value;
    }
    // Copy subject descriptor to object descriptor if horizontal relationship
    setObjectDescriptor() {
        if (this.type === "horizontal")
            this.objectDescriptor.value = this.subjectDescriptor.value;
    }
}