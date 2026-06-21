const slugs = {
    home: { es: "", en: "", ch: ""},
    services: {es: "servicios", en:"services", ch: ""},
    contact: { es:"contacto", en:"contact", ch:""},
    about: { es:"nosotros", en:"about", ch:"" },
    privacy: {es: "privacidad", en: "", ch:"" },

};

export function getLangFromUrl(url){
    if(!url?.pathname) return "es";

    const segments = url.pathname.split("/").filter(Boolean);
    const firstSegment = segments[0];

    switch(firstSegment){
        case "en": 
            return "en";
        case "ch": 
            return "ch";
        default: 
            return "es";
    }
}

export function path(lang, pageKey){
    const slug = slugs[pageKey]?.[lang];
    const prefix = lang === "es" ? "" : `/${lang}`;

    if (!slug) return prefix || "/"; 

    return `${prefix}/${slug}`;
}