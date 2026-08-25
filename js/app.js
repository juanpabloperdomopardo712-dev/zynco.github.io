/*═══════════════════════════════════════════════════════════════════════════
                    ZYNCO — LÓGICA DE LA APLICACIÓN (UNIFICADA)
    Archivo único: fusiona app.js + pagos.js (sin duplicar lógica de
    reservas/pagos ni funciones repetidas).
═══════════════════════════════════════════════════════════════════════════*/

// Declarar la función globalmente para sincronizar los datos de la sesión actual
window.sincronizarDatosSesion = function() {
    fetch("php/sesion_actual.php")
        .then(res => res.json())
        .then(data => {
            console.log("Respuesta de sesión actual:", data);

            if (data.exito && data.usuario) {
                const u = data.usuario;

                // 1. Rellenar la tarjeta de perfil en la sección usuarios
                const lblNombre = document.getElementById("perfilNombreSession");
                const lblCorreo = document.getElementById("perfilCorreoSession");
                const lblRol = document.getElementById("perfilRolSession");

                if (lblNombre) lblNombre.textContent = u.nombre;
                if (lblCorreo) lblCorreo.textContent = u.email;
                if (lblRol) lblRol.textContent = u.rol;

                // 2. Sincronizar el cuadrito superior de la cabecera (rolHeader / .rol-usuario-dinamico)
                const elementosRol = document.querySelectorAll(".rol-usuario-dinamico");
                elementosRol.forEach(el => {
                    el.textContent = u.rol;
                });
            } else {
                console.warn("No se pudo obtener la sesión actual:", data.mensaje);
            }
        })
        .catch(err => console.error("Error al sincronizar la sesión:", err));
};

// Declarar la función globalmente para cambiar de sección
window.cambiarSeccion = function(idContenedor) {
    const dashboard = document.getElementById("dashboard");
    if (!dashboard) return;

    // Buscar todas las secciones con la clase .contenedor dentro del dashboard
    const secciones = dashboard.querySelectorAll(".contenedor");

    secciones.forEach(seccion => {
        if (seccion.id === idContenedor) {
            seccion.style.display = "block";
            seccion.classList.add("activo");

            // Si entramos a usuarios, sincronizamos sesión y cargamos la tabla
            if (idContenedor === "usuarios") {
                if (typeof window.sincronizarDatosSesion === "function") {
                    window.sincronizarDatosSesion();
                }
                if (typeof window.cargarUsuariosGlobal === "function") {
                    window.cargarUsuariosGlobal();
                }
            }

            if (idContenedor === "inicio" && typeof window.cargarInicioGlobal === "function") {
                window.cargarInicioGlobal();
            }
            if (idContenedor === "reportes" && typeof window.cargarReportesGlobal === "function") {
                window.cargarReportesGlobal();
            }
            if (idContenedor === "reservas" && typeof window.cargarReservasGlobal === "function") {
                window.cargarReservasGlobal();
            }
            if (idContenedor === "pagos" && typeof window.cargarPagosGlobal === "function") {
                window.cargarPagosGlobal();
            }
            if (idContenedor === "clientes" && typeof window.cargarClientesGlobal === "function") {
                window.cargarClientesGlobal();
            }
        } else {
            seccion.style.display = "none";
            seccion.classList.remove("activo");
        }
    });

    // Actualizar el título de la página en la cabecera
    const tituloPagina = document.getElementById("tituloPagina");
    if (tituloPagina) {
        const btnActivo = document.querySelector(`[data-contenedor="${idContenedor}"]`);
        if (btnActivo) {
            const span = btnActivo.querySelector("span");
            if (span) tituloPagina.textContent = span.textContent;
        }
    }

    // Actualizar clase activa en los botones del menú lateral
    const botonesMenu = document.querySelectorAll(".sidebar-menu button");
    botonesMenu.forEach(b => {
        if (b.getAttribute("data-contenedor") === idContenedor) {
            b.classList.add("activo");
        } else {
            b.classList.remove("activo");
        }
    });
};

