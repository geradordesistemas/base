$( document ).ready(function() {
    let url = window.location.href

    /** Remove Icone Mosaic da pagina [LIST] do Sonata Admin */

    if( url.includes('list') ){
        let icoMosaic = $('.fa-th-large').parent()
        if(icoMosaic.attr('href').includes('mosaic')){
            icoMosaic.remove()
        }
    }



})