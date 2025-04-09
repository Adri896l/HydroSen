function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.querySelector('.table');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let match = false;

        for (let j = 0; j < cells.length; j++) {
            const cell = cells[j];
            if (cell) {
                const text = cell.textContent || cell.innerText;
                if (text.toUpperCase().indexOf(filter) > -1) {
                    match = true;
                    break;
                }
            }
        }

        if (match) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

// Asignar la función de búsqueda al evento 'input' del campo de búsqueda
document.getElementById('searchInput').addEventListener('input', filterTable);

// Asignar la función de búsqueda al botón de búsqueda (opcional)
document.getElementById('searchButton').addEventListener('click', filterTable);