document.addEventListener("DOMContentLoaded", () => {

    /*═══════════════════════════════════════════════════════════════════════
      1. REFERENCIAS A ELEMENTOS DEL DOM
    ═══════════════════════════════════════════════════════════════════════*/

    const login = document.getElementById("login");
    const dashboard = document.getElementById("dashboard");
    const loginForm = document.getElementById("loginForm");
    const inputUsuario = document.getElementById("usuario");
    const inputPassword = document.getElementById("password");
    const btnCerrarAdmin = document.getElementById("cerrarSesion");
    const tituloPagina = document.getElementById("tituloPagina");

    // Sección de Usuarios (CRUD)
    const formularioUsuario = document.querySelector("#usuarios .formulario-crud") || document.getElementById("formularioUsuario");
    const inputUsuarioIdEditar = document.getElementById("usuarioIdEditar");
    const inputUsuarioId = document.getElementById("usuarioId");
    const inputUsuarioNombre = document.getElementById("usuarioNombre");
    const inputUsuarioCorreo = document.getElementById("usuarioCorreo");
    const inputUsuarioRol = document.getElementById("usuarioRol");
    const inputUsuarioTelefono = document.getElementById("usuarioTelefono");
    const inputUsuarioDireccion = document.getElementById("usuarioDireccion");
    const inputUsuarioPassword = document.getElementById("usuarioPassword");
    const btnGuardarUsuario = document.getElementById("btnGuardarUsuario");
    const btnCancelarEdicionUsuario = document.getElementById("btnCancelarEdicionUsuario");
    const tablaUsuarios = document.getElementById("tablaUsuarios");


    /*═══════════════════════════════════════════════════════════════════════
      2. UTILIDADES Y MENSAJES
    ═══════════════════════════════════════════════════════════════════════*/

    function mostrarSoloLogin() {
        if (login) login.style.display = "flex";
        if (dashboard) dashboard.style.display = "none";
    }

    function mostrarMensaje(idAviso, contenedorForm, mensaje, color) {
        if (!contenedorForm) return;
        let aviso = document.getElementById(idAviso);

        if (!aviso) {
            aviso = document.createElement("p");
            aviso.id = idAviso;
            aviso.style.marginTop = "10px";
            aviso.style.fontSize = "14px";
            contenedorForm.appendChild(aviso);
        }

        aviso.style.color = color || "#ff7c7c";
        aviso.textContent = mensaje;
    }

    function mostrarErrorLogin(mensaje) {
        mostrarMensaje("loginError", loginForm, mensaje, "#ff7c7c");
    }

    function limpiarErrorLogin() {
        const aviso = document.getElementById("loginError");
        if (aviso) aviso.remove();
    }

    function mostrarMensajeUsuarios(mensaje, color) {
        mostrarMensaje("usuariosMensaje", formularioUsuario, mensaje, color);
    }

    function limpiarMensajeUsuarios() {
        const aviso = document.getElementById("usuariosMensaje");
        if (aviso) aviso.remove();
    }


    /*═══════════════════════════════════════════════════════════════════════
      3. GESTIÓN DE USUARIOS (CRUD)
    ═══════════════════════════════════════════════════════════════════════*/

    function cargarUsuarios() {
        if (!tablaUsuarios) return;

        tablaUsuarios.innerHTML = '<tr><td colspan="7" style="text-align:center;">Cargando usuarios...</td></tr>';

        fetch("php/usuarios_listar.php")
            .then(respuesta => respuesta.json())
            .then(data => {
                if (!data.exito) {
                    tablaUsuarios.innerHTML = `<tr><td colspan="7" style="text-align:center;">${data.mensaje || "No se pudo cargar la lista."}</td></tr>`;
                    return;
                }

                if (!data.usuarios || data.usuarios.length === 0) {
                    tablaUsuarios.innerHTML = '<tr><td colspan="7" style="text-align:center;">No hay usuarios registrados.</td></tr>';
                    return;
                }

                tablaUsuarios.innerHTML = "";

                data.usuarios.forEach(u => {
                    const fila = document.createElement("tr");

                    fila.innerHTML = `
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button class="btn-editar" title="Editar"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-eliminar" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    `;

                    const celdas = fila.querySelectorAll("td");
                    celdas[0].textContent = u.id || "";
                    celdas[1].textContent = u.nombre || "";
                    celdas[2].textContent = u.email || "";
                    celdas[3].textContent = u.rol || "";
                    celdas[4].textContent = u.telefono || "—";
                    celdas[5].textContent = u.direccion || "—";

                    fila.querySelector(".btn-editar").addEventListener("click", () => cargarUsuarioEnFormulario(u));
                    fila.querySelector(".btn-eliminar").addEventListener("click", () => eliminarUsuario(u.id, u.nombre));

                    tablaUsuarios.appendChild(fila);
                });
            })
            .catch(error => {
                console.error("Error al listar usuarios:", error);
                tablaUsuarios.innerHTML = '<tr><td colspan="7" style="text-align:center;">Error de conexión al cargar la tabla.</td></tr>';
            });
    }

    window.cargarUsuariosGlobal = cargarUsuarios;

    function cargarUsuarioEnFormulario(u) {
        if (inputUsuarioIdEditar) inputUsuarioIdEditar.value = u.id || "";
        if (inputUsuarioId) {
            inputUsuarioId.value = u.id || "";
            inputUsuarioId.readOnly = true;
        }
        if (inputUsuarioNombre) inputUsuarioNombre.value = u.nombre || "";
        if (inputUsuarioCorreo) inputUsuarioCorreo.value = u.email || "";
        if (inputUsuarioRol) inputUsuarioRol.value = u.rol || "Administrador";
        if (inputUsuarioTelefono) inputUsuarioTelefono.value = u.telefono || "";
        if (inputUsuarioDireccion) inputUsuarioDireccion.value = u.direccion || "";
        if (inputUsuarioPassword) {
            inputUsuarioPassword.value = "";
            inputUsuarioPassword.placeholder = "Dejar en blanco para no cambiarla";
        }

        if (btnGuardarUsuario) btnGuardarUsuario.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar cambios';
        if (btnCancelarEdicionUsuario) btnCancelarEdicionUsuario.style.display = "inline-flex";

        limpiarMensajeUsuarios();
        if (formularioUsuario) formularioUsuario.scrollIntoView({ behavior: "smooth", block: "start" });
    }

    function limpiarFormularioUsuario() {
        if (inputUsuarioIdEditar) inputUsuarioIdEditar.value = "";
        if (inputUsuarioId) {
            inputUsuarioId.value = "";
            inputUsuarioId.readOnly = false;
        }
        if (inputUsuarioNombre) inputUsuarioNombre.value = "";
        if (inputUsuarioCorreo) inputUsuarioCorreo.value = "";
        if (inputUsuarioRol) inputUsuarioRol.value = "Administrador";
        if (inputUsuarioTelefono) inputUsuarioTelefono.value = "";
        if (inputUsuarioDireccion) inputUsuarioDireccion.value = "";
        if (inputUsuarioPassword) {
            inputUsuarioPassword.value = "";
            inputUsuarioPassword.placeholder = "Mínimo 4 caracteres";
        }

        if (btnGuardarUsuario) btnGuardarUsuario.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar usuario';
        if (btnCancelarEdicionUsuario) btnCancelarEdicionUsuario.style.display = "none";

        limpiarMensajeUsuarios();
    }

    if (btnCancelarEdicionUsuario) {
        btnCancelarEdicionUsuario.addEventListener("click", limpiarFormularioUsuario);
    }

    if (btnGuardarUsuario) {
        btnGuardarUsuario.addEventListener("click", () => {
            const idEditar = inputUsuarioIdEditar ? inputUsuarioIdEditar.value.trim() : "";
            const id = inputUsuarioId ? inputUsuarioId.value.trim() : "";
            const nombre = inputUsuarioNombre ? inputUsuarioNombre.value.trim() : "";
            const correo = inputUsuarioCorreo ? inputUsuarioCorreo.value.trim() : "";
            const rol = inputUsuarioRol ? inputUsuarioRol.value : "Administrador";
            const telefono = inputUsuarioTelefono ? inputUsuarioTelefono.value.trim() : "";
            const direccion = inputUsuarioDireccion ? inputUsuarioDireccion.value.trim() : "";
            const password = inputUsuarioPassword ? inputUsuarioPassword.value : "";

            if (!id && !idEditar) {
                mostrarMensajeUsuarios("Escribe el ID / documento del usuario.");
                return;
            }
            if (!nombre || !correo) {
                mostrarMensajeUsuarios("Por favor completa nombre y correo.");
                return;
            }

            if (!idEditar && !password) {
                mostrarMensajeUsuarios("Escribe una contraseña para el usuario nuevo.");
                return;
            }

            const datos = new URLSearchParams();
            datos.append("id", idEditar || id);
            datos.append("nombre", nombre);
            datos.append("correo", correo);
            datos.append("rol", rol);
            datos.append("telefono", telefono);
            datos.append("direccion", direccion);
            datos.append("password", password);

            const url = idEditar ? "php/usuarios_editar.php" : "php/usuarios_crear.php";
            if (idEditar) datos.append("id", idEditar);

            fetch(url, { method: "POST", body: datos })
                .then(respuesta => respuesta.json())
                .then(data => {
                    if (!data.exito) {
                        mostrarMensajeUsuarios(data.mensaje || "No se pudo guardar el usuario.");
                        return;
                    }

                    mostrarMensajeUsuarios(data.mensaje || "¡Guardado con éxito!", "#4ee28b");
                    limpiarFormularioUsuario();
                    cargarUsuarios();
                })
                .catch(error => {
                    console.error("Error al guardar usuario:", error);
                    mostrarMensajeUsuarios("Error de conexión con el servidor.");
                });
        });
    }

    function eliminarUsuario(id, nombre) {
        const confirmado = confirm(`¿Estás seguro de eliminar al usuario "${nombre}"?`);
        if (!confirmado) return;

        const datos = new URLSearchParams();
        datos.append("id", id);

        fetch("php/usuarios_eliminar.php", { method: "POST", body: datos })
            .then(respuesta => respuesta.json())
            .then(data => {
                if (!data.exito) {
                    mostrarMensajeUsuarios(data.mensaje || "No se pudo eliminar el usuario.");
                    return;
                }
                cargarUsuarios();
            })
            .catch(error => {
                console.error("Error al eliminar usuario:", error);
                mostrarMensajeUsuarios("Error de conexión al eliminar.");
            });
    }


    /*═══════════════════════════════════════════════════════════════════════
      4. REPORTES (datos consultados desde php/reportes_resumen.php)
    ═══════════════════════════════════════════════════════════════════════*/
    const formatoMoneda = new Intl.NumberFormat("es-CO", { style: "currency", currency: "COP", maximumFractionDigits: 0 });

    function asignarTextoReporte(id, valor) {
        const elemento = document.getElementById(id);
        if (elemento) elemento.textContent = valor;
    }

    function obtenerPeriodosRecientes() {
        const fecha = new Date();
        fecha.setDate(1);
        const periodos = [];
        for (let i = 5; i >= 0; i--) {
            const mes = new Date(fecha.getFullYear(), fecha.getMonth() - i, 1);
            periodos.push(`${mes.getFullYear()}-${String(mes.getMonth() + 1).padStart(2, "0")}`);
        }
        return periodos;
    }

    function dibujarGraficaActividad(datos) {
        const contenedor = document.getElementById("graficaActividadMensual");
        if (!contenedor) return;
        const porPeriodo = Object.fromEntries(datos.map(item => [item.periodo, item.reservas]));
        const meses = obtenerPeriodosRecientes();
        const valores = meses.map(periodo => Number(porPeriodo[periodo] || 0));
        const maximo = Math.max(...valores, 1);
        contenedor.innerHTML = "";
        valores.forEach((valor, indice) => {
            const [anio, mes] = meses[indice].split("-");
            const etiqueta = new Intl.DateTimeFormat("es-CO", { month: "short" }).format(new Date(Number(anio), Number(mes) - 1, 1)).replace(".", "");
            const columna = document.createElement("div");
            columna.className = "barra-mensual";
            columna.innerHTML = `<strong>${valor}</strong><div class="barra-mensual-relleno" style="height: ${Math.max((valor / maximo) * 100, valor ? 8 : 2)}%"></div><span>${etiqueta}</span>`;
            contenedor.appendChild(columna);
        });
    }

    function cargarReportes() {
        fetch("php/reportes_resumen.php")
            .then(respuesta => respuesta.json())
            .then(data => {
                if (!data.exito) throw new Error(data.mensaje || "No se pudieron cargar los reportes.");
                const r = data.resumen;
                asignarTextoReporte("reporteTotalVehiculos", r.totalVehiculos);
                asignarTextoReporte("reporteTotalReservas", r.reservasMes);
                asignarTextoReporte("reporteTotalClientes", r.totalClientes);
                asignarTextoReporte("reporteTotalIngresos", formatoMoneda.format(r.ingresosMes));
                asignarTextoReporte("repVehiculosDisponibles", r.vehiculosDisponibles);
                asignarTextoReporte("repVehiculosAlquilados", r.vehiculosAlquilados);
                asignarTextoReporte("repVehiculosMantenimiento", r.vehiculosMantenimiento);
                asignarTextoReporte("repVehiculosEnReserva", r.vehiculosEnReserva);
                asignarTextoReporte("repReservasPendientes", r.reservasPendientes);
                asignarTextoReporte("repPagosPendientes", r.pagosPendientes);
                dibujarGraficaActividad(data.actividadMensual || []);
            })
            .catch(error => {
                console.error("Error al cargar reportes:", error);
                const grafica = document.getElementById("graficaActividadMensual");
                if (grafica) grafica.textContent = "No fue posible cargar la actividad mensual.";
            });
    }
    window.cargarReportesGlobal = cargarReportes;

    /*═══════════════════════════════════════════════════════════════════════
      5. LOGIN (CONEXIÓN CON php/login.php)
    ═══════════════════════════════════════════════════════════════════════*/

    if (loginForm) {
        loginForm.addEventListener("submit", (e) => {
            e.preventDefault();

            const usuario = inputUsuario.value.trim();
            const password = inputPassword.value;

            limpiarErrorLogin();

            const datos = new URLSearchParams();
            datos.append("usuario", usuario);
            datos.append("password", password);

            fetch("php/login.php", { method: "POST", body: datos })
                .then(respuesta => respuesta.json())
                .then(data => {
                    if (!data.exito) {
                        mostrarErrorLogin(data.mensaje || "Credenciales incorrectas.");
                        inputPassword.value = "";
                        return;
                    }

                    // Ocultar login y mostrar dashboard administrativo
                    if (login) login.style.display = "none";
                    if (dashboard) dashboard.style.display = "flex";

                    // ==========================================
                    // ¡LLAMA A LA SINCRONIZACIÓN AQUÍ MISMO!
                    // ==========================================
                    if (typeof window.sincronizarDatosSesion === "function") {
                        window.sincronizarDatosSesion();
                    }

                    // Ir a la sección por defecto (Inicio)
                    window.cambiarSeccion("inicio");
                    if (tituloPagina) tituloPagina.textContent = "Inicio";

                    loginForm.reset();
                })
                .catch(error => {
                    console.error("Error en el login:", error);
                    mostrarErrorLogin("Error de conexión con el servidor.");
                });
        });
    }


    /*═══════════════════════════════════════════════════════════════════════
      5. CERRAR SESIÓN
    ═══════════════════════════════════════════════════════════════════════*/

    function cerrarSesion() {
        mostrarSoloLogin();
        limpiarErrorLogin();
        if (loginForm) loginForm.reset();
        if (inputUsuario) inputUsuario.focus();

        fetch("php/logout.php", { method: "POST" }).catch(error => {
            console.error("Error al cerrar sesión en servidor:", error);
        });
    }

    if (btnCerrarAdmin) {
        btnCerrarAdmin.addEventListener("click", cerrarSesion);
    }


    /*═══════════════════════════════════════════════════════════════════════
      6. INICIALIZACIÓN DE EVENTOS DEL MENÚ LATERAL
    ═══════════════════════════════════════════════════════════════════════*/

    const botonesMenu = document.querySelectorAll(".sidebar-menu button");
    botonesMenu.forEach(boton => {
        boton.addEventListener("click", () => {
            const destino = boton.getAttribute("data-contenedor");
            if (destino) {
                window.cambiarSeccion(destino);
            }
        });
    });

    // Iniciar mostrando solo el login
    mostrarSoloLogin();


    /*═══════════════════════════════════════════════════════════════════════
      7. REGISTRO DE VEHÍCULO (AJAX SIN RECARGAR)
    ═══════════════════════════════════════════════════════════════════════*/

    const formVehiculo = document.getElementById("formVehiculo");
    if (formVehiculo) {
        formVehiculo.addEventListener("submit", async function(e) {
            e.preventDefault(); // Evita que la página se recargue y te redirija

            const formData = new FormData(this);
            const idEditar = document.getElementById("vehiculoIdEditar")?.value.trim();
            const url = idEditar ? "php/vehiculos_editar.php" : "php/vehiculos_crear.php";

            try {
                const respuesta = await fetch(url, {
                    method: "POST",
                    body: formData
                });
                const resultado = await respuesta.json();

                if (resultado.exito) {
                    if (typeof cerrarFormularioVehiculo === "function") {
                        cerrarFormularioVehiculo(); // Limpia y cierra el modal automáticamente
                    }
                    refrescarTablaVehiculos();
                } else {
                    alert(resultado.mensaje); // Muestra el mensaje solo si hubo un error específico devuelto por PHP
                }
            } catch (error) {
                console.error("Error al procesar la solicitud:", error);
                alert("Error de conexión al guardar el vehículo.");
            }
        });
    }

    /*═══════════════════════════════════════════════════════════════════════
      8. BUSCADOR DE VEHÍCULOS
    ═══════════════════════════════════════════════════════════════════════*/

    function filtrarTablaVehiculos() {
        const termino = (document.getElementById("buscarVehiculo")?.value || "").toLowerCase();
        document.querySelectorAll("#tablaVehiculos tr").forEach(fila => {
            fila.style.display = fila.textContent.toLowerCase().includes(termino) ? "" : "none";
        });
    }

    document.getElementById("buscarVehiculo")?.addEventListener("input", filtrarTablaVehiculos);
    document.getElementById("btnBuscarVehiculo")?.addEventListener("click", filtrarTablaVehiculos);

});

