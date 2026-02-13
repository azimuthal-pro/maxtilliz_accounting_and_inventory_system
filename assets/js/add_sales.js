document.addEventListener('DOMContentLoaded', () => {

    // Fetch price when item changes
    document.body.addEventListener('change', function (e) {
        if (!e.target.classList.contains('item-select')) return;

        const item = e.target.value;
        const priceInput = e.target.closest('.item-row').querySelector('.price');

        if (!item) {
            priceInput.value = '';
            return;
        }

        fetch(`../Sales/get_item_price.php?item=${encodeURIComponent(item)}`)
            .then(res => res.json())
            .then(data => {
                priceInput.value = data.price || '';
            })
            .catch(() => {
                priceInput.value = '';
            });
    });

    // Add new item row
    document.getElementById('addItem').addEventListener('click', () => {
        const container = document.getElementById('itemsContainer');
        const row = container.querySelector('.item-row').cloneNode(true);

        row.querySelector('.item-select').value = '';
        row.querySelector('.qty').value = '';
        row.querySelector('.price').value = '';

        container.appendChild(row);
    });

    // Remove item row
    document.getElementById('itemsContainer').addEventListener('click', (e) => {
        if (!e.target.classList.contains('remove-item')) return;

        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            e.target.closest('.item-row').remove();
        }
    });

});