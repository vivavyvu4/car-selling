(function () {
    'use strict';
    const rows = document.getElementById('dealer-spec-rows');
    const addButton = document.getElementById('dealer-add-spec');
    if (!rows || !addButton) return;

    const reindex = () => rows.querySelectorAll('tr').forEach((row, index) => {
        row.querySelectorAll('input').forEach((input) => {
            const field = input.name.endsWith('[label]') ? 'label' : 'value';
            input.name = 'dealer_specs[' + index + '][' + field + ']';
        });
    });

    addButton.addEventListener('click', () => {
        const index = rows.querySelectorAll('tr').length;
        const row = document.createElement('tr');
        const labelCell = document.createElement('td');
        const valueCell = document.createElement('td');
        const actionCell = document.createElement('td');
        const label = document.createElement('input');
        const value = document.createElement('input');
        const remove = document.createElement('button');

        label.type = value.type = 'text';
        label.className = value.className = 'widefat';
        label.name = 'dealer_specs[' + index + '][label]';
        value.name = 'dealer_specs[' + index + '][value]';
        label.placeholder = 'Hạng mục thông số';
        value.placeholder = 'Giá trị';
        remove.type = 'button';
        remove.className = 'button dealer-remove-spec';
        remove.textContent = 'Xóa';

        labelCell.appendChild(label);
        valueCell.appendChild(value);
        actionCell.appendChild(remove);
        row.append(labelCell, valueCell, actionCell);
        rows.appendChild(row);
        label.focus();
    });

    rows.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof Element)) return;
        const remove = target.closest('.dealer-remove-spec');
        if (!remove) return;
        remove.closest('tr').remove();
        reindex();
    });
})();