// Abre el modal de cliente. Sin argumento = modo "registrar nuevo".
// Con un cliente (fila de la tabla) = modo "editar", con los inputs
// pre-llenados con los datos que ya existen en la base de datos.
function abrirFormularioCliente(cliente) {
    const modal = document.getElementById('modalClienteOverlay');
    const formulario = document.getElementById('formCliente');
    const titulo = document.getElementById('tituloModalCliente');
    const btnGuardar = document.getElementById('btnGuardarCliente');
    const inputDocumento = document.getElementById('documentoCliente');
    const inputIdOriginal = document.getElementById('clienteIdOriginal');

    if (formulario) formulario.reset();
    if (inputIdOriginal) inputIdOriginal.value = "";
    if (inputDocumento) inputDocumento.readOnly = false;

    if (cliente) {
        if (inputDocumento) { inputDocumento.value = cliente.id; inputDocumento.readOnly = true; }
        if (inputIdOriginal) inputIdOriginal.value = cliente.id;
        const campos = {
            nombreCliente: cliente.nombre,
            correoCliente: cliente.email,
            telefonoCliente: cliente.telefono,
            direccionCliente: cliente.direccion
        };
        Object.keys(campos).forEach(id => {
            const campo = document.getElementById(id);
            if (campo) campo.value = campos[id] ?? "";
        });
        if (titulo) titulo.textContent = "Editar Cliente";
        if (btnGuardar) btnGuardar.innerHTML = '<i class="fa-solid fa-check"></i> Guardar cambios';
    } else {
        if (titulo) titulo.textContent = "Registrar Nuevo Cliente";
        if (btnGuardar) btnGuardar.innerHTML = '<i class="fa-solid fa-check"></i> Guardar Cliente';
    }

    if (modal) modal.style.display = 'flex';
}

// Cierra el modal de cliente y lo deja listo para un próximo registro.
function cerrarFormularioCliente() {
    const modal = document.getElementById('modalClienteOverlay');
    if (modal) modal.style.display = 'none';

    const formulario = document.getElementById('formCliente');
    if (formulario) formulario.reset();
    const inputIdOriginal = document.getElementById('clienteIdOriginal');
    if (inputIdOriginal) inputIdOriginal.value = "";
    const inputDocumento = document.getElementById('documentoCliente');
    if (inputDocumento) inputDocumento.readOnly = false;
    const titulo = document.getElementById('tituloModalCliente');
    if (titulo) titulo.textContent = "Registrar Nuevo Cliente";
    const btnGuardar = document.getElementById('btnGuardarCliente');
    if (btnGuardar) btnGuardar.innerHTML = '<i class="fa-solid fa-check"></i> Guardar Cliente';
}

// Se conserva por compatibilidad con cualquier llamada existente (p. ej. si
// algún botón viejo aún usa "toggleFormularioCliente" en vez del nuevo
// "abrirFormularioCliente"/"cerrarFormularioCliente").
function toggleFormularioCliente() {
    const modal = document.getElementById('modalClienteOverlay');
    if (!modal) return;
    if (modal.style.display === 'flex') {
        cerrarFormularioCliente();
    } else {
        abrirFormularioCliente();
    }
}

// Abre el modal de vehículo. Sin argumento = modo "registrar nuevo".
// Con un vehículo (fila de la tabla) = modo "editar".
function abrirFormularioVehiculo(vehiculo) {
    const modal = document.getElementById('modalVehiculoOverlay');
    const idInput = document.getElementById('vehiculoIdEditar');
    const titulo = document.getElementById('tituloModalVehiculo');
    const btnGuardar = document.getElementById('btnRegistrarVehiculo');
    const formulario = document.getElementById('formVehiculo');

    if (formulario) formulario.reset();

    if (vehiculo) {
        if (idInput) idInput.value = vehiculo.id;
        const campos = {
            placa: vehiculo.placa,
            marca: vehiculo.marca,
            modelo: vehiculo.modelo,
            anio: vehiculo.anio,
            color: vehiculo.color,
            tipo: vehiculo.tipo,
            precio: vehiculo.precio_dia
        };
        Object.keys(campos).forEach(id => {
            const campo = document.getElementById(id);
            if (campo) campo.value = campos[id] ?? "";
        });
        if (titulo) titulo.textContent = "Editar Vehículo";
        if (btnGuardar) btnGuardar.innerHTML = '<i class="fa-solid fa-check"></i> Guardar cambios';
    } else {
        if (idInput) idInput.value = "";
        if (titulo) titulo.textContent = "Registrar Nuevo Vehículo";
        if (btnGuardar) btnGuardar.innerHTML = '<i class="fa-solid fa-check"></i> Registrar vehículo';
    }

    if (modal) modal.style.display = 'flex';
}

