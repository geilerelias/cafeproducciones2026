import aboutImage from '../../images/bg/00.jpg';
import projectImage from '../../images/bg/12.jpg';
import contactImage from '../../images/bg/bg-contact-2.jpg';
import heroImage from '../../images/bg/Inicio.jpg';
import logoTransparent from '../../images/logo/logo-transparent.png';
import logoImage from '../../images/logo/logo.png';
import carmenPhoto from '../../images/team/carmenbarros.png';
import teamPhoto from '../../images/team/equipodetrabajo.jpg';
import ferneyPhoto from '../../images/team/ferneybarros.png';
import geilerPhoto from '../../images/team/geilerelias.png';
import gennerPhoto from '../../images/team/gennereliasradillosarmiento.png';

const serviceImageModules = import.meta.glob('../../images/services/**/*.{jpg,jpeg,png,gif}', {
    eager: true,
    import: 'default',
}) as Record<string, string>;

const galleryImageModules = import.meta.glob('../../images/gallery/*.{jpg,jpeg,png,gif}', {
    eager: true,
    import: 'default',
}) as Record<string, string>;

export const brand = {
    name: 'CAFE Producciones',
    legalName: 'CAFE Producciones',
    tagline: 'Produccion, planificacion y logistica para eventos memorables',
    description:
        'Empresa de produccion, logistica, tecnologia y acompanamiento para eventos sociales, corporativos e institucionales en Riohacha, La Guajira y Colombia.',
    phone: '+57 300 436 1660',
    secondaryPhone: '+57 300 328 5091',
    email: 'contacto@cafeproducciones.com',
    address: 'Riohacha, Colombia',
    city: 'Riohacha',
    region: 'La Guajira',
    country: 'Colombia',
    whatsapp: 'https://wa.me/573004361660',
    instagram: 'https://www.instagram.com/cafeproduccionesrio',
    facebook: 'https://www.facebook.com/CAFE-Producciones-101286955178495',
    mapEmbed:
        'https://maps.google.com/maps?width=100%25&height=600&hl=es&q=11.52277934403712,%20-72.90622382964963+(CAFE%20Producciones)&t=&z=14&ie=UTF8&iwloc=B&output=embed',
};

export const assets = {
    heroImage,
    aboutImage,
    contactImage,
    projectImage,
    logoImage,
    logoTransparent,
    teamPhoto,
};

export const navigation = [
    { title: 'Inicio', route: 'home' },
    { title: 'Nosotros', route: 'about-us' },
    { title: 'Servicios', route: 'our-services' },
    { title: 'Galeria', route: 'our-gallery' },
    { title: 'Noticias', route: 'news.index' },
    { title: 'Contacto', route: 'contact-us' },
];

export const features = [
    {
        title: 'Dedicados',
        description: 'La pasion por nuestro trabajo nos mantiene enfocados en entregar eventos con dedicacion y criterio.',
    },
    {
        title: 'Precavidos',
        description: 'Planificamos escenarios y contingencias para que la operacion no dependa de la improvisacion.',
    },
    {
        title: 'Detallistas',
        description: 'Supervisamos necesidades tecnicas, logisticas y de atencion antes y durante cada evento.',
    },
    {
        title: 'Innovadores',
        description: 'Integramos tecnologia, equipos audiovisuales y soluciones creativas para experiencias memorables.',
    },
    {
        title: 'Organizados',
        description: 'Gestionamos tiempos, proveedores y montaje con procesos claros desde la planeacion hasta el cierre.',
    },
    {
        title: 'Personalizables',
        description: 'Adaptamos el servicio a los objetivos, publico, presupuesto y formato de cada cliente.',
    },
];

export const team = [
    { name: 'Carmen Barros Diaz', role: 'Fundadora, CEO y gerente', image: carmenPhoto },
    { name: 'Genner Radillo Sarmiento', role: 'Director de operaciones', image: gennerPhoto },
    { name: 'Ferney Barros Diaz', role: 'Fundador y coordinador general', image: ferneyPhoto },
    { name: 'Geiler Radillo Sarmiento', role: 'Ingeniero de desarrollo', image: geilerPhoto },
];

