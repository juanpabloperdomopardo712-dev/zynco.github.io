<!doctype html>
<html lang="es">
  <!--═══════════════════════════════════════════════════════════════════════
      ZYNCO — index.php
      ───────────────────────────────────────────────────────────────────
      Zynco es una herramienta INTERNA, solo para el personal de la
      empresa (no hay panel de cliente ni registro público: los usuarios
      del sistema se crean desde la sección "Usuarios" del panel, una
      vez adentro, no desde la pantalla de login).

      ESTRUCTURA GENERAL
      ───────────────────────────────────────────────────────────────────
      <body>
        <section id="login">              Pantalla de inicio de sesión
        <div id="panel-admin">            Panel principal (oculto hasta
          <section id="dashboard">        iniciar sesión)
            <aside id="sidebar">            Menú lateral
            <main id="principal">           Título + secciones (data-contenedor):
                                             inicio, vehículos, reservas,
                                             clientes, pagos, reportes,
                                             usuarios (gestión de cuentas)
        <footer id="footer">
        <script src="js/app.js">          Toda la lógica de login,
                                           navegación y cierre de sesión
  ═══════════════════════════════════════════════════════════════════════-->
  <head>
    <!--==========================================================
                        CONFIGURACIÓN GENERAL
    ===========================================================-->

    <meta charset="UTF-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Zynco | Panel Principal</title>

    <meta name="description" content="Sistema de gestión de alquiler de autos y motos Zynco." />

    <!--<link rel="icon" href="img/coche.png" type="image/png" />-->

    <!--==========================================================
                        ICONOS
    ===========================================================-->

    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    />

    <!--==========================================================
                        ESTILOS
    ===========================================================-->

    <link rel="stylesheet" href="css/styles_zynco.css" />
  <link rel="icon" type="image/png" href="img/logo_zinco.png">
  </head>

  <body>
    <!--
    ████████████████████████████████████████████████████████████████████
       CONTENEDOR: LOGIN
       Pantalla de inicio de sesión. Es lo único visible al cargar la
       página. app.js valida usuario/contraseña contra la base de datos
       (php/login.php); si son correctos, entra al panel único de
       administración. No hay registro público: las cuentas se crean
       desde la sección "Usuarios" del panel, ya adentro.
    ████████████████████████████████████████████████████████████████████
    -->

    <section id="login">
      <main class="login-container">
        <!--==============================
                LOGIN (tarjeta 1 de 2)
                Visible por defecto.
            ==============================-->

        <section class="login-card" id="loginCard">
          <!--==============================
                        LOGO
            ==============================-->

          <div class="login-logo">
            <img src="img/logo_zinco.png" alt="Logo Zynco" id="logoLogin" />
          </div>

          <!--==============================
                    FORMULARIO LOGIN
            ==============================-->

          <div class="login-form">
            <h1>Bienvenido</h1>

            <p>Inicia sesión para administrar Zynco.</p>

            <form id="loginForm">
              <div class="input-group">
                <label for="usuario">
                  <i class="fa-solid fa-user"></i>

                  Usuario
                </label>

                <input
                  type="text"
                  id="usuario"
                  value="Administrador"
                  name="usuario"
                  placeholder="Ingrese su usuario"
                  autocomplete="username"
                  required
                />
              </div>

              <div class="input-group">
                <label for="password">
                  <i class="fa-solid fa-lock"></i>

                  Contraseña
                </label>

                <input
                  type="password"
                  id="password"
                  value="123456"
                  name="password"
                  placeholder="Ingrese su contraseña"
                  autocomplete="current-password"
                  required
                />
              </div>

              <button id="btnLogin" class="btn-login" type="submit">
                <i class="fa-solid fa-right-to-bracket"></i>

                Iniciar sesión
              </button>
            </form>
          </div>
        </section>
      </main>
    </section>

    <!--
    ████████████████████████████████████████████████████████████████████
       PANEL: ADMINISTRADOR  (id="panel-admin")
       Todo lo que ve el administrador tras iniciar sesión. Zynco ahora
       es una herramienta SOLO para el personal de la empresa: ya no
       existe un panel de cliente ni un formulario público de
       registro (los usuarios del sistema se crean desde la sección
       "Usuarios" de este mismo panel, más abajo).
    ████████████████████████████████████████████████████████████████████
    -->
    
      <section id="dashboard">
        <!--
        ══════════════════════════════════════════════════════════════
           MENÚ LATERAL — Administrador (aside#sidebar)
           Cada botón tiene "data-contenedor" con el id de la sección
           que debe mostrar; app.js lee ese atributo para cambiar de
           pestaña.
        ══════════════════════════════════════════════════════════════
        -->

        <aside class="sidebar" id="sidebar">
          <!--==========================
                LOGO
        ==========================-->

          <div class="sidebar-logo">
            <img src="img/icon_zynco.png" alt="Logo Zynco" id="logoSidebar" />

            <h2>Zynco</h2>
          </div>

          <!--==========================
                MENÚ
        ==========================-->

          <nav class="sidebar-menu" id="menuPrincipal">
            <button id="btnInicio" class="menu activo" data-contenedor="inicio">
              <i class="fa-solid fa-house"></i>

              <span>Inicio</span>
            </button>

            <button id="btnVehiculos" class="menu" data-contenedor="vehiculos">
              <i class="fa-solid fa-car"></i>

              <span>Vehículos</span>
            </button>

            <button id="btnReservas" class="menu" data-contenedor="reservas">
              <i class="fa-solid fa-calendar-check"></i>

              <span>Reservas</span>
            </button>

            <button id="btnClientes" class="menu" data-contenedor="clientes">
              <i class="fa-solid fa-users"></i>

              <span>Clientes</span>
            </button>

            <button id="btnPagos" class="menu" data-contenedor="pagos">
              <i class="fa-solid fa-credit-card"></i>

              <span>Pagos</span>
            </button>

            <button id="btnReportes" class="menu" data-contenedor="reportes">
              <i class="fa-solid fa-chart-line"></i>

              <span>Reportes</span>
            </button>

            <button id="btnUsuarios" class="menu" data-contenedor="usuarios">
              <i class="fa-solid fa-user-gear"></i>

              <span>Usuarios</span>
            </button>
          </nav>

          <!--==========================
            CERRAR SESIÓN
        ==========================-->

          <button id="cerrarSesion" class="cerrar-sesion">
            <i class="fa-solid fa-right-from-bracket"></i>

            <span> Cerrar sesión </span>
          </button>
        </aside>

        <!--
        ══════════════════════════════════════════════════════════════
           CONTENIDO PRINCIPAL — Administrador (main#principal)
           Cabecera + barra de herramientas + los 7 módulos del panel
           de administrador (más abajo, cada uno con su propio título).
        ══════════════════════════════════════════════════════════════
        -->

        <main class="principal" id="principal">
          <!--======================================================
                        CABECERA
        ======================================================-->

          <header class="header" id="header">
            <div class="header-left">
              <h1 id="tituloPagina">Inicio</h1>
            </div>

            <div class="usuario" id="usuarioActual">
    <i class="fa-solid fa-user"></i>
    <span class="rol-usuario-dinamico" id="rolHeader">ministrador</span>
</div>
          </header>

          <!--======================================================
                    BARRA DE HERRAMIENTAS
        ======================================================-->

          <section class="toolbar" id="toolbar">
            <!--
                Este espacio queda preparado para:

                - Buscador
                - Botón Nuevo
                - Exportar
                - Filtros
                - Acciones rápidas

                No eliminar.
            -->
          </section>

          <!--======================================================
                    CONTENIDO GENERAL
        ======================================================-->

          <section class="contenido" id="contenidoPrincipal">
            <!--
            ══════════════════════════════════════════════════════════
               CONTENEDOR: INICIO  (id="inicio") — Panel Administrador
               Es la pestaña que se ve primero al entrar como admin
               (tiene la clase "activo" desde el HTML). Muestra
               tarjetas resumen (vehículos, reservas, clientes, pagos,
               reportes) y accesos rápidos.
            ══════════════════════════════════════════════════════════
            -->

            <section class="contenedor activo" id="inicio">
              <div class="page-header">
                <!-- Título del módulo -->
                <h2>Panel de Control / Inicio</h2>
                <p style="color: var(--texto2)">
                  Bienvenido al sistema de administración de Zynco. Aquí tienes un resumen general
                  de la plataforma.
                </p>
              </div>

              <div class="page-body">
                <!-- Dashboard -->

                <!-- Tarjetas de Estadísticas / Resumen en miniatura -->
                <div class="estadisticas" style="margin-top: 20px">
                  <!-- Resumen de Vehículos -->
                  <div class="estadistica">
                    <div
                      style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 15px;
                      "
                    >
                      <h3 style="font-size: 16px; color: var(--texto2); margin: 0">Vehículos</h3>
                      <i class="fa-solid fa-car" style="font-size: 20px; color: var(--azul)"></i>
                    </div>
                    <h2 id="inicioTotalVehiculos">0</h2>
                    <p style="font-size: 13px; margin-bottom: 15px">
                      Total registrados (Disponibles y en alquiler)
                    </p>
                    <button
                      class="btn"
                      style="width: 100%; padding: 10px; font-size: 13px"
                      onclick="cambiarSeccion('vehiculos')"
                    >
                      Ver Vehículos
                    </button>
                  </div>

                  <!-- Resumen de Reservas -->
                  <div class="estadistica">
                    <div
                      style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 15px;
                      "
                    >
                      <h3 style="font-size: 16px; color: var(--texto2); margin: 0">Reservas</h3>
                      <i
                        class="fa-solid fa-calendar-days"
                        style="font-size: 20px; color: var(--dorado)"
                      ></i>
                    </div>
                    <h2 id="inicioReservasActivas">0</h2>
                    <p style="font-size: 13px; margin-bottom: 15px">
                      Reservas activas para esta semana
                    </p>
                    <button
                      class="btn"
                      style="width: 100%; padding: 10px; font-size: 13px"
                      onclick="cambiarSeccion('reservas')"
                    >
                      Ver Reservas
                    </button>
                  </div>

                  <!-- Resumen de Clientes -->
                  <div class="estadistica">
                    <div
                      style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 15px;
                      "
                    >
                      <h3 style="font-size: 16px; color: var(--texto2); margin: 0">Clientes</h3>
                      <i class="fa-solid fa-users" style="font-size: 20px; color: var(--verde)"></i>
                    </div>
                    <h2 id="inicioTotalClientes">0</h2>
                    <p style="font-size: 13px; margin-bottom: 15px">
                      Usuarios registrados en la plataforma
                    </p>
                    <button
                      class="btn"
                      style="width: 100%; padding: 10px; font-size: 13px"
                      onclick="cambiarSeccion('clientes')"
                    >
                      Ver Clientes
                    </button>
                  </div>

                  <!-- Resumen de Pagos -->
                  <div class="estadistica">
                    <div
                      style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 15px;
                      "
                    >
                      <h3 style="font-size: 16px; color: var(--texto2); margin: 0">Pagos</h3>
                      <i
                        class="fa-solid fa-wallet"
                        style="font-size: 20px; color: var(--verde)"
                      ></i>
                    </div>
                    <h2 id="inicioIngresosMes">$0</h2>
                    <p style="font-size: 13px; margin-bottom: 15px">
                      Ingresos totales recaudados este mes
                    </p>
                    <button
                      class="btn"
                      style="width: 100%; padding: 10px; font-size: 13px"
                      onclick="cambiarSeccion('pagos')"
                    >
                      Ver Pagos
                    </button>
                  </div>

                  <!-- Resumen de Reportes -->
                  <div class="estadistica">
                    <div
                      style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 15px;
                      "
                    >
                      <h3 style="font-size: 16px; color: var(--texto2); margin: 0">Reportes</h3>
                      <i
                        class="fa-solid fa-chart-pie"
                        style="font-size: 20px; color: var(--rojo)"
                      ></i>
                    </div>
                    <h2 style="font-size: 24px; margin-bottom: 10px">General</h2>
                    <p style="font-size: 13px; margin-bottom: 15px">
                      Análisis de rendimiento y estadísticas
                    </p>
                    <button
                      class="btn"
                      style="width: 100%; padding: 10px; font-size: 13px"
                      onclick="cambiarSeccion('reportes')"
                    >
                      Ver Reportes
                    </button>
                  </div>
                </div>

                <!-- Sección inferior de acciones rápidas usando la clase .card del CSS -->
                <div style="margin-top: 35px" class="card">
                  <h3 style="margin-bottom: 15px; color: var(--texto)">Acciones Rápidas</h3>
                  <p style="color: var(--texto2); font-size: 14px; margin-bottom: 20px">
                    Utiliza estos accesos directos para gestionar las operaciones más frecuentes del
                    sistema de alquiler.
                  </p>
                  <div style="display: flex; gap: 15px; flex-wrap: wrap">
                    <button class="btn btn-verde" onclick="cambiarSeccion('vehiculos')">
                      <i class="fa-solid fa-plus"></i> Registrar Vehículo
                    </button>
                    <button class="btn" onclick="cambiarSeccion('reservas')">
                      <i class="fa-solid fa-calendar-plus"></i> Nueva Reserva
                    </button>
                  </div>
                </div>
              </div>
            </section>

            <!--
            ══════════════════════════════════════════════════════════
               CONTENEDOR: VEHÍCULOS  (id="vehiculos") — Panel Admin
               Formulario para registrar un vehículo nuevo
               (#btnRegistrarVehiculo) + tabla con los vehículos ya
               registrados (tbody#tablaVehiculos).
            ══════════════════════════════════════════════════════════
            --><section class="contenedor" id="vehiculos">
              <!--==============================
              TÍTULO Y BOTÓN DE APERTURA MODAL
              ==============================-->
              <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <h2>Gestión de Vehículos</h2>

                <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                  <div class="busqueda-crud" style="margin-top: 0;">
                    <input type="text" id="buscarVehiculo" placeholder="Buscar vehículo..." />
                    <button type="button" class="btn-buscar" id="btnBuscarVehiculo">
                      <i class="fa-solid fa-magnifying-glass"></i> Buscar
                    </button>
                  </div>

                  <!-- Botón para abrir la ventana emergente -->
                  <button class="btn btn-verde" id="btnMostrarFormVehiculo" onclick="abrirFormularioVehiculo()">
                    <i class="fa-solid fa-car"></i> Registrar Vehículo
                  </button>
                </div>
              </div>

              <!--==============================
              VENTANA MODAL EMERGENTE (FORMULARIO)
              ==============================-->
              <div class="modal-overlay" id="modalVehiculoOverlay">
                <div class="modal-contenido">
                  <div class="formulario-crud" style="margin-bottom: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                      <h2 id="tituloModalVehiculo">Registrar Nuevo Vehículo</h2>
                      <button type="button" class="btn-eliminar" onclick="cerrarFormularioVehiculo()" style="padding: 6px 10px;">
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                    </div>

                    <form id="formVehiculo" method="POST">
                        <input type="hidden" id="vehiculoIdEditar" name="id" value="" />
                        <div class="grupo-formulario">
                        <div class="campo">
                          <label>Placa</label>
                          <input id="placa" name="placa" placeholder="ABC123" />
                        </div>

                        <div class="campo">
                          <label>Marca</label>
                          <input id="marca" name="marca" placeholder="Mazda" />
                        </div>
                      </div>

                      <div class="grupo-formulario">
                        <div class="campo">
                          <label>Modelo</label>
                          <input  id="modelo" name="modelo" placeholder="Mazda 3"  />
                        </div>

                        <div class="campo">
                          <label>Año</label>
                          <input type="number" id="anio" name="anio" min="1980" max="2035" placeholder="2025"  />
                        </div>
                      </div>

                      <div class="grupo-formulario">
                        <div class="campo">
                          <label>Color</label>
                          <input  id="color" name="color" placeholder="Negro"  />
                        </div>

                        <div class="campo">
                          <label>Tipo</label>
                          <select id="tipo" name="tipo" >
                            <option value="">Seleccione tipo...</option>
                            <option value="Carro">Carro</option>
                            <option value="Moto">Moto</option>
                          </select>
                        </div>
                      </div>

                      <div class="grupo-formulario">
                        <div class="campo" style="grid-column: 1 / -1;">
                          <label for="imagenVehiculo">Imagen del vehículo</label>
                          <input type="file" id="imagenVehiculo" name="imagen_file" accept="image/jpeg,image/png,image/webp,image/gif" />
                          <small style="color: var(--texto2);">JPG, PNG, WEBP o GIF. Máximo 5 MB.</small>
                        </div>
                      </div>

                      <div class="grupo-formulario">
                        <div class="campo" style="grid-column: 1 / -1;">
                          <label>Precio por día</label>
                          <input type="number" id="precio" name="precio" placeholder="150000"  />
                        </div>
                      </div>

                      <div class="botones-formulario" style="margin-top: 15px;">
                        <button type="submit" id="btnRegistrarVehiculo" class="btn-registrar">
                          <i class="fa-solid fa-check"></i> Registrar vehículo
                        </button>
                        <button type="button" class="btn btn-rojo" onclick="cerrarFormularioVehiculo()">
                          <i class="fa-solid fa-xmark"></i> Cancelar
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <!--==============================
              TABLA
              ==============================-->
              <div class="page-body">
                <div class="tabla-contenedor">
                  <table class="tabla-zynco">
                    <thead>
                      <tr>
                        <th>Placa</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Año</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>

                    <tbody id="tablaVehiculos">
                    </tbody>
                  </table>
                </div>
              </div>
            </section>
            <!--
            ══════════════════════════════════════════════════════════
               CONTENEDOR: RESERVAS  (id="reservas") — Panel Admin
               Buscador (#buscarReserva) + tabla de reservas
               (tbody#tablaReservas) con acciones ver/editar/cancelar.
            ══════════════════════════════════════════════════════════
            -->
<section class="contenedor" id="reservas">
              <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <h2>Administración de Reservas</h2>

                <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                  <div class="busqueda-crud" style="margin-top: 0;">
                    <input
                      type="text"
                      id="buscarReserva"
                      placeholder="Buscar por cliente, vehículo o estado..."
                    />

                    <button class="btn-buscar">
                      <i class="fa-solid fa-magnifying-glass"></i>
                      Buscar
                    </button>
                  </div>

                  <!-- Botón para abrir la ventana emergente de nueva reserva -->
                  <button class="btn btn-verde" id="btnMostrarFormReserva" onclick="abrirFormularioReserva()">
                    <i class="fa-solid fa-calendar-plus"></i> Nueva Reserva
                  </button>
                </div>
              </div>

              <div class="page-body">
                
                <!--====================================
                VENTANA MODAL EMERGENTE (REGISTRO DE RESERVA)
                =====================================-->
                <div class="modal-overlay" id="modalReservaOverlay">
                  <div class="modal-contenido">
                    <div class="formulario-crud" style="margin-bottom: 0;">
                      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h2>Crear Nueva Reserva</h2>
                        <button type="button" class="btn-eliminar" onclick="toggleFormularioReserva()" style="padding: 6px 10px;">
                          <i class="fa-solid fa-xmark"></i>
                        </button>
                      </div>

                      <form id="formReserva" method="POST">
                        
                        <div class="grupo-formulario">
                          <div class="campo">
                            <label for="selectClienteReserva">Cliente</label>
                            <select id="selectClienteReserva" name="cliente_id" required>
                              <option value="">Seleccione un cliente...</option>
                            </select>
                          </div>

                          <div class="campo">
                            <label for="selectVehiculoReserva">Vehículo</label>
                            <select id="selectVehiculoReserva" name="vehiculo_id" required>
                              <option value="">Seleccione un vehículo...</option>
                            </select>
                          </div>
                        </div>

                        <div class="grupo-formulario">
                          <div class="campo">
                            <label for="fechaEntrega">Fecha de Entrega</label>
                            <input type="date" id="fechaEntrega" name="fecha_entrega" required>
                          </div>

                          <div class="campo">
                            <label for="fechaDevolucion">Fecha de Devolución</label>
                            <input type="date" id="fechaDevolucion" name="fecha_devolucion" required>
                          </div>
                        </div>

                        <div class="grupo-formulario">
                          <div class="campo">
                            <label for="precioDiaReserva">Precio por día</label>
                            <input type="text" id="precioDiaReserva" placeholder="$0" readonly>
                          </div>

                          <div class="campo">
                            <label for="diasReserva">Días de alquiler</label>
                            <input type="number" id="diasReserva" value="0" readonly>
                          </div>
                        </div>

                        <div class="grupo-formulario">
                          <div class="campo">
                            <label for="valorReserva">Valor Total ($)</label>
                            <input type="number" id="valorReserva" name="valor" placeholder="Se calcula automáticamente" min="0" step="0.01" readonly required>
                            <small style="color:var(--texto2);display:block;margin-top:6px;">Precio por día × días de alquiler</small>
                          </div>

                          <div class="campo">
                            <label for="estadoReservaInicial">Estado Inicial</label>
                            <input type="text" id="estadoReservaInicial" value="Pendiente" readonly>
                            <input type="hidden" name="estado" value="Pendiente">
                          </div>
                        </div>

                        <div class="botones-formulario" style="margin-top: 15px;">
                          <button type="submit" class="btn-registrar">
                            <i class="fa-solid fa-check"></i> Guardar Reserva
                          </button>
                          <button type="button" class="btn btn-rojo" onclick="toggleFormularioReserva()">
                            <i class="fa-solid fa-xmark"></i> Cancelar
                          </button>
                        </div>

                      </form>
                    </div>
                  </div>
                </div>

                <!--====================================
                MODAL DE CONFIRMACIÓN DE RESERVA
                =====================================-->
                <div class="modal-overlay" id="modalConfirmarReservaOverlay" aria-hidden="true">
                  <div class="modal-contenido" style="max-width: 900px; width: 92%;">
                    <div class="formulario-crud" style="margin-bottom: 0;">
                      <div style="display:flex;justify-content:space-between;align-items:center;gap:15px;margin-bottom:15px;">
                        <h2>Confirmar reserva</h2>
                        <button type="button" class="btn-eliminar" onclick="cerrarConfirmacionReserva()" style="padding:6px 10px;">
                          <i class="fa-solid fa-xmark"></i>
                        </button>
                      </div>
                      <div id="contenidoConfirmacionReserva" style="display:grid;grid-template-columns:minmax(220px,320px) 1fr;gap:24px;align-items:start;"></div>
                      <div class="botones-formulario" style="margin-top:20px;">
                        <button type="button" class="btn btn-verde" id="btnConfirmarGuardarReserva">
                          <i class="fa-solid fa-check"></i> Confirmar y guardar reserva
                        </button>
                        <button type="button" class="btn btn-rojo" onclick="cerrarConfirmacionReserva()">
                          <i class="fa-solid fa-xmark"></i> Volver
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!--====================================
                CONTENEDOR DE LA TABLA
                =====================================-->
                <div class="tabla-contenedor">
                  <table class="tabla-zynco">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Vehículo</th>
                        <th>Entrega</th>
                        <th>Devolución</th>
                        <th>Valor</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>

                    <tbody id="tablaReservas">
                      <tr><td colspan="8" style="text-align:center;">Cargando reservas...</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </section>            <!--
            ══════════════════════════════════════════════════════════
               CONTENEDOR: CLIENTES  (id="clientes") — Panel Admin
               Buscador (#buscarCliente) + tabla de clientes
               (tbody#tablaClientes) con acciones editar/ver/desactivar.
            ══════════════════════════════════════════════════════════
            --><section class="contenedor" id="clientes">
              <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <h2>Administración de Clientes</h2>

                <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                  <div class="busqueda-crud" style="margin-top: 0;">
                    <input
                      type="text"
                      id="buscarCliente"
                      placeholder="Buscar por documento, nombre o correo..."
                    />

                    <button class="btn-buscar">
                      <i class="fa-solid fa-magnifying-glass"></i>
                      Buscar
                    </button>
                  </div>

                  <!-- Botón para abrir la ventana emergente -->
                  <button class="btn btn-verde" id="btnMostrarFormCliente" onclick="abrirFormularioCliente()">
                    <i class="fa-solid fa-user-plus"></i> Registrar Cliente
                  </button>
                </div>
              </div>

              <div class="page-body">
                
                <!--====================================
                VENTANA MODAL EMERGENTE PARA EL FORMULARIO
                =====================================-->
                <div class="modal-overlay" id="modalClienteOverlay">
                  <div class="modal-contenido">
                    <div class="formulario-crud" style="margin-bottom: 0;">
                      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h2 id="tituloModalCliente">Registrar Nuevo Cliente</h2>
                        <button type="button" class="btn-eliminar" onclick="cerrarFormularioCliente()" style="padding: 6px 10px;">
                          <i class="fa-solid fa-xmark"></i>
                        </button>
                      </div>

                      <!-- Los clientes no tienen cuenta en el sistema (no inician sesión),
                           por eso este formulario no incluye contraseña ni rol: siempre
                           son "Cliente" y no necesitan credenciales. -->
                      <form id="formCliente">
                        <input type="hidden" id="clienteIdOriginal" name="id_original" value="">

                        <div class="grupo-formulario">
                          <div class="campo">
                            <label for="documentoCliente">Documento</label>
                            <input type="text" id="documentoCliente" name="id" placeholder="Ej. 1078123456" required>
                          </div>

                          <div class="campo">
                            <label for="nombreCliente">Nombre Completo</label>
                            <input type="text" id="nombreCliente" name="nombre" placeholder="Ej. Juan Pérez" required>
                          </div>
                        </div>

                        <div class="grupo-formulario">
                          <div class="campo">
                            <label for="correoCliente">Correo Electrónico</label>
                            <input type="email" id="correoCliente" name="email" placeholder="Ej. juan@email.com">
                          </div>

                          <div class="campo">
                            <label for="telefonoCliente">Teléfono</label>
                            <input type="tel" id="telefonoCliente" name="telefono" placeholder="Ej. 3001234567">
                          </div>
                        </div>

                        <div class="grupo-formulario">
                          <div class="campo">
                            <label for="direccionCliente">Dirección</label>
                            <input type="text" id="direccionCliente" name="direccion" placeholder="Ej. Calle 123 #45-67">
                          </div>
                        </div>

                        <div class="botones-formulario" style="margin-top: 10px;">
                          <button type="submit" class="btn-registrar" id="btnGuardarCliente">
                            <i class="fa-solid fa-check"></i> Guardar Cliente
                          </button>
                          <button type="button" class="btn btn-rojo" onclick="cerrarFormularioCliente()">
                            <i class="fa-solid fa-xmark"></i> Cancelar
                          </button>
                        </div>

                      </form>
                    </div>
                  </div>
                </div>

                <!--====================================
                CONTENEDOR DE LA TABLA
                =====================================-->
                <div class="tabla-contenedor">
                  <table class="tabla-zynco">
                    <thead>
                      <tr>
                        <th>Documento</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Fecha de Registro</th>
                        <th>Dirección</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>

                    <tbody id="tablaClientes">
                      <tr>
                        <td>1078123456</td>
                        <td>Juan Pérez</td>
                        <td>juan@email.com</td>
                        <td>3001234567</td>
                        <td>2023-01-15</td>
                        <td>Calle 123 #45-67</td>
                        <td>
                          <span class="estado disponible"> Activo </span>
                        </td>
                        <td>
                          <button class="btn-editar" title="Editar">
                            <i class="fa-solid fa-pen"></i>
                          </button>
                          <button class="btn-ver" title="Ver información">
                            <i class="fa-solid fa-eye"></i>
                          </button>
                          <button class="btn-eliminar" title="Desactivar">
                            <i class="fa-solid fa-user-slash"></i>
                          </button>
                        </td>
                      </tr>

                      <tr>
                        <td>1000987654</td>
                        <td>María Gómez</td>
                        <td>maria@email.com</td>
                        <td>3209876543</td>
                        <td>2023-01-20</td>
                        <td>Av. Siempre Viva123</td>
                        <td>
                          <span class="estado disponible"> Activo </span>
                        </td>
                        <td>
                          <button class="btn-editar" title="Editar">
                            <i class="fa-solid fa-pen"></i>
                          </button>
                          <button class="btn-ver" title="Ver información">
                            <i class="fa-solid fa-eye"></i>
                          </button>
                          <button class="btn-eliminar" title="Desactivar">
                            <i class="fa-solid fa-user-slash"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </section>
            <!--
            ══════════════════════════════════════════════════════════
               CONTENEDOR: PAGOS  (id="pagos") — Panel Administrador
               Buscador (#buscarPago) + tabla de pagos
               (tbody#tablaPagos) con acceso al comprobante.
            ══════════════════════════════════════════════════════════
            --><section class="contenedor" id="pagos">
              <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <h2>Administración de Pagos</h2>

                <div class="busqueda-crud" style="margin-top: 0;">
                  <input
                    type="text"
                    id="buscarPago"
                    placeholder="Buscar por cliente, reserva o método de pago..."
                  />

                  <button class="btn-buscar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Buscar
                  </button>
                </div>
              </div>

              <div class="page-body">
                
                <!--====================================
                VENTANA MODAL EMERGENTE (REGISTRO DE PAGO)
                =====================================-->
                <div class="modal-overlay" id="modalPagoOverlay">
                  <div class="modal-contenido">
                    <div class="formulario-crud" style="margin-bottom: 0;">
                      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h2>Registrar Nuevo Pago</h2>
                        <button type="button" class="btn-eliminar" onclick="toggleFormularioPago()" style="padding: 6px 10px;">
                          <i class="fa-solid fa-xmark"></i>
                        </button>
                      </div>

                      <form id="formPago" novalidate>
                        <input type="hidden" id="pagoId" name="pago_id">
                        
                        <div class="grupo-formulario">
                          <div class="campo">
                            <label for="clientePago">Cliente</label>
                            <input type="text" id="clientePago" readonly>
                          </div>

                          <div class="campo">
                            <label for="selectReservaPago">ID Reserva</label>
                            <input type="text" id="selectReservaPago" name="reserva_id" readonly required>
                          </div>
                        </div>

                        <div class="grupo-formulario">
                          <div class="campo">
                            <label for="valorPago">Valor a Pagar ($)</label>
                            <input type="number" id="valorPago" name="monto" min="1" step="0.01" placeholder="Ej. 540000" required>
                          </div>

                          <div class="campo">
                            <label for="metodoPago">Método de Pago</label>
                            <select id="metodoPago" name="metodo_pago" required>
                              <option value="">Seleccione método...</option>
                              <option value="Tarjeta">Tarjeta de Crédito / Débito</option>
                              <option value="Transferencia">Transferencia Bancaria</option>
                              <option value="Efectivo">Efectivo</option>
                            </select>
                          </div>
                        </div>

                        <div class="botones-formulario" style="margin-top: 15px;">
                          <button type="submit" class="btn-registrar">
                            <i class="fa-solid fa-check"></i> Guardar Pago
                          </button>
                          <button type="button" class="btn btn-rojo" onclick="toggleFormularioPago()">
                            <i class="fa-solid fa-xmark"></i> Cancelar
                          </button>
                        </div>

                      </form>
                    </div>
                  </div>
                </div>

                <!--====================================
                CONTENEDOR DE LA TABLA
                =====================================-->
                <div class="tabla-contenedor">
                  <table class="tabla-zynco">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Reserva</th>
                        <th>Fecha</th>
                        <th>Método</th>
                        <th>Valor</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>

                    <tbody id="tablaPagos">
                      <tr><td colspan="8" style="text-align:center;">Cargando pagos...</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="modal-overlay" id="modalComprobanteOverlay" aria-hidden="true">
                <div class="modal-contenido comprobante-modal" role="dialog" aria-modal="true" aria-labelledby="tituloComprobante">
                  <div class="formulario-crud" style="margin-bottom:0;">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:15px;">
                      <h2 id="tituloComprobante">Comprobante de pago</h2>
                      <button type="button" class="btn-eliminar" onclick="cerrarComprobante()" aria-label="Cerrar comprobante"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div id="contenidoComprobante" class="comprobante-contenido" aria-live="polite"></div>
                    <div class="botones-formulario"><button type="button" class="btn btn-rojo" onclick="cerrarComprobante()">Cerrar</button></div>
                  </div>
                </div>
              </div>

            </section>
            <!--
            ══════════════════════════════════════════════════════════
               CONTENEDOR: REPORTES  (id="reportes") — Panel Admin
               Tarjetas resumen (.dashboard-cards), un espacio para
               gráfica (.grafica-contenedor, aún sin datos reales) y
               una lista de resumen general (.lista-resumen).
            ══════════════════════════════════════════════════════════
            -->

<section class="contenedor" id="reportes">
              <div class="page-header">
                <h2>Reportes del Sistema</h2>
              </div>

              <div class="page-body">
                <!--====================================
                TARJETAS PRINCIPALES (Usando .estadisticas y .estadistica)
                =====================================-->
                <div class="estadisticas">
                  
                  <div class="estadistica">
                    <i class="fa-solid fa-car" style="font-size: 24px; color: var(--azul); margin-bottom: 15px;"></i>
                    <h2 id="reporteTotalVehiculos">0</h2>
                    <p>Total registrados (Disponibles y alquilados)</p>
                  </div>

                  <div class="estadistica">
                    <i class="fa-solid fa-calendar-check" style="font-size: 24px; color: var(--dorado); margin-bottom: 15px;"></i>
                    <h2 id="reporteTotalReservas">0</h2>
                    <p>Reservas registradas este mes</p>
                  </div>

                  <div class="estadistica">
                    <i class="fa-solid fa-users" style="font-size: 24px; color: var(--verde); margin-bottom: 15px;"></i>
                    <h2 id="reporteTotalClientes">0</h2>
                    <p>Clientes registrados en la plataforma</p>
                  </div>

                  <div class="estadistica">
                    <i class="fa-solid fa-dollar-sign" style="font-size: 24px; color: var(--verde); margin-bottom: 15px;"></i>
                    <h2 id="reporteTotalIngresos">$0</h2>
                    <p>Ingresos totales recaudados este mes</p>
                  </div>

                </div>

                <!--====================================
                CONTENEDORES SECUNDARIOS / ESTADOS
                =====================================-->
                <div class="estadisticas" style="margin-top: 25px;">
                  
                  <div class="estadistica">
                    <h3 style="color: var(--texto); display: flex; align-items: center; gap: 10px;">
                      <i class="fa-solid fa-car-side" style="color: var(--verde);"></i> Desglose de Vehículos
                    </h3>
                    <p style="margin-top: 15px;">Disponibles: <strong id="repVehiculosDisponibles" style="color: var(--verde);">0</strong></p>
                    <p style="margin-top: 8px;">Entregados: <strong id="repVehiculosAlquilados" style="color: var(--azul);">0</strong></p>
                    <p style="margin-top: 8px;">En mantenimiento: <strong id="repVehiculosMantenimiento" style="color: var(--dorado);">0</strong></p>
                    <p style="margin-top: 8px;">Reservados: <strong id="repVehiculosEnReserva" style="color: var(--azul);">0</strong></p>
                  </div>

                  <div class="estadistica">
                    <h3 style="color: var(--texto); display: flex; align-items: center; gap: 10px;">
                      <i class="fa-solid fa-tasks" style="color: var(--azul);"></i> Operaciones Pendientes
                    </h3>
                    <p style="margin-top: 15px;">Reservas por aprobar: <strong id="repReservasPendientes" style="color: var(--azul);">0</strong></p>
                    <p style="margin-top: 8px;">Pagos pendientes: <strong id="repPagosPendientes" style="color: var(--dorado);">0</strong></p>
                    <p style="margin-top: 8px;">Estado del sistema: <strong style="color: var(--verde);">Activo</strong></p>
                  </div>

                </div>

                <!--====================================
                CONTENEDORES RESERVADOS PARA GRÁFICAS
                =====================================-->
                <div class="estadisticas" style="margin-top: 25px;">
                  
                  <div class="estadistica" style="grid-column: 1 / -1; min-height: 260px;">
                    <i class="fa-solid fa-chart-line" style="font-size: 32px; color: var(--azul); margin-bottom: 12px;"></i>
                    <h3 style="margin-bottom: 5px;">Actividad Mensual del Sistema</h3>
                    <p style="color: var(--texto2); font-size: 14px;">Reservas creadas durante los últimos seis meses.</p>
                    <div id="graficaActividadMensual" class="grafica-actividad" role="img" aria-label="Gráfica de reservas mensuales"></div>
                  </div>

                </div>
              </div>
            </section>

            <!--
            ══════════════════════════════════════════════════════════
               CONTENEDOR: USUARIOS  (id="usuarios") — Panel Admin
               Gestión de las cuentas que pueden entrar a Zynco (tabla
               "usuarios" de la base de datos). Como el sistema ya no
               tiene registro público, las cuentas nuevas SOLO se crean
               desde aquí. Formulario de arriba sirve para crear Y para
               editar (al presionar "Editar" en una fila, se rellena
               con esos datos y el botón pasa a decir "Guardar
               cambios"); tabla de abajo (tbody#tablaUsuarios) la llena
               app.js pidiéndosela a "php/usuarios_listar.php".
            ══════════════════════════════════════════════════════════
            -->

            <section class="contenedor" id="usuarios">
              <div class="page-header">
                <h2>Gestión de Usuarios</h2>
                
                <div class="tarjeta-sesion-actual">
    <h3><i class="fa-solid fa-id-card"></i> Mi Sesión Actual</h3>
    <p><strong>Nombre:</strong> <span id="perfilNombreSession">Cargando...</span></p>
    <p><strong>Correo Electrónico:</strong> <span id="perfilCorreoSession">Cargando...</span></p>
    <p><strong>Rol en el Sistema:</strong> <span id="perfilRolSession" class="badge-rol">Cargando...</span></p>
</div>
              </div>

              <!-- Campo oculto: vacío = se está creando un usuario nuevo;
                   con un id = se está editando ese usuario. Lo llena/limpia
                   app.js, la persona no lo ve. -->
              <input type="hidden" id="usuarioIdEditar" value="" />

              <div class="formulario-crud">
                <div class="grupo-formulario">
                  <div class="campo">
                    <label for="usuarioId">ID / Documento</label>
                    <input type="text" id="usuarioId" placeholder="Ej. 10102030" maxlength="30" />
                  </div>

                  <div class="campo">
                    <label for="usuarioNombre">Nombre</label>
                    <input type="text" id="usuarioNombre" placeholder="Nombre completo" />
                  </div>

                  <div class="campo">
                    <label for="usuarioCorreo">Correo</label>
                    <input type="email" id="usuarioCorreo" placeholder="correo@zynco.com" />
                  </div>
                </div>

                <div class="grupo-formulario">
                  <div class="campo">
                    <label for="usuarioRol">Rol</label>
                    <select id="usuarioRol">
                      <option value="Administrador">Administrador</option>
                      <option value="Personal">Personal</option>
                    </select>
                  </div>

                  <div class="campo">
                    <label for="usuarioTelefono">Teléfono</label>
                    <input type="tel" id="usuarioTelefono" placeholder="Ej. 3001234567" />
                  </div>

                  <div class="campo">
                    <label for="usuarioDireccion">Dirección</label>
                    <input type="text" id="usuarioDireccion" placeholder="Ej. Calle 50 #10-20" />
                  </div>
                </div>

                <div class="grupo-formulario">
                  <div class="campo">
                    <label for="usuarioPassword">Contraseña</label>
                    <input type="password" id="usuarioPassword" placeholder="Mínimo 4 caracteres" />
                    <!-- Al editar, se puede dejar vacía para conservar la contraseña. -->
                  </div>
                </div>

                <div class="botones-formulario">
                  <button type="button" class="btn-registrar" id="btnGuardarUsuario">
                    <i class="fa-solid fa-floppy-disk"></i>

                    Guardar usuario
                  </button>

                  <button
                    type="button"
                    class="btn-eliminar"
                    id="btnCancelarEdicionUsuario"
                    style="display: none"
                  >
                    Cancelar edición
                  </button>
                </div>
              </div>

              <div class="tabla-contenedor">
                <table class="tabla-zynco">
                  <thead>
                    <tr>
                      <th>ID / Documento</th>
                      <th>Nombre</th>
                      <th>Correo</th>
                      <th>Rol</th>
                      <th>Teléfono</th>
                      <th>Dirección</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>

                  <!-- Filas generadas por app.js con lo que devuelva
                       "php/usuarios_listar.php". Vacío mientras carga. -->
                  <tbody id="tablaUsuarios"></tbody>
                </table>
              </div>
            </section>


            <!--
            ══════════════════════════════════════════════════════════
               MODAL GENERAL  (id="modal") — Panel Administrador
               Ventana reutilizable y vacía por ahora; app.js debe
               rellenarla según la acción (nuevo vehículo, editar
               vehículo, nuevo cliente, nueva reserva, confirmaciones).
               No eliminar.
            ══════════════════════════════════════════════════════════
            -->

            <div id="modal" class="modal">
              <!--
        Ventana reutilizable.

        Se utilizará para:

        • Nuevo vehículo
        • Editar vehículo
        • Nuevo cliente
        • Nueva reserva
        • Confirmaciones

        No eliminar.
    -->
            </div>

            <!--
            ══════════════════════════════════════════════════════════
               NOTIFICACIONES  (id="notificaciones") — Panel Admin
               Contenedor vacío y reutilizable donde JavaScript debe ir
               agregando los avisos (✔ guardado, ✖ error, ⚠ advertencia).
               No eliminar.
            ══════════════════════════════════════════════════════════
            -->

            <div id="notificaciones">
              <!--
        Aquí aparecerán:

        ✔ Guardado correctamente

        ✖ Error

        ⚠ Advertencias

        Todo será generado desde JavaScript.
    -->
            </div>
          </section>
          <!-- fin de la sección "contenidoPrincipal" -->
        </main>
        <!-- fin de <main id="principal"> -->

        <!--====================================
        MODAL GLOBAL: DETALLE DE RESERVA
        Se movió fuera de cualquier .contenedor de pestaña porque
        .contenedor{display:none} lo ocultaba salvo que esa pestaña
        estuviera activa (por eso solo aparecía tras entrar a Pagos).
        Ahora es accesible desde Reservas y desde Pagos por igual.
        =====================================-->
        <div class="modal-overlay" id="modalDetalleReservaOverlay" aria-hidden="true">
          <div class="modal-contenido detalle-reserva-modal" role="dialog" aria-modal="true" aria-labelledby="tituloDetalleReserva">
            <div class="formulario-crud" style="margin-bottom:0;">
              <div style="display:flex;justify-content:space-between;align-items:center;gap:15px;">
                <h2 id="tituloDetalleReserva">Detalle de la reserva</h2>
                <button type="button" class="btn-eliminar" onclick="cerrarDetalleReserva()" aria-label="Cerrar detalle"><i class="fa-solid fa-xmark"></i></button>
              </div>
              <div id="contenidoDetalleReserva" class="detalle-reserva-contenido" aria-live="polite"></div>
              <div class="botones-formulario"><button type="button" class="btn btn-rojo" onclick="cerrarDetalleReserva()">Cerrar</button></div>
            </div>
          </div>
        </div>
      </section>
      <!-- fin de <section id="dashboard"> -->
    </div>
    <!-- fin de <div id="panel-admin"> -->

    <!-- El panel de cliente se eliminó: Zynco ahora es una
         herramienta interna, solo para el personal de la empresa. -->

    <!--
    ████████████████████████████████████████████████████████████████████
       FOOTER  (id="footer")
       Pie de página, se ve siempre, dentro o fuera de sesión.
    ████████████████████████████████████████████████████████████████████
    -->

    <footer id="footer">
      <span> Zynco © 2026 </span>
    </footer>

    <!--==========================================================
                JAVASCRIPT
===========================================================-->

<script src="js/app.js"></script>
  </body>
</html>