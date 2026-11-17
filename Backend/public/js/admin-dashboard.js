/**
 * FARUSAC - SISTEMA DE INFORMES: ADMIN DASHBOARD (admin-dashboard.js)
 */

const moduleInfo = {
    carga: {
        title: 'Carga de Datos',
        description: 'Este módulo permite la carga masiva de usuarios (docentes, coordinadores y administradores) mediante un archivo estructurado en formato CSV. Facilita la administración rápida de cuentas autorizadas para acceder al sistema.',
        image: '/images/Admin/CargaDatos.png'
    },
    usuarios: {
        title: 'Gestión de Usuarios',
        description: 'Permite administrar la información general de los usuarios registrados en la plataforma. Podrás buscar, filtrar, modificar roles e inhabilitar cuentas de acceso de forma individual y segura.',
        image: '/images/Admin/GestionUsuarios.png'
    },
    cursos: {
        title: 'Gestión de Cursos',
        description: 'Este espacio está dedicado a la creación, edición y asignación de cursos de la facultad de arquitectura. Permite asignar docentes correspondientes y organizar la planificación académica.',
        image: '/images/Admin/GestionCursos.png'
    },
    informes: {
        title: 'Gestión de Informes',
        description: 'Permite dar seguimiento a los informes de actividades consolidados de los docentes. Podrás visualizar estadísticas de entrega, revisar bitácoras, exportar datos y validar la información consolidada.',
        image: '/images/Admin/GestionInformes.png'
    }
};

function showInfo(moduleKey) {
    const info = moduleInfo[moduleKey];
    if (!info) return;

    document.getElementById('modalTitle').textContent = info.title;
    document.getElementById('modalDescription').textContent = info.description;
    document.getElementById('modalIcon').src = info.image;
    document.getElementById('modalIcon').alt = info.title;

    window.openModal('infoModal');
}

function closeModal() {
    window.closeModal('infoModal');
}