// Cierra el modal de vehículo y lo deja listo para un próximo registro.
function cerrarFormularioVehiculo() {
    const modal = document.getElementById('modalVehiculoOverlay');
    if (modal) modal.style.display = 'none';

    const formulario = document.getElementById('formVehiculo');
    if (formulario) formulario.reset();
    const idInput = document.getElementById('vehiculoIdEditar');
    if (idInput) idInput.value = "";
    const titulo = document.getElementById('tituloModalVehiculo');
    if (titulo) titulo.textContent = "Registrar Nuevo Vehículo";
    const btnGuardar = document.getElementById('btnRegistrarVehiculo');
    if (btnGuardar) btnGuardar.innerHTML = '<i class="fa-solid fa-check"></i> Registrar vehículo';
}

// Se conserva por compatibilidad con cualquier llamada existente.
function toggleFormularioVehiculo() {
    const modal = document.getElementById('modalVehiculoOverlay');
    if (!modal) return;

    if (modal.style.display === 'flex') {
        cerrarFormularioVehiculo();
    } else {
        abrirFormularioVehiculo();
    }
}

function toggleFormularioReserva() {
    const modal = document.getElementById('modalReservaOverlay');

    if (modal.style.display === 'flex') {
        modal.style.display = 'none';
    } else {
        modal.style.display = 'flex';
    }
}

// Llena los selects de Cliente y Vehículo con datos reales de la base de
// datos (antes tenían opciones fijas de ejemplo que no existían en la BD,
// por eso al crear la reserva no pasaba nada / daba error).
let vehiculosReservaCache = [];

function actualizarCalculoReserva() {
    const selectVehiculo = document.getElementById("selectVehiculoReserva");
    const fechaEntrega = document.getElementById("fechaEntrega");
    const fechaDevolucion = document.getElementById("fechaDevolucion");
    const precioDia = document.getElementById("precioDiaReserva");
    const dias = document.getElementById("diasReserva");
    const valor = document.getElementById("valorReserva");

    const vehiculo = vehiculosReservaCache.find(v => String(v.id) === String(selectVehiculo?.value));
    const precio = vehiculo ? Number(vehiculo.precio_dia || 0) : 0;

    if (precioDia) {
        precioDia.value = precio ? monedaZynco(precio) : "$0";
    }

    let cantidadDias = 0;
    if (fechaEntrega?.value && fechaDevolucion?.value) {
        const inicio = new Date(`${fechaEntrega.value}T00:00:00`);
        const fin = new Date(`${fechaDevolucion.value}T00:00:00`);
        cantidadDias = Math.ceil((fin - inicio) / 86400000);
        if (!Number.isFinite(cantidadDias) || cantidadDias < 0) cantidadDias = 0;
    }

    if (dias) dias.value = cantidadDias;
    if (valor) valor.value = cantidadDias > 0 && precio > 0 ? (cantidadDias * precio).toFixed(2) : "";
}

function poblarSelectsReserva() {
    const selectCliente = document.getElementById("selectClienteReserva");
    const selectVehiculo = document.getElementById("selectVehiculoReserva");

    if (selectCliente) {
        selectCliente.innerHTML = '<option value="">Cargando clientes...</option>';
        fetch("php/clientes_listar.php")
            .then(respuesta => respuesta.json())
            .then(data => {
                selectCliente.innerHTML = '<option value="">Seleccione un cliente...</option>';
                (data.clientes || []).filter(c => String(c.estado || "Activo").toLowerCase() === "activo").forEach(c => {
                    const opcion = document.createElement("option");
                    opcion.value = c.id;
                    opcion.textContent = `${c.nombre} (${c.id})`;
                    selectCliente.appendChild(opcion);
                });
            })
            .catch(() => {
                selectCliente.innerHTML = '<option value="">No se pudieron cargar los clientes</option>';
            });
    }

    if (selectVehiculo) {
        selectVehiculo.innerHTML = '<option value="">Cargando vehículos...</option>';
        fetch("php/vehiculos_listar.php")
            .then(respuesta => respuesta.json())
            .then(data => {
                vehiculosReservaCache = (data.vehiculos || []);
                selectVehiculo.innerHTML = '<option value="">Seleccione un vehículo...</option>';
                vehiculosReservaCache
                    .filter(v => String(v.estado || "").toLowerCase() === "disponible")
                    .forEach(v => {
                        const opcion = document.createElement("option");
                        opcion.value = v.id;
                        opcion.textContent = `${v.marca} ${v.modelo} (${v.placa || "sin placa"}) — ${monedaZynco(v.precio_dia)}/día`;
                        selectVehiculo.appendChild(opcion);
                    });
                actualizarCalculoReserva();
            })
            .catch(() => {
                selectVehiculo.innerHTML = '<option value="">No se pudieron cargar los vehículos</option>';
            });
    }
}
// Abre el modal de "Nueva Reserva" y carga los clientes/vehículos reales.
function abrirFormularioReserva() {
    const formulario = document.getElementById("formReserva");
    if (formulario) formulario.reset();
    const fechaEntrega = document.getElementById("fechaEntrega");
    const fechaDevolucion = document.getElementById("fechaDevolucion");
    if (fechaEntrega) fechaEntrega.min = new Date().toISOString().slice(0, 10);
    if (fechaDevolucion) fechaDevolucion.min = new Date().toISOString().slice(0, 10);
    actualizarCalculoReserva();
    poblarSelectsReserva();
    const modal = document.getElementById("modalReservaOverlay");
    if (modal) modal.style.display = "flex";
}

let reservaPendienteConfirmacion = null;

function obtenerImagenVehiculoReserva(vehiculo) {
    const defecto = "img/veiculos/defect.png";
    const imagen = String(vehiculo?.imagen || "").trim();
    if (!imagen) return defecto;
    if (/^https?:\/\//i.test(imagen) || imagen.startsWith("data:")) return imagen;
    return imagen.replace(/^\.?[\\/]+/, "");
}

function escaparAtributoHtml(valor) {
    return String(valor ?? "").replace(/[&<>"']/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;", "'":"&#39;"}[c]));
}

function mostrarConfirmacionReserva(datos) {
    const modal = document.getElementById("modalConfirmarReservaOverlay");
    const contenido = document.getElementById("contenidoConfirmacionReserva");
    if (!modal || !contenido) {
        alert("No se pudo abrir la confirmación de la reserva. Verifica que el modal exista en index.php.");
        return false;
    }

    const v = datos.vehiculo;
    const imagen = obtenerImagenVehiculoReserva(v);
    const fallback = "img/veiculos/defect.png";
    contenido.innerHTML = `
        <div>
            <img src="${escaparAtributoHtml(imagen)}" alt="${escaparAtributoHtml(`${v.marca || ""} ${v.modelo || ""}`)}"
                 onerror="this.onerror=null;this.src='${fallback}'"
                 style="width:100%;height:230px;object-fit:cover;border-radius:12px;border:1px solid rgba(128,128,128,.25);display:block;">
            <div style="margin-top:10px;text-align:center;font-weight:700;font-size:18px;">${textoSeguro(v.marca)} ${textoSeguro(v.modelo)}</div>
        </div>
        <div style="display:grid;gap:10px;">
            <div><strong>Cliente:</strong> ${textoSeguro(datos.clienteTexto)}</div>
            <div><strong>Vehículo:</strong> ${textoSeguro(v.marca)} ${textoSeguro(v.modelo)}</div>
            <div><strong>Placa:</strong> ${textoSeguro(v.placa || "Sin placa")}</div>
            <div><strong>Tipo:</strong> ${textoSeguro(v.tipo || "No especificado")}</div>
            <div><strong>Color:</strong> ${textoSeguro(v.color || "No especificado")}</div>
            <div><strong>Entrega:</strong> ${textoSeguro(datos.fechaInicio)}</div>
            <div><strong>Devolución:</strong> ${textoSeguro(datos.fechaFin)}</div>
            <div><strong>Días de alquiler:</strong> ${datos.dias}</div>
            <div><strong>Precio por día:</strong> ${monedaZynco(datos.precio)}</div>
            <div style="font-size:22px;font-weight:800;margin-top:8px;"><strong>Total:</strong> ${monedaZynco(datos.total)}</div>
            <div><strong>Estado:</strong> <span class="estado mantenimiento">Pendiente</span></div>
        </div>`;
    modal.style.display = "flex";
    modal.setAttribute("aria-hidden", "false");
    return true;
}