export const services = [
    {
        slug: 'transmision',
        title: 'Transmision de eventos en vivo o diferido',
        description:
            'Equipos de ultima tecnologia para transmitir eventos en vivo y ampliar el alcance hacia publicos remotos desde redes sociales, sitios web o canales privados.',
    },
    {
        slug: 'sonido',
        title: 'Sonido para auditorios y exteriores',
        description:
            'Sistemas de sonido profesional para eventos empresariales, institucionales y sociales, ajustados al espacio, publico y tipo de actividad.',
    },
    {
        slug: 'montaje',
        title: 'Montaje de todo tipo de evento',
        description: 'Estructuras, tarimas, escenarios y soluciones de montaje optimizadas para tiempos, seguridad y costos.',
    },
    {
        slug: 'pirotecnia',
        title: 'Pirotecnia de escenarios y aerea',
        description: 'Espectaculos especiales para celebraciones, actos y momentos de alto impacto, coordinados segun lugar, duracion y presupuesto.',
    },
    {
        slug: 'vallas',
        title: 'Vallas de contencion de publico',
        description: 'Delimitacion de espacios para eventos temporales, espectaculos, desfiles y procesiones en espacios abiertos o cerrados.',
    },
    {
        slug: 'personal',
        title: 'Personal logistico',
        description: 'Equipo humano para soporte tecnico, atencion, coordinacion de flujos, transporte y operacion general del evento.',
    },
    {
        slug: 'moviliarios',
        title: 'Carpas, sillas, mesas y equipos',
        description: 'Mobiliario, toldos, carpas, pisos, stands, computadores e impresoras para actividades empresariales y sociales.',
    },
    {
        slug: 'filmacion',
        title: 'Filmacion general y entrevistas',
        description: 'Produccion audiovisual para registro, promocion, entrevistas, presentaciones y piezas de comunicacion.',
    },
    {
        slug: 'refrigerios',
        title: 'Refrigerios',
        description: 'Brunch y refrigerios para cursos, seminarios, reuniones empresariales y actividades institucionales.',
    },
    {
        slug: 'pantallas',
        title: 'Pantallas',
        description:
            'Alquiler, traslado, armado, desarmado y operacion de pantallas para eventos corporativos, actos institucionales y carteleria digital.',
    },
    {
        slug: 'iluminacion',
        title: 'Iluminacion',
        description: 'Diseno de ambientes con luces para escenarios, salones, fiestas, conciertos, stands y eventos corporativos.',
    },
    {
        slug: 'piso-led',
        title: 'Piso led',
        description: 'Pistas y pisos LED para escenarios, pasarelas, fiestas, exposiciones y experiencias visuales interactivas.',
    },
    {
        slug: 'escenografia',
        title: 'Escenografias',
        description: 'Conceptualizacion, diseno y ejecucion de escenarios integrales para eventos de alto impacto.',
    },
].map((service) => {
    const images = Object.entries(serviceImageModules)
        .filter(([path]) => path.includes(`/services/${service.slug}/`))
        .sort(([a], [b]) => a.localeCompare(b))
        .map(([, image]) => image);

    return {
        ...service,
        images,
        image: images[0] ?? assets.heroImage,
    };
});

export const galleryImages = Object.entries(galleryImageModules)
    .sort(([a], [b]) => a.localeCompare(b))
    .map(([, image]) => image);

export const processSteps = [
    ['Investigacion y planificacion', 'Entendemos objetivos, publico, alcance, presupuesto y riesgos del evento.'],
    ['Diseno y conceptualizacion', 'Creamos la experiencia, la identidad visual, el montaje y la narrativa del evento.'],
    ['Logistica y produccion', 'Coordinamos proveedores, equipos, personal, tiempos, transporte y montaje.'],
    ['Ejecucion y coordinacion', 'Supervisamos la operacion en campo para mantener continuidad y calidad.'],
    ['Evaluacion y mejora', 'Revisamos resultados y aprendizajes para elevar el estandar en futuros proyectos.'],
];

export const notices = [
    {
        title: 'Joven elige Joven',
        date: 'Octubre 2021',
        description: 'Produccion integral para elecciones de consejos de juventud con equipo logistico y tecnico.',
        image: galleryImages.find((image) => image.includes('joveneligejoven')) ?? assets.heroImage,
        link: 'https://www.instagram.com/p/CVfwRpgJKfy/',
        platform: 'Instagram',
        category: 'Noticias',
    },
    {
        title: 'Jornada de actualizacion con OIM',
        date: 'Octubre 2021',
        description: 'Acompanamiento logistico, soporte tecnico, alimentacion y apoyo operativo para jornada institucional.',
        image: galleryImages.find((image) => image.includes('eventooim')) ?? assets.heroImage,
        link: 'https://www.instagram.com/p/CVgTakLPD3v/',
        platform: 'Instagram',
        category: 'Noticias',
    },
    {
        title: 'Guerra de DJ',
        date: 'Octubre 2021',
        description: 'Produccion y adecuacion de escenarios para una experiencia en vivo de alto impacto.',
        image: galleryImages.find((image) => image.includes('guerradj')) ?? assets.heroImage,
        link: '#',
        platform: 'Facebook',
        category: 'Noticias',
    },
];

export const socialProfiles = [
    {
        label: 'Instagram',
        href: brand.instagram,
        description: 'Fotos, clips y registros de produccion.',
    },
    {
        label: 'Facebook',
        href: brand.facebook,
        description: 'Cobertura, albumes y noticias del dia a dia.',
    },
];
