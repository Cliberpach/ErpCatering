
//============== LIMPIAR UNA TABLA ========
function limpiarTabla(idTabla) {

    const tbody =   document.querySelector(`#${idTabla}`);
    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
    }

}