function cerrarConfirmacionReserva() {
    const modal = document.getElementById("modalConfirmarReservaOverlay");
    if (modal) {
        modal.style.display = "none";
        modal.setAttribute("aria-hidden", "true");
    }
    reservaPendienteConfirmacion = null;
}

function guardarReservaConfirmada() {
    const reserva = reservaPendienteConfirmacion;
    const formReserva = document.getElementById("formReserva");
    const boton = document.getElementById("btnConfirmarGuardarReserva");
    if (!reserva || !formReserva) return;

    if (boton) {
        boton.disabled = true;
        boton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';
    }

    fetch("php/reservas_crear.php", { method: "POST", body: new FormData(formReserva) })
        .then(async respuesta => {
            const texto = await respuesta.text();
            let data;
            try { data = JSON.parse(texto); }
            catch (_) { throw new Error(texto || `Respuesta HTTP ${respuesta.status}`); }
            if (!respuesta.ok || !data.exito) throw new Error(data.mensaje || "No se pudo crear la reserva.");
            return data;
        })
        .then(data => {
            cerrarConfirmacionReserva();
            formReserva.reset();
            toggleFormularioReserva();
            if (typeof window.cargarReservasGlobal === "function") window.cargarReservasGlobal();
            if (typeof window.cargarInicioGlobal === "function") window.cargarInicioGlobal();
            if (typeof window.cargarReportesGlobal === "function") window.cargarReportesGlobal();
            alert(data.mensaje || "Reserva guardada correctamente.");
        })
        .catch(error => {
            console.error("Error al crear la reserva:", error);
            alert(error.message || "Error de conexión al crear la reserva.");
        })
        .finally(() => {
            if (boton) {
                boton.disabled = false;
                boton.innerHTML = '<i class="fa-solid fa-check"></i> Confirmar y guardar reserva';
            }
        });
}

function inicializarEventosReserva() {
    const selectVehiculoReserva = document.getElementById("selectVehiculoReserva");
    const fechaEntrega = document.getElementById("fechaEntrega");
    const fechaDevolucion = document.getElementById("fechaDevolucion");
    const formReserva = document.getElementById("formReserva");
    const botonConfirmar = document.getElementById("btnConfirmarGuardarReserva");

    selectVehiculoReserva?.addEventListener("change", actualizarCalculoReserva);
    fechaEntrega?.addEventListener("change", () => {
        if (fechaDevolucion) fechaDevolucion.min = fechaEntrega.value || new Date().toISOString().slice(0, 10);
        actualizarCalculoReserva();
    });
    fechaDevolucion?.addEventListener("change", actualizarCalculoReserva);
    botonConfirmar?.addEventListener("click", guardarReservaConfirmada);

    if (formReserva) {
        formReserva.addEventListener("submit", function(evento) {
            evento.preventDefault();
            actualizarCalculoReserva();

            const selectCliente = document.getElementById("selectClienteReserva");
            const selectVehiculo = document.getElementById("selectVehiculoReserva");
            const fechaInicio = document.getElementById("fechaEntrega")?.value || "";
            const fechaFin = document.getElementById("fechaDevolucion")?.value || "";
            const vehiculo = vehiculosReservaCache.find(v => String(v.id) === String(selectVehiculo?.value));
            const precio = vehiculo ? Number(vehiculo.precio_dia || 0) : 0;
            const dias = Number(document.getElementById("diasReserva")?.value || 0);
            const total = Number(document.getElementById("valorReserva")?.value || 0);

            if (!selectCliente?.value || !vehiculo || !fechaInicio || !fechaFin) {
                alert("Completa cliente, vehículo y fechas antes de continuar.");
                return;
            }
            if (dias <= 0 || precio <= 0 || total <= 0) {
                alert("Selecciona un vehículo y un rango de fechas válido para calcular el total.");
                return;
            }
            if (new Date(`${fechaFin}T00:00:00`) <= new Date(`${fechaInicio}T00:00:00`)) {
                alert("La fecha de devolución debe ser posterior a la fecha de entrega.");
                return;
            }

            reservaPendienteConfirmacion = {
                clienteTexto: selectCliente.options[selectCliente.selectedIndex]?.textContent || selectCliente.value,
                vehiculo,
                fechaInicio,
                fechaFin,
                dias,
                precio,
                total
            };
            mostrarConfirmacionReserva(reservaPendienteConfirmacion);
        });
    }
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", inicializarEventosReserva, { once: true });
} else {
    inicializarEventosReserva();
}

// Función independiente para actualizar el listado cuando se actualice la base de datos
function refrescarTablaVehiculos() {
    if (typeof cargarVehiculos === "function") {
        cargarVehiculos();
    }
}

/*═══════════════════════════════════════════════════════════════════════
      GESTIÓN Y LISTADO DE VEHÍCULOS
═══════════════════════════════════════════════════════════════════════*/

function cargarVehiculos() {
    const tablaVehiculos = document.getElementById("tablaVehiculos");
    if (!tablaVehiculos) return;

    tablaVehiculos.innerHTML = '<tr><td colspan="8" style="text-align:center;">Cargando vehículos...</td></tr>';

    fetch("php/vehiculos_listar.php")
        .then(respuesta => respuesta.json())
        .then(data => {
            if (!data.exito) {
                tablaVehiculos.innerHTML = `<tr><td colspan="8" style="text-align:center;">${data.mensaje || "No se pudo cargar la lista."}</td></tr>`;
                return;
            }

            if (!data.vehiculos || data.vehiculos.length === 0) {
                tablaVehiculos.innerHTML = '<tr><td colspan="8" style="text-align:center;">No hay vehículos registrados.</td></tr>';
                return;
            }

            tablaVehiculos.innerHTML = "";

            data.vehiculos.forEach(v => {
                const fila = document.createElement("tr");

                const precioFormateado = "$" + Number(v.precio_dia).toLocaleString('es-CO');
                const estadoVehiculo = String(v.estado || "Disponible").trim().toLowerCase();
                const claseEstado = estadoVehiculo === "disponible" ? "disponible" : (estadoVehiculo === "reservado" ? "mantenimiento" : "alquilado");

                fila.innerHTML = `
                    <td>${v.placa || "N/A"}</td>
                    <td>${v.marca || ""}</td>
                    <td>${v.modelo || ""}</td>
                    <td>${v.anio || ""}</td>
                    <td>${v.tipo || ""}</td>
                    <td>
                        <span class="estado ${claseEstado}">${v.estado || "Disponible"}</span>
                    </td>
                    <td>${precioFormateado}</td>
                    <td>
                        <button class="btn-editar" title="Editar"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn-eliminar" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                    </td>
                `;

                fila.querySelector(".btn-editar").addEventListener("click", () => abrirFormularioVehiculo(v));
                fila.querySelector(".btn-eliminar").addEventListener("click", () => {
                    eliminarVehiculo(v.id, `${v.marca || ""} ${v.modelo || ""}`.trim() || `#${v.id}`);
                });

                tablaVehiculos.appendChild(fila);
            });
        })
        .catch(error => {
            console.error("Error al listar vehículos:", error);
            tablaVehiculos.innerHTML = '<tr><td colspan="8" style="text-align:center;">Error de conexión al cargar la tabla.</td></tr>';
        });
}

function eliminarVehiculo(id, nombre) {
    if (!confirm(`¿Estás seguro de eliminar el vehículo "${nombre}"?`)) return;

    const datos = new URLSearchParams();
    datos.append("id", id);

    fetch("php/vehiculos_eliminar.php", { method: "POST", body: datos })
        .then(respuesta => respuesta.json())
        .then(data => {
            if (!data.exito) {
                alert(data.mensaje || "No se pudo eliminar el vehículo.");
                return;
            }
            refrescarTablaVehiculos();
        })
        .catch(error => {
            console.error("Error al eliminar vehículo:", error);
            alert("Error de conexión al eliminar el vehículo.");
        });
}

// Cargar al iniciar la página
document.addEventListener("DOMContentLoaded", () => {
    cargarVehiculos();
});

/*═══════════════════════════════════════════════════════════════════════
  INICIO: tarjetas resumen conectadas a php/inicio_resumen.php
═══════════════════════════════════════════════════════════════════════*/

// Rellena las tarjetas de la pestaña Inicio con los datos ya calculados.
function pintarTarjetasInicio(resumen) {
    const elVehiculos = document.getElementById("inicioTotalVehiculos");
    const elReservas = document.getElementById("inicioReservasActivas");
    const elClientes = document.getElementById("inicioTotalClientes");
    const elIngresos = document.getElementById("inicioIngresosMes");

    if (elVehiculos) elVehiculos.textContent = resumen.totalVehiculos;
    if (elReservas) elReservas.textContent = resumen.reservasActivasSemana;
    if (elClientes) elClientes.textContent = resumen.totalClientes;
    if (elIngresos) elIngresos.textContent = monedaZynco(resumen.ingresosMes);
}

