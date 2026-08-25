/* Capa independiente del módulo de pagos para evitar depender de caché de app.js. */
if (typeof window.cargarPagosGlobal !== "function") {
    const escapePago = valor => String(valor ?? "").replace(/[&<>'"]/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;","'":"&#39;",'"':"&quot;"})[c]);
    const monedaPago = valor => new Intl.NumberFormat("es-CO", {style:"currency",currency:"COP",maximumFractionDigits:0}).format(Number(valor || 0));
    const fechaPago = (valor, hora = false) => new Intl.DateTimeFormat("es-CO", {dateStyle:"medium", ...(hora ? {timeStyle:"medium"} : {})}).format(new Date(String(valor).replace(" ", "T")));
    const clasePago = estado => String(estado).toLowerCase() === "pendiente" ? "mantenimiento" : "disponible";

    window.cargarReservasGlobal = function () {
        const tabla = document.getElementById("tablaReservas");
        if (!tabla) return;
        fetch("php/reservas_listar.php").then(r => r.json()).then(data => {
            if (!data.exito) throw new Error(data.mensaje);
            tabla.innerHTML = "";
            if (!data.reservas.length) { tabla.innerHTML = '<tr><td colspan="8" style="text-align:center;">No hay reservas registradas.</td></tr>'; return; }
            window.reservasDisponiblesPago = data.reservas;
            data.reservas.forEach(r => {
                const fila = document.createElement("tr");
                fila.innerHTML = `<td>R${r.id}</td><td>${escapePago(r.cliente_nombre)}</td><td>${escapePago(r.marca)} ${escapePago(r.modelo)}</td><td>${fechaPago(r.fecha_inicio)}</td><td>${fechaPago(r.fecha_fin)}</td><td>${monedaPago(r.costo_total)}</td><td><span class="estado ${clasePago(r.estado)}">${escapePago(r.estado)}</span></td><td></td>`;
                if (!r.tiene_pago && String(r.estado).toLowerCase() === "pendiente") {
                    const boton = document.createElement("button"); boton.className = "btn-verde"; boton.title = "Registrar pago"; boton.innerHTML = '<i class="fa-solid fa-file-invoice-dollar"></i>';
                    boton.addEventListener("click", () => window.abrirPagoReserva(r)); fila.lastElementChild.appendChild(boton);
                } else fila.lastElementChild.textContent = r.tiene_pago ? "Pago registrado" : "Sin acciones";
                tabla.appendChild(fila);
            });
        }).catch(() => { tabla.innerHTML = '<tr><td colspan="8" style="text-align:center;">No se pudieron cargar las reservas.</td></tr>'; });
    };

    window.abrirPagoReserva = function (r) {
        document.getElementById("pagoId").value = "";
        document.getElementById("clientePago").value = `${r.cliente_nombre} (${r.cliente_documento})`;
        document.getElementById("selectReservaPago").value = r.id;
        document.getElementById("valorPago").value = r.costo_total;
        document.getElementById("metodoPago").value = "";
        document.getElementById("modalPagoOverlay").style.display = "flex";
    };
    window.abrirPagoExistente = function (p) {
        document.getElementById("pagoId").value = p.id;
        document.getElementById("clientePago").value = `${p.cliente_nombre} (${p.cliente_documento})`;
        document.getElementById("selectReservaPago").value = p.reserva_id;
        document.getElementById("valorPago").value = p.monto;
        document.getElementById("metodoPago").value = p.metodo_pago;
        document.getElementById("modalPagoOverlay").style.display = "flex";
    };
    window.abrirPagoDisponible = function () {
        const pendientes = (window.reservasDisponiblesPago || []).filter(r => !r.tiene_pago && String(r.estado).toLowerCase() === "pendiente");
        if (pendientes.length === 1) { window.abrirPagoReserva(pendientes[0]); return; }
        if (!pendientes.length) { alert("No hay reservas pendientes de pago."); return; }
        window.cambiarSeccion("reservas");
        alert("Selecciona el botón de pago de la reserva que deseas registrar.");
    };
    const cerrarPago = () => { document.getElementById("modalPagoOverlay").style.display = "none"; document.getElementById("formPago")?.reset(); };
    window.toggleFormularioPago = cerrarPago;

    window.cargarPagosGlobal = function () {
        const tabla = document.getElementById("tablaPagos");
        if (!tabla) return;
        fetch("php/pagos_listar.php").then(r => r.json()).then(data => {
            if (!data.exito) throw new Error(data.mensaje);
            tabla.innerHTML = "";
            if (!data.pagos.length) { tabla.innerHTML = '<tr><td colspan="8" style="text-align:center;">No hay pagos registrados.</td></tr>'; return; }
            data.pagos.forEach(p => {
                const fila = document.createElement("tr");
                fila.innerHTML = `<td>P${p.id}</td><td>${escapePago(p.cliente_nombre)}</td><td>R${p.reserva_id}</td><td>${fechaPago(p.fecha_pago, true)}</td><td>${escapePago(p.metodo_pago)}</td><td>${monedaPago(p.monto)}</td><td><span class="estado ${clasePago(p.estado_pago)}">${escapePago(p.estado_pago || "Sin estado")}</span></td><td></td>`;
                const registrar = document.createElement("button"); registrar.className = "btn-verde"; registrar.title = "Registrar pago"; registrar.innerHTML = '<i class="fa-solid fa-file-invoice-dollar"></i>';
                registrar.addEventListener("click", () => window.abrirPagoExistente(p)); fila.lastElementChild.appendChild(registrar);
                const boton = document.createElement("button"); boton.className = "btn-ver"; boton.title = "Ver comprobante"; boton.innerHTML = '<i class="fa-solid fa-eye"></i>';
                boton.addEventListener("click", () => window.verComprobante(p.id)); fila.lastElementChild.appendChild(boton); tabla.appendChild(fila);
            });
        }).catch(() => { tabla.innerHTML = '<tr><td colspan="8" style="text-align:center;">No se pudieron cargar los pagos.</td></tr>'; });
    };

    window.verComprobante = function (id) {
        const modal = document.getElementById("modalComprobanteOverlay"), contenido = document.getElementById("contenidoComprobante");
        modal.style.display = "flex"; contenido.textContent = "Cargando comprobante...";
        fetch(`php/pagos_comprobante.php?id=${encodeURIComponent(id)}`).then(r => r.json()).then(data => {
            if (!data.exito) throw new Error(data.mensaje); const p = data.pago;
            contenido.innerHTML = `<section><h3>Transacción</h3><p><strong>ID de pago:</strong> P${p.id}</p><p><strong>Fecha y hora:</strong> ${fechaPago(p.fecha_pago, true)}</p></section><section><h3>Cliente</h3><p><strong>Nombre:</strong> ${escapePago(p.cliente_nombre)}</p><p><strong>Documento:</strong> ${escapePago(p.cliente_documento)}</p></section><section><h3>Vehículo alquilado</h3><p><strong>Vehículo:</strong> ${escapePago(p.marca)} ${escapePago(p.modelo)}</p><p><strong>Placa:</strong> ${escapePago(p.placa || "No registrada")}</p></section><section><h3>Desglose financiero</h3><p><strong>Monto pagado:</strong> ${monedaPago(p.monto)}</p><p><strong>Método:</strong> ${escapePago(p.metodo_pago)}</p></section><section><h3>Usuario que registró el pago</h3><p><strong>Nombre:</strong> ${escapePago(p.usuario_nombre || "No disponible")}</p><p><strong>Cargo / rol:</strong> ${escapePago(p.usuario_rol || "No disponible")}</p><p><strong>Correo:</strong> ${escapePago(p.usuario_email || "No disponible")}</p></section>`;
        }).catch(error => { contenido.textContent = error.message || "No fue posible cargar el comprobante."; });
    };
    window.cerrarComprobante = () => { document.getElementById("modalComprobanteOverlay").style.display = "none"; };

    document.addEventListener("DOMContentLoaded", () => {
        document.getElementById("formPago")?.addEventListener("submit", e => {
            e.preventDefault(); fetch("php/pagos_registrar.php", {method:"POST", body:new FormData(e.currentTarget)}).then(r => r.json()).then(data => {
                if (!data.exito) throw new Error(data.mensaje); cerrarPago(); window.cargarPagosGlobal(); window.cargarReservasGlobal();
            }).catch(error => alert(error.message || "No se pudo registrar el pago."));
        });
        document.querySelectorAll('[data-contenedor="reservas"], [data-contenedor="pagos"]').forEach(b => b.addEventListener("click", () => b.dataset.contenedor === "reservas" ? window.cargarReservasGlobal() : window.cargarPagosGlobal()));
        window.cargarReservasGlobal(); window.cargarPagosGlobal();
    });
}
