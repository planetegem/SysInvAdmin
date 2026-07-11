export default class RelationshipManager {

    constructor(id, relationships = []) {

        // Prepare some elements
        this.id = id;
        this.container = document.getElementById(id);
        this.template = this.container.querySelector("template");
        this.ol = this.container.querySelector("ol");
        this.add_button = this.container.querySelector("button.add-new-relationship");
        this.fallback_text = this.container.querySelector(".fallback-text");

        // Add event listener for add button
        this.add_button.addEventListener("click", () => this.createRelationshipElement());

        // Populate with elements in case of existing item with existing relationships
        relationships.forEach((relationship) => this.createRelationshipElement(relationship.type, relationship.item));

        // Update UI even if no elements were created
        if (relationships.length == 0) this.updateUI();
    }

    createRelationshipElement(relationship = null, item = null) {
        // Get the template
        const clone = document.importNode(this.template.content, true);
        const li = clone.querySelector("li.item-relationship");
        this.ol.appendChild(li);

        // Fetch important elements
        const type_selector = li.querySelector(".relationship-type-selector");
        const target_selector = li.querySelector(".relationship-item-selector");
        const remove_button = li.querySelector(".remove-relationship");

        // Set event listeners
        // 1. button to remove relationships
        remove_button.addEventListener("click", () => {
            li.remove();
            this.updateUI();
        })

        // 2. Update UI if any values are changed
        type_selector.addEventListener("change", () => this.updateUI());
        target_selector.addEventListener("change", () => this.updateUI());

        // Set values (if applicable)
        if (relationship) type_selector.value = relationship;
        if (item) target_selector.value = item;

        // Refresh UI
        this.updateUI();
    }

    updateUI() {
        // Get all relationship elements
        const relationships = this.ol.querySelectorAll("li.item-relationship");

        // Toggle fallback if necessary
        const hasRelationships = relationships.length > 0;
        this.fallback_text.classList.toggle("hidden", hasRelationships);
        this.ol.classList.toggle("hidden", !hasRelationships);

        // Return early if no relationships
        if (!hasRelationships) return;

        // Gather all selected targets in first pass of relationships
        const selected_targets = Array.from(relationships, li =>
            li.querySelector('.relationship-item-selector').value
        ).filter(Boolean);

        // Do a 2nd pass of relationships, this time adjusting their names, ids and available options
        relationships.forEach((element, i) => {
            const type_selector = element.querySelector(".relationship-type-selector");
            const target_selector = element.querySelector(".relationship-item-selector");

            const type_id = `${this.id}[${i}][type]`;
            type_selector.id = type_id;
            type_selector.name = type_id;
            type_selector.parentElement.setAttribute("for", type_id);

            const target_id = `${this.id}[${i}][item]`;
            target_selector.id = target_id;
            target_selector.name = target_id;
            target_selector.parentElement.setAttribute("for", target_id);

            const current_value = target_selector.value;

            target_selector.querySelectorAll("option").forEach(option => {
                // Disable the option if it's selected elsewhere, EXCEPT if it's the current dropdown's choice
                const isSelectedElsewhere = selected_targets.includes(option.value);
                const isCurrentChoice = option.value === current_value;

                option.disabled = isSelectedElsewhere && !isCurrentChoice;
            });
        });
    }
}