// Pide el resumen al backend y lo pinta. Se llama al entrar a "Inicio".
function cargarInicioGlobal() {
    fetch("php/inicio_resumen.php")
        .then(respuesta => respuesta.json())
        .then(data => {
            if (!data.exito) throw new Error(data.mensaje);
            pintarTarjetasInicio(data.resumen);
        })
        .catch(error => {
            console.error("Error al cargar el resumen de inicio:", error);
        });
}
window.cargarInicioGlobal = cargarInicioGlobal;

/*═══════════════════════════════════════════════════════════════════════
  PAGOS Y RESERVAS: datos reales, registro y comprobante no editable
  (Módulo unificado — antes vivía duplicado en pagos.js)
═══════════════════════════════════════════════════════════════════════*/
function textoSeguro(valor) {
    return String(valor ?? "").replace(/[&<>'"]/g, caracter => ({
        "&": "&amp;", "<": "&lt;", ">": "&gt;", "'": "&#39;", '"': "&quot;"
    })[caracter]);
}

function monedaZynco(valor) {
    return new Intl.NumberFormat("es-CO", { style: "currency", currency: "COP", maximumFractionDigits: 0 }).format(Number(valor || 0));
}

// Filtro genérico reutilizable por todos los buscadores (vehículos, reservas,
// clientes, pagos). Se expone en window para poder "reaplicar" el filtro
// después de que una tabla se recargue (por ejemplo, por el auto-refresco
// cada 5s de Reservas/Pagos), que de otra forma borraría lo que el usuario
// había buscado.
function filtrarTablaPorTexto(inputId, tablaId) {
    const termino = (document.getElementById(inputId)?.value || "").toLowerCase();
    document.querySelectorAll(`#${tablaId} tr`).forEach(fila => {
        fila.style.display = fila.textContent.toLowerCase().includes(termino) ? "" : "none";
    });
}
window.filtrarTablaPorTexto = filtrarTablaPorTexto;

function fechaZynco(valor, incluirHora = false) {
    if (!valor) return "No disponible";
    const fecha = new Date(String(valor).replace(" ", "T"));
    return new Intl.DateTimeFormat("es-CO", {
        dateStyle: "medium", ...(incluirHora ? { timeStyle: "medium" } : {})
    }).format(fecha);
}

function claseEstadoZynco(estado) {
    const estadoNormalizado = String(estado || "").trim().toLowerCase();
    if (["disponible", "devuelto", "pagada", "aprobada", "pagado"].includes(estadoNormalizado)) return "disponible";
    if (["pendiente", "reservado"].includes(estadoNormalizado)) return "mantenimiento";
    return "alquilado";
}

let reservasCargadasUnaVez = false;

function cargarReservas() {
    const tabla = document.getElementById("tablaReservas");
    if (!tabla) return;
    if (!reservasCargadasUnaVez) {
        tabla.innerHTML = '<tr><td colspan="8" style="text-align:center;">Cargando reservas...</td></tr>';
    }
    fetch("php/reservas_listar.php")
        .then(respuesta => respuesta.json())
        .then(data => {
            reservasCargadasUnaVez = true;
            if (!data.exito) throw new Error(data.mensaje);
            if (!data.reservas.length) {
                tabla.innerHTML = '<tr><td colspan="8" style="text-align:center;">No hay reservas registradas.</td></tr>';
                return;
            }
            window.reservasDisponiblesPago = data.reservas;
            tabla.innerHTML = "";
            data.reservas.forEach(reserva => {
                const fila = document.createElement("tr");
                fila.innerHTML = `<td>R${reserva.id}</td><td>${textoSeguro(reserva.cliente_nombre)}</td><td>${textoSeguro(reserva.marca)} ${textoSeguro(reserva.modelo)}</td><td>${fechaZynco(reserva.fecha_inicio)}</td><td>${fechaZynco(reserva.fecha_fin)}</td><td>${monedaZynco(reserva.costo_total)}</td><td><span class="estado ${claseEstadoZynco(reserva.estado)}">${textoSeguro(reserva.estado)}</span></td><td></td>`;
                const acciones = fila.lastElementChild;

                const botonVer = document.createElement("button");
                botonVer.className = "btn-ver";
                botonVer.title = "Ver detalle de la reserva";
                botonVer.setAttribute("aria-label", `Ver detalle de la reserva R${reserva.id}`);
                botonVer.innerHTML = '<i class="fa-solid fa-eye"></i>';
                botonVer.addEventListener("click", () => abrirDetalleReserva(reserva));
                acciones.appendChild(botonVer);

                tabla.appendChild(fila);
            });
            filtrarTablaPorTexto("buscarReserva", "tablaReservas");
        })
        .catch(error => {
            reservasCargadasUnaVez = true;
            console.error("Error al listar reservas:", error);
            tabla.innerHTML = '<tr><td colspan="8" style="text-align:center;">No se pudieron cargar las reservas.</td></tr>';
        });
}

function abrirPagoReserva(reserva) {
    document.getElementById("pagoId").value = "";
    document.getElementById("clientePago").value = `${reserva.cliente_nombre} (${reserva.cliente_documento})`;
    document.getElementById("selectReservaPago").value = reserva.id;
    document.getElementById("valorPago").value = reserva.costo_total;
    document.getElementById("metodoPago").value = "";
    document.getElementById("modalPagoOverlay").style.display = "flex";
}

function abrirPagoExistente(pago) {
    document.getElementById("pagoId").value = pago.id;
    document.getElementById("clientePago").value = `${pago.cliente_nombre} (${pago.cliente_documento})`;
    document.getElementById("selectReservaPago").value = pago.reserva_id;
    document.getElementById("valorPago").value = pago.monto;
    document.getElementById("metodoPago").value = pago.metodo_pago;
    document.getElementById("modalPagoOverlay").style.display = "flex";
}

function cerrarFormularioPago() {
    const modal = document.getElementById("modalPagoOverlay");
    if (modal) modal.style.display = "none";
    const formulario = document.getElementById("formPago");
    if (formulario) formulario.reset();
}

let pagosCargadosUnaVez = false;

// La tabla de Pagos ahora combina dos fuentes:
//  1) reservas "Pendiente" sin pago todavía (vienen de reservas_listar.php) —
//     se muestran con opción de "Registrar pago".
//  2) pagos ya registrados (vienen de pagos_listar.php) — el botón de
//     "Registrar pago" solo se muestra si, por algún motivo, ese pago no
//     está en estado "Pagado".
function cargarPagos() {
    const tabla = document.getElementById("tablaPagos");
    if (!tabla) return;
    if (!pagosCargadosUnaVez) {
        tabla.innerHTML = '<tr><td colspan="8" style="text-align:center;">Cargando pagos...</td></tr>';
    }

    Promise.all([
        fetch("php/pagos_listar.php").then(r => r.json()),
        fetch("php/reservas_listar.php").then(r => r.json())
    ])
        .then(([datosPagos, datosReservas]) => {
            pagosCargadosUnaVez = true;
            if (!datosPagos.exito) throw new Error(datosPagos.mensaje);

            if (datosReservas.exito) {
                window.reservasDisponiblesPago = datosReservas.reservas;
            }

            const reservasPendientes = (datosReservas.reservas || []).filter(
                r => !r.tiene_pago && String(r.estado).toLowerCase() === "pendiente"
            );

            if (!datosPagos.pagos.length && !reservasPendientes.length) {
                tabla.innerHTML = '<tr><td colspan="8" style="text-align:center;">No hay pagos registrados.</td></tr>';
                return;
            }

            tabla.innerHTML = "";

            // 1) Reservas pendientes de pago
            reservasPendientes.forEach(reserva => {
                const fila = document.createElement("tr");
                fila.innerHTML = `<td>R${reserva.id}</td><td>${textoSeguro(reserva.cliente_nombre)}</td><td>R${reserva.id}</td><td>—</td><td>—</td><td>${monedaZynco(reserva.costo_total)}</td><td><span class="estado mantenimiento">Pendiente</span></td><td></td>`;
                const acciones = fila.lastElementChild;

                const botonPagar = document.createElement("button");
                botonPagar.className = "btn-pagar";
                botonPagar.title = "Registrar pago";
                botonPagar.setAttribute("aria-label", `Registrar pago de la reserva R${reserva.id}`);
                botonPagar.innerHTML = '<i class="fa-solid fa-file-invoice-dollar"></i>';
                botonPagar.addEventListener("click", () => abrirPagoReserva(reserva));
                acciones.appendChild(botonPagar);

                const botonDetalle = document.createElement("button");
                botonDetalle.className = "btn-ver";
                botonDetalle.title = "Ver detalle de la reserva";
                botonDetalle.setAttribute("aria-label", `Ver detalle de la reserva R${reserva.id}`);
                botonDetalle.innerHTML = '<i class="fa-solid fa-clipboard-list"></i>';
                botonDetalle.addEventListener("click", () => abrirDetalleReserva(reserva));
                acciones.appendChild(botonDetalle);

                tabla.appendChild(fila);
            });

            // 2) Pagos ya registrados
            datosPagos.pagos.forEach(pago => {
                const fila = document.createElement("tr");
                fila.innerHTML = `<td>P${pago.id}</td><td>${textoSeguro(pago.cliente_nombre)}</td><td>R${pago.reserva_id}</td><td>${fechaZynco(pago.fecha_pago, true)}</td><td>${textoSeguro(pago.metodo_pago)}</td><td>${monedaZynco(pago.monto)}</td><td><span class="estado ${claseEstadoZynco(pago.estado_pago)}">${textoSeguro(pago.estado_pago || "Sin estado")}</span></td><td></td>`;
                const acciones = fila.lastElementChild;

                // Solo se muestra si el pago no está ya "Pagado".
                if (String(pago.estado_pago || "").toLowerCase() !== "pagado") {
                    const botonRegistrar = document.createElement("button");
                    botonRegistrar.className = "btn-pagar";
                    botonRegistrar.title = "Registrar pago";
                    botonRegistrar.setAttribute("aria-label", `Registrar pago P${pago.id}`);
                    botonRegistrar.innerHTML = '<i class="fa-solid fa-file-invoice-dollar"></i>';
                    botonRegistrar.addEventListener("click", () => abrirPagoExistente(pago));
                    acciones.appendChild(botonRegistrar);
                }

                const botonComprobante = document.createElement("button");
                botonComprobante.className = "btn-ver";
                botonComprobante.title = "Ver comprobante";
                botonComprobante.setAttribute("aria-label", `Ver comprobante del pago P${pago.id}`);
                botonComprobante.innerHTML = '<i class="fa-solid fa-eye"></i>';
                botonComprobante.addEventListener("click", () => verComprobante(pago.id));
                acciones.appendChild(botonComprobante);

                const botonDetalle = document.createElement("button");
                botonDetalle.className = "btn-ver";
                botonDetalle.title = "Ver detalle de la reserva";
                botonDetalle.setAttribute("aria-label", `Ver detalle de la reserva R${pago.reserva_id}`);
                botonDetalle.innerHTML = '<i class="fa-solid fa-clipboard-list"></i>';
                botonDetalle.addEventListener("click", () => abrirDetalleReservaPorId(pago.reserva_id));
                acciones.appendChild(botonDetalle);

                tabla.appendChild(fila);
            });

            filtrarTablaPorTexto("buscarPago", "tablaPagos");
        })
        .catch(error => {
            pagosCargadosUnaVez = true;
            console.error("Error al listar pagos:", error);
            tabla.innerHTML = '<tr><td colspan="8" style="text-align:center;">No se pudieron cargar los pagos.</td></tr>';
        });
}

function verComprobante(pagoId) {
    const modal = document.getElementById("modalComprobanteOverlay");
    const contenido = document.getElementById("contenidoComprobante");
    modal.style.display = "flex";
    contenido.textContent = "Cargando comprobante...";
    fetch(`php/pagos_comprobante.php?id=${encodeURIComponent(pagoId)}`)
        .then(respuesta => respuesta.json())
        .then(data => {
            if (!data.exito) throw new Error(data.mensaje);
            const p = data.pago;
            contenido.innerHTML = `<section><h3>Transacción</h3><p><strong>ID de pago:</strong> P${p.id}</p><p><strong>Fecha y hora:</strong> ${fechaZynco(p.fecha_pago, true)}</p></section><section><h3>Cliente</h3><p><strong>Nombre:</strong> ${textoSeguro(p.cliente_nombre)}</p><p><strong>Documento:</strong> ${textoSeguro(p.cliente_documento)}</p></section><section><h3>Vehículo alquilado</h3><p><strong>Vehículo:</strong> ${textoSeguro(p.marca)} ${textoSeguro(p.modelo)}</p><p><strong>Placa:</strong> ${textoSeguro(p.placa || "No registrada")}</p></section><section><h3>Desglose financiero</h3><p><strong>Monto pagado:</strong> ${monedaZynco(p.monto)}</p><p><strong>Método:</strong> ${textoSeguro(p.metodo_pago)}</p></section><section><h3>Usuario que registró el pago</h3><p><strong>Nombre:</strong> ${textoSeguro(p.usuario_nombre || "No disponible")}</p><p><strong>Cargo / rol:</strong> ${textoSeguro(p.usuario_rol || "No disponible")}</p><p><strong>Correo:</strong> ${textoSeguro(p.usuario_email || "No disponible")}</p></section>`;
        })
        .catch(error => { contenido.textContent = error.message || "No fue posible cargar el comprobante."; });
}

function cerrarComprobante() {
    const modal = document.getElementById("modalComprobanteOverlay");
    if (modal) modal.style.display = "none";
}

function abrirDetalleReserva(reserva) {
    const modal = document.getElementById("modalDetalleReservaOverlay");
    const contenido = document.getElementById("contenidoDetalleReserva");
    if (!modal || !contenido) return;

    const cliente = reserva.cliente || {};
    const vehiculo = reserva.vehiculo || {};
    const usuario = reserva.usuario || {};

    const noDisponible = "No disponible";
    const mostrar = valor => (valor === null || valor === undefined || valor === "") ? noDisponible : textoSeguro(valor);

    contenido.innerHTML = `
        <section>
            <h3>Reserva</h3>
            <p><strong>ID:</strong> R${reserva.id}</p>
            <p><strong>Estado:</strong> <span class="estado ${claseEstadoZynco(reserva.estado)}">${textoSeguro(reserva.estado)}</span></p>
            <div style="display:flex; gap:8px; flex-wrap:wrap; margin:12px 0;">
                ${String(reserva.estado).toLowerCase() === "pendiente" ? `<button class="btn btn-verde" type="button" onclick="actualizarEstadoReserva(${reserva.id}, 'Entregado')"><i class="fa-solid fa-key"></i> Marcar entregado</button>` : ""}
                ${String(reserva.estado).toLowerCase() === "entregado" ? `<button class="btn btn-verde" type="button" onclick="actualizarEstadoReserva(${reserva.id}, 'Devuelto')"><i class="fa-solid fa-rotate-left"></i> Marcar devuelto</button>` : ""}
            </div>
            <p><strong>Generada el:</strong> ${reserva.fecha_creacion ? fechaZynco(reserva.fecha_creacion, true) : noDisponible}</p>
            <p><strong>Recogida del vehículo:</strong> ${reserva.fecha_inicio ? fechaZynco(reserva.fecha_inicio) : noDisponible}</p>
            <p><strong>Devolución del vehículo:</strong> ${reserva.fecha_fin ? fechaZynco(reserva.fecha_fin) : noDisponible}</p>
            <p><strong>Valor total:</strong> ${monedaZynco(reserva.costo_total)}</p>
        </section>
        <section>
            <h3>Cliente</h3>
            <p><strong>Nombre:</strong> ${mostrar(cliente.nombre)}</p>
            <p><strong>Documento:</strong> ${mostrar(cliente.id)}</p>
            <p><strong>Correo:</strong> ${mostrar(cliente.email)}</p>
            <p><strong>Teléfono:</strong> ${mostrar(cliente.telefono)}</p>
            <p><strong>Dirección:</strong> ${mostrar(cliente.direccion)}</p>
        </section>
        <section>
            <h3>Vehículo</h3>
            <p><strong>Vehículo:</strong> ${mostrar(vehiculo.marca)} ${mostrar(vehiculo.modelo) === noDisponible ? "" : mostrar(vehiculo.modelo)}</p>
            <p><strong>Placa:</strong> ${mostrar(vehiculo.placa)}</p>
            <p><strong>Año:</strong> ${mostrar(vehiculo.anio)}</p>
            <p><strong>Color:</strong> ${mostrar(vehiculo.color)}</p>
            <p><strong>Tipo:</strong> ${mostrar(vehiculo.tipo)}</p>
            <p><strong>Precio por día:</strong> ${vehiculo.precio_dia !== null && vehiculo.precio_dia !== undefined ? monedaZynco(vehiculo.precio_dia) : noDisponible}</p>
        </section>
        <section>
            <h3>Registrada por</h3>
            <p><strong>Nombre:</strong> ${mostrar(usuario.nombre)}</p>
            <p><strong>Correo:</strong> ${mostrar(usuario.email)}</p>
            <p><strong>Rol:</strong> ${mostrar(usuario.rol)}</p>
        </section>
    `;

    modal.style.display = "flex";
}

function cerrarDetalleReserva() {
    const modal = document.getElementById("modalDetalleReservaOverlay");
    if (modal) modal.style.display = "none";
}

// Usada desde la pestaña de Pagos: busca la reserva por id (ya cargada en
// memoria por la pestaña de Reservas) y abre el mismo modal de detalle.
function abrirDetalleReservaPorId(reservaId) {
    const cache = window.reservasDisponiblesPago || [];
    const encontrada = cache.find(r => Number(r.id) === Number(reservaId));
    if (encontrada) {
        abrirDetalleReserva(encontrada);
        return;
    }

    fetch("php/reservas_listar.php")
        .then(respuesta => respuesta.json())
        .then(data => {
            if (!data.exito) throw new Error(data.mensaje);
            window.reservasDisponiblesPago = data.reservas;
            const reserva = data.reservas.find(r => Number(r.id) === Number(reservaId));
            if (reserva) {
                abrirDetalleReserva(reserva);
            } else {
                alert("No se encontró la información de esa reserva.");
            }
        })
        .catch(error => {
            console.error("Error al cargar el detalle de la reserva:", error);
            alert("No se pudo cargar el detalle de la reserva.");
        });
}
async function actualizarEstadoReserva(reservaId, nuevoEstado) {
    if (!confirm(`¿Cambiar la reserva R${reservaId} a "${nuevoEstado}"?`)) return;
    try {
        const formData = new FormData();
        formData.append("id", reservaId);
        formData.append("estado", nuevoEstado);
        const respuesta = await fetch("php/reservas_estado.php", { method: "POST", body: formData });
        const data = await respuesta.json();
        if (!data.exito) throw new Error(data.mensaje || "No se pudo actualizar el estado.");
        cerrarDetalleReserva();
        cargarReservas();
        if (typeof refrescarTablaVehiculos === "function") refrescarTablaVehiculos();
        if (typeof window.cargarPagosGlobal === "function") window.cargarPagosGlobal();
        alert(data.mensaje);
    } catch (error) {
        console.error("Error al actualizar el estado de la reserva:", error);
        alert(error.message || "Error de conexión.");
    }
}

window.abrirDetalleReservaPorId = abrirDetalleReservaPorId;
window.actualizarEstadoReserva = actualizarEstadoReserva;

window.cargarReservasGlobal = cargarReservas;
window.cargarPagosGlobal = cargarPagos;
window.abrirPagoReserva = abrirPagoReserva;
window.abrirPagoExistente = abrirPagoExistente;
window.cerrarComprobante = cerrarComprobante;
window.verComprobante = verComprobante;
window.toggleFormularioPago = cerrarFormularioPago;

document.addEventListener("DOMContentLoaded", () => {
    const formularioPago = document.getElementById("formPago");
    if (formularioPago) {
        formularioPago.addEventListener("submit", evento => {
            evento.preventDefault();
            const datos = new FormData(formularioPago);
            fetch("php/pagos_registrar.php", { method: "POST", body: datos })
                .then(respuesta => respuesta.json())
                .then(data => {
                    if (!data.exito) throw new Error(data.mensaje);
                    cerrarFormularioPago();
                    cargarPagos();
                    cargarReservas();
                    if (typeof window.cargarReportesGlobal === "function") window.cargarReportesGlobal();
                })
                .catch(error => alert(error.message || "No se pudo registrar el pago."));
        });
    }

    document.getElementById("buscarPago")?.addEventListener("input", () => filtrarTablaPorTexto("buscarPago", "tablaPagos"));

    // El buscador de Reservas no tenía ningún listener conectado; ahora usa
    // el mismo filtro genérico y se reaplica automáticamente cada vez que
    // cargarReservas() vuelve a dibujar la tabla (ver el auto-refresco abajo).
    document.getElementById("buscarReserva")?.addEventListener("input", () => filtrarTablaPorTexto("buscarReserva", "tablaReservas"));

    // Carga inicial de reservas y pagos
    cargarReservas();
    cargarPagos();

    // Auto-refresco cada 5s mientras el dashboard esté visible y no haya un modal de pago abierto
    setInterval(() => {
        const dashboard = document.getElementById("dashboard");
        const modalAbierto = document.getElementById("modalPagoOverlay")?.style.display === "flex";

        if (dashboard && dashboard.style.display !== "none" && !modalAbierto) {
            cargarReservas();
            cargarPagos();
        }
    }, 5000);
});

/*═══════════════════════════════════════════════════════════════════════
  GESTIÓN Y LISTADO DE CLIENTES (CORRECCIÓN Y CONEXIÓN)
═══════════════════════════════════════════════════════════════════════*/

function cargarClientes() {
    const tablaClientes = document.getElementById("tablaClientes");
    if (!tablaClientes) return;

    tablaClientes.innerHTML = '<tr><td colspan="8" style="text-align:center;">Cargando clientes...</td></tr>';

    fetch("php/clientes_listar.php")
        .then(respuesta => respuesta.json())
        .then(data => {
            if (!data.exito) {
                tablaClientes.innerHTML = `<tr><td colspan="8" style="text-align:center;">${data.mensaje || "No se pudo cargar la lista."}</td></tr>`;
                return;
            }

            if (!data.clientes || data.clientes.length === 0) {
                tablaClientes.innerHTML = '<tr><td colspan="8" style="text-align:center;">No hay clientes registrados.</td></tr>';
                return;
            }

            tablaClientes.innerHTML = "";

            data.clientes.forEach(c => {
                const fila = document.createElement("tr");
                const estadoTexto = c.estado || "";
                const estadoClase = estadoTexto.toLowerCase() === "activo" ? "disponible" : "alquilado";

                fila.innerHTML = `
                    <td>${textoSeguro(c.id)}</td>
                    <td>${textoSeguro(c.nombre)}</td>
                    <td>${textoSeguro(c.email)}</td>
                    <td>${textoSeguro(c.telefono)}</td>
                    <td>${c.fecha_registro ? fechaZynco(c.fecha_registro) : ""}</td>
                    <td>${textoSeguro(c.direccion)}</td>
                    <td>${estadoTexto ? `<span class="estado ${estadoClase}">${textoSeguro(estadoTexto)}</span>` : ""}</td>
                    <td>
                        <button class="btn-editar" title="Editar"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn-eliminar" title="Desactivar/Eliminar"><i class="fa-solid fa-ban"></i></button>
                    </td>
                `;

                // Evento para Editar: abre el modal ya lleno con los datos
                // reales del cliente (documento, nombre, correo, teléfono,
                // dirección). El documento no es editable porque identifica
                // al cliente en la base de datos.
                fila.querySelector(".btn-editar").addEventListener("click", () => {
                    abrirFormularioCliente(c);
                });

                // Evento para Activar / Desactivar (toggle sobre el campo "estado")
                const esActivo = estadoTexto.toLowerCase() === "activo";
                const botonEstado = fila.querySelector(".btn-eliminar");
                botonEstado.title = esActivo ? "Desactivar" : "Activar";
                botonEstado.addEventListener("click", () => {
                    const accion = esActivo ? "desactivar" : "activar";
                    if (!confirm(`¿Estás seguro de ${accion} al cliente ${c.nombre}?`)) return;

                    const datos = new URLSearchParams();
                    datos.append("id", c.id);

                    fetch("php/clientes_desactivar.php", { method: "POST", body: datos })
                        .then(res => res.json())
                        .then(resData => {
                            if (!resData.exito) {
                                alert(resData.mensaje || "No se pudo procesar la solicitud.");
                                return;
                            }
                            cargarClientes();
                        })
                        .catch(err => console.error("Error al cambiar el estado del cliente:", err));
                });

                tablaClientes.appendChild(fila);
            });

            filtrarTablaPorTexto("buscarCliente", "tablaClientes");
        })
        .catch(error => {
            console.error("Error al listar clientes:", error);
            tablaClientes.innerHTML = '<tr><td colspan="8" style="text-align:center;">Error de conexión al cargar la tabla de clientes.</td></tr>';
        });
}

// Vincular el formulario de guardar cliente si existe en el HTML
document.addEventListener("DOMContentLoaded", () => {
    const formCliente = document.getElementById("formCliente");
    if (formCliente) {
        formCliente.addEventListener("submit", e => {
            e.preventDefault();
            const datos = new FormData(formCliente);

            fetch("php/clientes_guardar.php", { method: "POST", body: datos })
                .then(res => res.json())
                .then(data => {
                    if (!data.exito) {
                        alert(data.mensaje || "Error al guardar el cliente.");
                        return;
                    }
                    alert(data.mensaje);
                    cerrarFormularioCliente();
                    cargarClientes();
                })
                .catch(err => console.error("Error al guardar cliente:", err));
        });
    }

    // El buscador de Clientes tampoco tenía listener conectado.
    document.getElementById("buscarCliente")?.addEventListener("input", () => filtrarTablaPorTexto("buscarCliente", "tablaClientes"));

    // Cargar clientes al iniciar si ya está activa la vista
    cargarClientes();
});

// Registrar la función globalmente para que el sistema la llame cuando cambies de sección
window.cargarClientesGlobal = cargarClientes;